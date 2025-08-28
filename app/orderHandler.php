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

            // Get cart data from session
            $cartItems = $cart->getCartItems();
            if (empty($cartItems)) {
                die("Cart is empty!");
            }
            $orderReference = $utility->generateRandomString(10);
            // Calculate total (in Naira -> Kobo or GBP -> Pence, depending on your config)
            $total = 0;
            $lineItems = [];
            foreach ($cartItems as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total   += $subtotal;

                // Add to Stripe Checkout line items
                $lineItems[] = [
                    "name"     => $item['name'],
                    "amount"   => intval($item['price'] * 100), // Convert to smallest unit
                    "currency" => "gbp", // change to "ngn" if in Naira
                    "quantity" => $item['quantity'],
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
                'total_amount' => $total,
                'payment_status'       => 'pending'
            ];
            $orderId = $model->insert('orders_mart', $orderData);

            // Insert order items
            foreach ($cartItems as $item) {
                $model->insert('order_items_mart', [
                    'order_item_id' => $orderId,
                    'product_id'    => $item['product_id'],
                    'product_name'  => $item['name'],
                    'price'         => $item['price'],
                    'quantity'      => $item['quantity'],
                    'subtotal'      => $item['price'] * $item['quantity']
                ]);
            }

            // Clear cart
            $cart->clearCart();
            $_SESSION["orderId"] = $orderId;

            // Initiate Stripe Checkout
            $session = $stripe->createCheckoutSession(
                "http://localhost/mart/app/paymentHandler.php",
                "http://localhost/mart/app/paymentHandler.php",
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
