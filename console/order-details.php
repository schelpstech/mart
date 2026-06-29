<?php
$pageTitle = "Order Details";
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';

if (empty($_GET['id'])) {
    $utility->setFlash("danger", "Invalid Order ID");
    $utility->redirect("orders.php");
    exit;
}

$orderId = (int)$_GET['id'];
$order = $model->getRows("orders_mart", [
    "where" => ["order_tbl_id" => $orderId],
    "return_type" => "single"
]);

if (!$order) {
    $utility->setFlash("danger", "Order not found");
    $utility->redirect("orders.php");
    exit;
}

$orderItems = $model->getRows("order_items_mart", [
    "where" => ["order_item_id" => $orderId]
]);
$deliverySettings = qs_get_delivery_settings($model);
$itemSubtotal = 0;
foreach ($orderItems as $item) {
    $itemSubtotal += (float)$item['subtotal'];
}
$financials = qs_order_financials($order, $itemSubtotal);
$fulfilment = $order['fulfilment_type'] ?? 'delivery';
$paymentStatus = strtolower($order['payment_status'] ?? 'pending');
$paymentBadge = match ($paymentStatus) {
    'paid' => 'success',
    'failed' => 'danger',
    'pending' => 'warning',
    default => 'secondary'
};
?>

<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2">
            <h1>Order #<?= htmlspecialchars($order['order_reference'] ?? $orderId); ?></h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs">
                <span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>
                <span><a href="orders.php">Orders</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>
                Order Details
            </p>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card card-default">
                    <div class="card-header">
                        <h4>Customer & Fulfilment</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars(($order['firstname'] ?? '') . " " . ($order['lastname'] ?? '')); ?></p>
                        <hr>
                        <p><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
                        <hr>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
                        <hr>
                        <p><strong>Fulfilment:</strong> <?= qs_fulfilment_label($fulfilment); ?></p>
                        <?php if ($fulfilment === 'pickup'): ?>
                            <p><strong>Pickup Address:</strong><br><?= htmlspecialchars($deliverySettings['pickup_address']); ?></p>
                        <?php else: ?>
                            <p><strong>Delivery Address:</strong><br>
                                <?= htmlspecialchars($order['address1'] ?? ''); ?><br>
                                <?= !empty($order['address2']) ? htmlspecialchars($order['address2']) . '<br>' : ''; ?>
                                <?= htmlspecialchars(trim(($order['city'] ?? '') . ' ' . ($order['county'] ?? ''))); ?><br>
                                <?= htmlspecialchars(trim(($order['postcode'] ?? '') . ' ' . ($order['country'] ?? 'UK'))); ?>
                            </p>
                            <?php if (!empty($order['delivery_location'])): ?>
                                <p><strong>Delivery Zone:</strong> <?= htmlspecialchars($order['delivery_location']); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (!empty($order['appointment_date']) && $order['appointment_date'] !== '0000-00-00' && $order['appointment_date'] !== '2000-01-01'): ?>
                            <hr>
                            <p><strong>Appointment:</strong> <?= htmlspecialchars($order['appointment_date']); ?> <?= htmlspecialchars($order['appointment_time'] ?? ''); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($order['order_notes'])): ?>
                            <hr>
                            <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($order['order_notes'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card card-default">
                    <div class="card-header">
                        <h4>Status</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Payment:</strong>
                            <span class="badge badge-<?= $paymentBadge ?>"><?= strtoupper($paymentStatus); ?></span>
                        </p>
                        <p><strong>Order Status:</strong> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['order_status'] ?? 'pending'))); ?></p>
                        <p><strong>Date Created:</strong> <?= date('d M Y H:i', strtotime($order['created_at'])); ?></p>

                        <hr>
                        <form method="POST" action="../app/admin/orderHandler.php">
                            <input type="hidden" name="order_id" value="<?= (int)$order['order_tbl_id']; ?>">

                            <div class="form-group">
                                <label>Order Status</label>
                                <select name="order_status" class="form-control" required>
                                    <?php
                                    $statuses = ['pending', 'processing', 'ready_for_pickup', 'dispatched', 'delivered', 'completed', 'cancelled'];
                                    foreach ($statuses as $status):
                                    ?>
                                        <option value="<?= $status; ?>" <?= ($order['order_status'] ?? 'pending') === $status ? 'selected' : ''; ?>>
                                            <?= ucwords(str_replace('_', ' ', $status)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $fulfilment === 'pickup' ? 'Pickup Status' : 'Delivery Status'; ?></label>
                                <select name="delivery_status" class="form-control">
                                    <option value="">Select Status</option>
                                    <?php
                                    $fulfilmentStatuses = $fulfilment === 'pickup'
                                        ? ['Payment Received - Awaiting Pickup', 'Ready for Pickup', 'Picked Up']
                                        : ['Payment Received - Pending Delivery', 'Dispatched', 'Payment Received - Delivered'];
                                    foreach ($fulfilmentStatuses as $status):
                                    ?>
                                        <option value="<?= htmlspecialchars($status); ?>" <?= ($order['delivery_status'] ?? '') === $status ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($status); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $fulfilment === 'pickup' ? 'Pickup Date' : 'Delivery Date'; ?></label>
                                <input type="date" name="delivery_date" class="form-control" value="<?= htmlspecialchars($order['delivery_date'] ?? ''); ?>">
                            </div>

                            <button type="submit" name="update_order_status" class="btn btn-primary btn-block">
                                Update Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-default">
                    <div class="card-header">
                        <h4>Items Ordered</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($orderItems)): ?>
                                        <?php $count = 1; ?>
                                        <?php foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= htmlspecialchars($item['product_name'] ?? 'Item'); ?></td>
                                                <td><?= htmlspecialchars(ucfirst($item['orderType'] ?? 'product')); ?></td>
                                                <td><?= (int)$item['quantity']; ?></td>
                                                <td><?= qs_money($item['price']); ?></td>
                                                <td><?= qs_money($item['subtotal']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">No items found for this order.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card card-default">
                    <div class="card-header">
                        <h4>Order Totals</h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Subtotal</th>
                                    <td class="text-right"><?= qs_money($financials['subtotal']); ?></td>
                                </tr>
                                <tr>
                                    <th>Delivery Fee</th>
                                    <td class="text-right"><?= qs_money($financials['delivery_fee']); ?></td>
                                </tr>
                                <tr>
                                    <th>Coupon</th>
                                    <td class="text-right"><?= !empty($order['coupon_code']) ? htmlspecialchars($order['coupon_code']) : 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <th>Discount</th>
                                    <td class="text-right">-<?= qs_money($financials['discount']); ?></td>
                                </tr>
                                <tr>
                                    <th>Final Total</th>
                                    <td class="text-right"><strong><?= qs_money($financials['total']); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './inc/footer.php'; ?>
