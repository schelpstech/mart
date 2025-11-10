<?php
include "./query.php"; // load session + db + model

// Ensure it’s a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
     $utility->setFlash('error', "Invalid Action");
    header("Location: ../view/booking.php");
    exit;
}

$serviceIds = $_POST['service_ids'] ?? [];

if (empty($serviceIds)) {
     $utility->setFlash('error', "Please complete all required fields and select at least one service.");
    header("Location: ../view/booking.php");
    exit;
}

try {
    $cartid =  $cart->getCartId();
    // Store selected services
    foreach ($serviceIds as $srvId) {
        $srv = $model->getById('services', $srvId);
        if (!$srv) continue;

        $model->insert('service_cart_items', [
            'cart_id' => $cartid,
            'service_id'=> $srvId,
            'price'=> $srv['base_price']
        ]);
    }

    // Redirect to checkout page
     $utility->setFlash('success', "Booking created successfully. Proceed to payment.");
     $_SESSION['order_type']  = "service";
    header("Location: ../view/checkout.php");
    exit;

} catch (Exception $e) {
    $model->rollBack();
     $utility->setFlash('error',  "Booking failed: " . $e->getMessage());
    
    header("Location: ../view/booking.php");
    exit;
}
