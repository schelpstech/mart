<?php
require_once "./query.php";

function order_post($key, $default = '')
{
    return trim((string)($_POST[$key] ?? $default));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $utility->setFlash("danger", "Error! Restricted Action.");
    header("Location: ../view/index.php");
    exit;
}

$action = $utility->inputDecode($_POST['action'] ?? '');
if ($action !== 'place_order') {
    $utility->setFlash("danger", "Invalid action.");
    header("Location: ../view/checkout.php");
    exit;
}

if (empty($_SESSION['user_id'])) {
    $utility->setFlash("danger", "Please login before checkout.");
    header("Location: ../view/login.php");
    exit;
}

$firstname = order_post('firstname');
$lastname = order_post('lastname');
$email = filter_var(order_post('email'), FILTER_VALIDATE_EMAIL);
$phone = order_post('phone');
$address1 = order_post('address1');
$address2 = order_post('address2');
$city = order_post('city');
$county = order_post('county');
$postcode = strtoupper(order_post('postcode'));
$notes = order_post('order-notes');
$appointmentDate = order_post('appointment_date', '2000-01-01');
$appointmentTime = order_post('appointment_time', '00:00:00');
$fulfilment = strtolower(order_post('fulfilment_type'));
$deliveryZoneId = order_post('delivery_zone_id') !== '' ? (int)order_post('delivery_zone_id') : null;
$couponCode = order_post('coupon_code');

$settings = qs_get_delivery_settings($model);
$summary = $cart->getCartSummary();

if (empty($summary['items'])) {
    $utility->setFlash("danger", "Your cart is empty.");
    header("Location: ../view/viewcart.php");
    exit;
}

$fulfilment = qs_normalize_fulfilment($fulfilment, $settings, !empty($summary['product_items']), !empty($summary['service_items']));

$errors = [];
if (strlen($firstname) < 2) $errors[] = "First name is required.";
if (strlen($lastname) < 2) $errors[] = "Last name is required.";
if (!$email) $errors[] = "A valid email address is required.";
if (!preg_match('/^\+44\s?\d{10}$/', $phone)) $errors[] = "A valid UK phone number is required.";
if (empty($_POST['privacy_consent'])) $errors[] = "You must agree to the Privacy Policy.";

if ($fulfilment === 'delivery') {
    if (empty($settings['delivery_enabled'])) {
        $errors[] = "Delivery is currently unavailable.";
    }
    if (strlen($address1) < 5) $errors[] = "Delivery address line 1 is required.";
    if (strlen($city) < 2) $errors[] = "Delivery city is required.";
    if (!preg_match('/^[A-Z]{1,2}\d[A-Z\d]? ?\d[A-Z]{2}$/i', $postcode)) $errors[] = "A valid UK postcode is required for delivery.";
} else {
    if (empty($settings['pickup_enabled'])) {
        $errors[] = "Pickup is currently unavailable.";
    }
    $address1 = $address2 = $city = $county = $postcode = '';
}

if (!empty($summary['service_items'])) {
    if (empty($appointmentDate) || empty($appointmentTime)) {
        $errors[] = "Appointment date and time are required for services.";
    }
}

$totals = qs_calculate_order_totals($cart, $model, $fulfilment, $deliveryZoneId, $couponCode);
if (!$totals['coupon']['valid']) {
    $errors[] = $totals['coupon']['message'];
}

if (!empty($errors)) {
    $utility->setFlash("danger", implode("<br>", $errors));
    header("Location: ../view/checkout.php");
    exit;
}

$orderReference = $utility->generateRandomString(10);
$coupon = $totals['coupon']['coupon'];
$couponId = $coupon ? (int)$coupon['coupon_id'] : null;
$couponCode = $coupon ? $totals['coupon']['code'] : null;

try {
    $model->beginTransaction();

    $orderData = [
        'user_id' => $_SESSION["user_id"],
        'order_reference' => $orderReference,
        'firstname' => htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8'),
        'lastname' => htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8'),
        'email' => $email,
        'phone' => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'),
        'address1' => htmlspecialchars($address1, ENT_QUOTES, 'UTF-8'),
        'address2' => htmlspecialchars($address2, ENT_QUOTES, 'UTF-8'),
        'city' => htmlspecialchars($city, ENT_QUOTES, 'UTF-8'),
        'county' => htmlspecialchars($county, ENT_QUOTES, 'UTF-8'),
        'postcode' => htmlspecialchars($postcode, ENT_QUOTES, 'UTF-8'),
        'order_notes' => htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'),
        'fulfilment_type' => $totals['fulfilment_type'],
        'delivery_zone_id' => $totals['delivery_zone_id'],
        'delivery_location' => $totals['delivery_location'],
        'appointment_date' => $appointmentDate ?: '2000-01-01',
        'appointment_time' => $appointmentTime ?: '00:00:00',
        'subtotal_amount' => $totals['subtotal'],
        'delivery_fee' => $totals['delivery_fee'],
        'coupon_id' => $couponId,
        'coupon_code' => $couponCode,
        'discount_amount' => $totals['discount'],
        'total_amount' => $totals['total'],
        'payment_status' => 'pending',
        'order_status' => 'pending'
    ];

    $orderId = $model->insert('orders_mart', $orderData);
    if (!$orderId) {
        throw new Exception("Unable to create order.");
    }

    foreach ($totals['product_items'] as $item) {
        $model->insert('order_items_mart', [
            'order_item_id' => $orderId,
            'orderType' => 'product',
            'product_id' => $item['product_id'],
            'product_name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'subtotal' => (float)$item['price'] * (int)$item['quantity']
        ]);
    }

    foreach ($totals['service_items'] as $item) {
        $model->insert('order_items_mart', [
            'order_item_id' => $orderId,
            'orderType' => 'service',
            'product_id' => $item['service_id'],
            'product_name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'subtotal' => (float)$item['price'] * (int)$item['quantity']
        ]);
    }

    $model->commit();
} catch (Exception $e) {
    if (method_exists($model, 'rollBack')) {
        try {
            $model->rollBack();
        } catch (Exception $ignored) {
        }
    }
    $utility->setFlash("danger", "Order failed: " . $e->getMessage());
    header("Location: ../view/checkout.php");
    exit;
}

$_SESSION["orderId"] = $orderId;

if ($totals['total'] <= 0) {
    $model->update('orders_mart', ['payment_status' => 'paid', 'order_status' => 'processing'], ['order_tbl_id' => $orderId]);
    $order = $model->getRows('orders_mart', [
        'where' => ['order_tbl_id' => $orderId],
        'return_type' => 'single'
    ]);
    if ($order) {
        qs_record_coupon_usage($model, $order);
    }
    $cart->clearCart();
    $user->sendOrderConfirmationEmail($orderId);
    $user->notifyAdminOfNewOrder($orderId);
    $user->sendPaymentConfirmationEmail($orderId);
    $utility->setFlash('success', "Order placed successfully.");
    header('Location: ../view/ordersuccess.php');
    exit;
}

$lineItems = [];
if ($totals['discount'] > 0) {
    $lineItems[] = [
        "name" => "Queenzy Stores Order #" . $orderReference,
        "amount" => (int)round($totals['total'] * 100),
        "currency" => "gbp",
        "quantity" => 1,
    ];
} else {
    foreach ($totals['items'] as $item) {
        $lineItems[] = [
            "name" => $item['name'],
            "amount" => (int)round(((float)$item['price']) * 100),
            "currency" => "gbp",
            "quantity" => (int)$item['quantity'],
        ];
    }

    if ($totals['delivery_fee'] > 0) {
        $lineItems[] = [
            "name" => "Delivery Fee",
            "amount" => (int)round($totals['delivery_fee'] * 100),
            "currency" => "gbp",
            "quantity" => 1,
        ];
    }
}

$session = $stripe->createCheckoutSession(
    "https://queenzystores.com/app/paymentHandler.php",
    "https://queenzystores.com/app/paymentHandler.php",
    $lineItems,
    $email
);

if (isset($session["url"])) {
    header("Location: " . $session["url"]);
    exit;
}

$utility->setFlash("danger", "Error creating payment session.");
header("Location: ../view/checkout.php");
exit;
