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
    $addedServices = 0;

    foreach ($serviceIds as $srvId) {
        $srv = $model->getRows('services', [
            'where' => ['id' => (int)$srvId, 'status' => 'active'],
            'return_type' => 'single'
        ]);
        if (!$srv) continue;

        $cart->addServiceToCart((int)$srvId, 1, (float)$srv['base_price']);
        $addedServices++;
    }

    if ($addedServices === 0) {
        $utility->setFlash('error', "Selected services are no longer available.");
        header("Location: ../view/booking.php");
        exit;
    }

    // Redirect to checkout page
     $utility->setFlash('success', "Booking created successfully. Proceed to payment.");
     $_SESSION['order_type']  = "service";
    header("Location: ../view/checkout.php");
    exit;

} catch (Exception $e) {
     $utility->setFlash('error',  "Booking failed: " . $e->getMessage());
    
    header("Location: ../view/booking.php");
    exit;
}
