<?php
require_once "../query.php";
/*
|--------------------------------------------------------------------------
| Handle Order Status Update (Only for Paid Orders)
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_delivery_status'])) {
    $orderId = $_POST['order_id'] ?? null;

    $order = $model->getRows("orders_mart", [
        "where" => ["order_tbl_id" => $orderId]
    ]);
    if (!$order || strtolower($order[0]['payment_status']) !== 'paid') {
        $utility->setFlash("danger", "Only paid orders can be updated.");
        $utility->redirect("../../console/order-details.php?id=" . $orderId);
        exit;
    }

    $deliveryStatus = trim($_POST['delivery_status']);
    $deliveryDate   = !empty($_POST['delivery_date']) ? $_POST['delivery_date'] : null;

    if (empty($deliveryStatus)) {
        $utility->setFlash("danger", "Please select delivery status.");
        $utility->redirect("../../console/order-details.php?id=" . $orderId);
        exit;
    }

    $updateData = [
        "delivery_status" => $deliveryStatus,
        "delivery_date"   => $deliveryDate
    ];

    $updated = $model->update("orders_mart", $updateData, [
        "order_tbl_id" => $orderId
    ]);

    if ($updated) {
        $utility->setFlash("success", "Order delivery status updated successfully.");
    } else {
        $utility->setFlash("danger", "Failed to update order.");
    }

    $utility->redirect("../../console/order-details.php?id=" . $orderId);
    exit;
}
