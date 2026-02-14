<?php
require_once "./query.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $utility->inputDecode($_POST['action']);

    switch ($action) {
        case 'place_order':
            // Capture form inputs
            $firstname = $_POST['firstname'];
            $lastname  = $_POST['lastname'];
            $email     = $_POST['email'];
            $phone     = $_POST['phone'];
            $address1  = $_POST['address1'];
            $address2  = $_POST['address2'];
            $city      = $_POST['city'];
            $county    = $_POST['county'];
            $postcode  = $_POST['postcode'];
            $notes     = $_POST['order-notes'];
            $appointment_date     = $_POST['appointment_date'] ?? "2000-01-01update";
            $appointment_time     = $_POST['appointment_time'] ?? "00:00:00";

            // Fetch both product and service cart items
            $productCartItems = $cart->getCartItems();
            $serviceCartItems = $cart->getServiceCartItems();

            // Merge both if they exist
            if (!empty($productCartItems) && !empty($serviceCartItems)) {
                $cartItems = array_merge($productCartItems, $serviceCartItems);
            } elseif (!empty($productCartItems)) {
                $cartItems = $productCartItems;
            } elseif (!empty($serviceCartItems)) {
                $cartItems = $serviceCartItems;
            } else {
                die("Cart is empty!");
            }

            $orderReference = $utility->generateRandomString(10);

            // Calculate total (in GBP → Pence)
            $total = 0;
            $lineItems = [];

            foreach ($cartItems as $item) {
                // Fallback for missing quantity or name
                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                $price    = isset($item['price']) ? (float)$item['price'] : 0;
                $name     = isset($item['name']) ? $item['name'] : ($item['service_name'] ?? 'Service');

                $subtotal = $price * $quantity;
                $total   += $subtotal;

                // Add to Stripe Checkout line items
                $lineItems[] = [
                    "name"     => $name,
                    "amount"   => intval($price * 100), // Convert to smallest unit (pence)
                    "currency" => "gbp", // change to "ngn" if processing in Naira
                    "quantity" => $quantity,
                ];
            }

            $deliveryFee = 0;

            if ($total < 100) {
                $deliveryFee = 10;
                $calculatedTotal  = $total + 10; // Add £10 delivery fee for orders under £20
            }else {
                $calculatedTotal = $total; // No delivery fee for orders £20 and above
            }

            // Add delivery as line item if applicable
            if ($deliveryFee > 0) {

                $lineItems[] = [
                    "name"     => "Delivery Fee",
                    "amount"   => intval($deliveryFee * 100),
                    "currency" => "gbp",
                    "quantity" => 1,
                ];
            }

            // Insert order into DB (status pending until webhook confirms)
            $orderData = [
                'user_id'      => $_SESSION["user_id"],
                'order_reference' => $orderReference,
                'firstname'    => $firstname,
                'lastname'     => $lastname,
                'email'        => $email,
                'phone'        => $phone,
                'address1'     => $address1,
                'address2'     => $address2,
                'city'         => $city,
                'county'       => $county,
                'postcode'     => $postcode,
                'order_notes'  => $notes,
                'total_amount' => $calculatedTotal,
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time,
                'payment_status'       => 'pending'
            ];
            $orderId = $model->insert('orders_mart', $orderData);

            // Insert order items

            $productCartItems = $cart->getCartItems();
            $serviceCartItems = $cart->getServiceCartItems();

            // Merge both if they exist
            if (!empty($productCartItems)) {
                $cartItems = $productCartItems;
                foreach ($cartItems as $item) {
                    $model->insert('order_items_mart', [
                        'order_item_id' => $orderId,
                        'orderType' => "product",
                        'product_id'    => $item['product_id'],
                        'product_name'  => $item['name'],
                        'price'         => $item['price'],
                        'quantity'      => $item['quantity'],
                        'subtotal'      => $item['price'] * $item['quantity']
                    ]);
                }
            }
            if (!empty($serviceCartItems)) {
                $cartItems = $serviceCartItems;
                foreach ($cartItems as $item) {
                    $model->insert('order_items_mart', [
                        'order_item_id' => $orderId,
                        'orderType' => "service",
                        'product_id'    => $item['service_id'],
                        'product_name'  => $item['name'],
                        'price'         => $item['price'],
                        'quantity'      => $item['quantity'],
                        'subtotal'      => $item['price'] * $item['quantity']
                    ]);
                }
            }
            // Clear cart
            $cart->clearCart();
            $_SESSION["orderId"] = $orderId;

            // Initiate Stripe Checkout
            $session = $stripe->createCheckoutSession(
                "https://queenzystores.com/app/paymentHandler.php",
                "https://queenzystores.com/app/paymentHandler.php",
                $lineItems,
                $email
            );

            if (isset($session["url"])) {
                header("Location: " . $session["url"]);
                exit;
            } else {
                echo "Error creating Stripe Checkout session: " . print_r($session, true);
                exit;
            }

        default:
            $utility->setFlash("danger", "Invalid action.");
            header("Location: ../view/checkout.php");
            exit;
    }
} else {
    $utility->setFlash("danger", "Error! Restricted Action.");
    header("Location: ../view/index.php");
    exit;
}
