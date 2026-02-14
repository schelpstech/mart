<?php
include './query.php';

// Utility Functionsrequire 'StripePayment.php';

if (isset($_GET['session_id']) && isset($_SESSION["orderId"])) {
    $sessionId = $_GET['session_id'];
    $orderId = $_SESSION["orderId"];

    $session = $stripe->retrieveCheckoutSession($sessionId);

    if (isset($session['payment_status']) && $session['payment_status'] === 'paid') {
        // ✅ Update DB: mark order as paid
        $model->update(
            'orders_mart',
            ['payment_status' => 'paid', 'session_ref' => $sessionId],
            ['order_tbl_id' => $orderId]
        );

        //Send confirmation email to user
        $user->sendOrderConfirmationEmail($orderId);

        //send confirmation email to admin
        $user->notifyAdminOfNewOrder($orderId);

        //send payment confirmation email to user
        $user->sendPaymentConfirmationEmail($orderId);

        //Set flash message and redirect to success page
        $utility->setFlash('success', "Payment Successful for Order #{$orderId}");
        header('Location: ../view/ordersuccess.php');
    } elseif (isset($session['payment_status']) && $session['payment_status'] === 'pending') {
        $model->update(
            'orders_mart',
            ['payment_status' => 'pending', 'session_ref' => $sessionId],
            ['order_tbl_id' => $orderId]
        );
                //send payment confirmation email to user
        $user->sendPaymentConfirmationEmail($orderId);
        $utility->setFlash('error', "Payment Failed or Pending for Order #{$orderId}");
        header('Location: ../view/paymentfail.php');
    } else {
        $model->update(
            'orders_mart',
            ['payment_status' => 'failed', 'session_ref' => $sessionId],
            ['order_tbl_id' => $orderId]
        );
                //send payment confirmation email to user
        $user->sendPaymentConfirmationEmail($orderId);
        $utility->setFlash('error', "Payment Failed or Pending for Order #{$orderId}");
        header('Location: ../view/paymentfail.php');
    }
} else {
    $utility->setFlash('error', "Invalid Action");
    header('Location: ../view/index.php');
}
