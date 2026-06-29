<?php
include './query.php';

if (!isset($_GET['session_id'], $_SESSION["orderId"])) {
    $utility->setFlash('error', "Invalid Action");
    header('Location: ../view/index.php');
    exit;
}

$sessionId = $_GET['session_id'];
$orderId = (int)$_SESSION["orderId"];
$session = $stripe->retrieveCheckoutSession($sessionId);
$paymentStatus = $session['payment_status'] ?? 'failed';

if ($paymentStatus === 'paid') {
    $model->update(
        'orders_mart',
        ['payment_status' => 'paid', 'session_ref' => $sessionId, 'order_status' => 'processing'],
        ['order_tbl_id' => $orderId]
    );

    $order = $model->getRows('orders_mart', [
        'where' => ['order_tbl_id' => $orderId],
        'return_type' => 'single'
    ]);
    if ($order) {
        qs_record_coupon_usage($model, $order);
    }

    $user->sendOrderConfirmationEmail($orderId);
    $user->notifyAdminOfNewOrder($orderId);
    $user->sendPaymentConfirmationEmail($orderId);
    $cart->clearCart();

    $utility->setFlash('success', "Payment Successful for Order #{$orderId}");
    header('Location: ../view/ordersuccess.php');
    exit;
}

if ($paymentStatus === 'pending') {
    $model->update(
        'orders_mart',
        ['payment_status' => 'pending', 'session_ref' => $sessionId, 'order_status' => 'pending'],
        ['order_tbl_id' => $orderId]
    );
    $user->sendPaymentConfirmationEmail($orderId);
    $utility->setFlash('error', "Payment Pending for Order #{$orderId}");
    header('Location: ../view/paymentfail.php');
    exit;
}

$model->update(
    'orders_mart',
    ['payment_status' => 'failed', 'session_ref' => $sessionId, 'order_status' => 'failed'],
    ['order_tbl_id' => $orderId]
);
$user->sendPaymentConfirmationEmail($orderId);
$utility->setFlash('error', "Payment Failed for Order #{$orderId}");
header('Location: ../view/paymentfail.php');
exit;
