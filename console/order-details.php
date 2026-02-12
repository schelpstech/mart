<?php

$pageTitle = "Order Details";
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $utility->setFlash("Invalid Order ID", "danger");
    $utility->redirect("orders.php");
    exit;
}

$orderId = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| Fetch Order Information
|--------------------------------------------------------------------------
*/

$order = $model->getRows("orders_mart", [
    "where" => ["order_tbl_id" => $orderId]
]);

if (!$order) {
    $utility->setFlash("Order not found", "danger");
    $utility->redirect("orders.php");
    exit;
}

$order = $order[0];

/*
|--------------------------------------------------------------------------
| Fetch Order Items
|--------------------------------------------------------------------------
*/

$orderItems = $model->getRows("order_items_mart", [
    "where" => ["order_item_id" => $orderId]
]);


?>

<div class="ec-content-wrapper">
    <div class="content">

        <div class="breadcrumb-wrapper breadcrumb-wrapper-2">
            <h1>Order #<?= $orderId; ?></h1>
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

            <!-- ORDER SUMMARY -->
            <div class="col-lg-4">
                <div class="card card-default">
                    <div class="card-header">
                        <h4>Customer Information</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= $order['firstname'] . " " . $order['lastname']; ?></p>
                        <hr>
                        <p><strong>Email:</strong> <?= $order['email']; ?></p>
                        <hr>
                        <p><strong>Phone:</strong> <?= $order['phone'] ?? 'N/A'; ?></p>
                        <hr>
                        <p>
                            <strong>Payment Status:</strong>
                            <?php
                            $status = strtolower($order['payment_status'] ?? "pending");
                            $badgeClass = match ($status) {
                                'paid'    => 'success',
                                'failed'  => 'danger',
                                'pending' => 'warning',
                                default   => 'secondary'
                            };
                            ?>
                            <span class="badge badge-<?= $badgeClass ?>">
                                <?= strtoupper($status); ?>
                            </span>
                        </p>
                        <hr>
                        <p><strong>Date:</strong> <?= date('d M Y', strtotime($order['created_at'])); ?></p>

                        <?php if (strtolower($order['payment_status']) === 'paid'): ?>

                            <hr>
                            <h5>Update Delivery Status</h5>

                            <form method="POST" action="../app/admin/orderHandler.php">
                                <div class="form-group">
                                    <label>Delivery Status</label>
                                    <select name="delivery_status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="Payment Received - Pending Delivery"
                                            <?= ($order['delivery_status'] ?? '') === 'Payment Received - Pending Delivery' ? 'selected' : ''; ?>>
                                            Payment Received - Pending Delivery
                                        </option>
                                        <option value="Payment Received - Delivered"
                                            <?= ($order['delivery_status'] ?? '') === 'Payment Received - Delivered' ? 'selected' : ''; ?>>
                                            Payment Received - Delivered
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" hidden readonly ="form-control" value="<?= $order['order_tbl_id'] ?? 'N/A'; ?>" name="order_id">
                                </div>
                                <div class="form-group">
                                    <label>Delivery Date</label>
                                    <input type="date"
                                        name="delivery_date"
                                        class="form-control"
                                        value="<?= $order['delivery_date'] ?? ''; ?>">
                                </div>

                                <button type="submit"
                                    name="update_delivery_status"
                                    class="btn btn-primary btn-block">
                                    Update Status
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- ORDER ITEMS -->
            <div class="col-lg-8">
                <div class="card card-default">
                    <div class="card-header">
                        <h4>Order Items</h4>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $grandTotal = 0;
                                    $count = 1;

                                    if ($orderItems) :
                                        foreach ($orderItems as $item) :
                                            $total = $item['price'] * $item['quantity'];
                                            $grandTotal += $total;
                                    ?>

                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $item['product_name'] ?? 'Product'; ?></td>
                                                <td><?= $item['quantity']; ?></td>
                                                <td>£<?= number_format($item['price'], 2); ?></td>
                                                <td>£<?= number_format($total, 2); ?></td>
                                            </tr>

                                        <?php endforeach;
                                    else: ?>

                                        <tr>
                                            <td colspan="5">No items found for this order.</td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                                        <td><strong>£<?= number_format($grandTotal, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include './inc/footer.php'; ?>