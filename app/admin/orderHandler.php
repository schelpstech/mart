<?php
require_once "../query.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_order_status'])) {
    $utility->setFlash("danger", "Invalid order action.");
    $utility->redirect("../../console/orders.php");
    exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
$order = $model->getRows("orders_mart", [
    "where" => ["order_tbl_id" => $orderId],
    "return_type" => "single"
]);

if (!$order) {
    $utility->setFlash("danger", "Order not found.");
    $utility->redirect("../../console/orders.php");
    exit;
}

$allowedOrderStatuses = ['pending', 'processing', 'ready_for_pickup', 'dispatched', 'delivered', 'completed', 'cancelled'];
$orderStatus = strtolower(trim($_POST['order_status'] ?? 'pending'));
if (!in_array($orderStatus, $allowedOrderStatuses, true)) {
    $utility->setFlash("danger", "Invalid order status.");
    $utility->redirect("../../console/order-details.php?id=" . $orderId);
    exit;
}

$deliveryStatus = trim($_POST['delivery_status'] ?? '');
$deliveryDate = !empty($_POST['delivery_date']) ? $_POST['delivery_date'] : null;

$updateData = [
    "order_status" => $orderStatus,
    "delivery_status" => $deliveryStatus ?: null,
    "delivery_date" => $deliveryDate
];

$updated = $model->update("orders_mart", $updateData, [
    "order_tbl_id" => $orderId
]);

if ($updated) {
    $utility->setFlash("success", "Order status updated successfully.");
} else {
    $utility->setFlash("danger", "Failed to update order.");
}

$utility->redirect("../../console/order-details.php?id=" . $orderId);
exit;
