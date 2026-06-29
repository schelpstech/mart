<?php
$pageTitle = "Order Summary"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';
?>


<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Orders - Queenzy Stores</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Order List
            </p>
        </div>
        <!-- Top Statistics -->

        <?php

        // Get dashboard stats
        $totalOrders = $model->countRows("orders_mart");
        $totalpaidOrders = $model->countRows("orders_mart", "payment_status = 'paid'");
        $totalpendingOrders = $model->countRows("orders_mart", "payment_status = 'pending'");
        $totalRevenueData = $model->getRows("orders_mart", [
            "select" => "SUM(total_amount) AS total_revenue",
            "where"  => ["payment_status" => "paid"],
            "return_type" => "single"
        ]);
        $totalRevenue = $totalRevenueData['total_revenue'] ?? 0;
        ?>
        <div class="row">
            <div class="col-xl-3 col-sm-6 p-b-15 lbl-card">
                <div class="card card-mini dash-card card-3">
                    <div class="card-body">
                        <h2 class="mb-1"><?= number_format($totalOrders) ?></h2>
                        <p>Total Orders</p>
                        <span class="mdi mdi-package-variant"></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 p-b-15 lbl-card">
                <div class="card card-mini dash-card card-2">
                    <div class="card-body">
                        <h2 class="mb-1"><?= number_format($totalpendingOrders) ?></h2>
                        <p>Orders Pending Payment</p>
                        <span class="mdi mdi-account-clock"></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 p-b-15 lbl-card">
                <div class="card card-mini dash-card card-3">
                    <div class="card-body">
                        <h2 class="mb-1"><?= number_format($totalpaidOrders) ?></h2>
                        <p>Paid Orders</p>
                        <span class="mdi mdi-package-variant"></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 p-b-15 lbl-card">
                <div class="card card-mini dash-card card-4">
                    <div class="card-body">
                        <h2 class="mb-1">£<?= number_format($totalRevenue, 2) ?></h2>
                        <p>Total Revenue</p>
                        <span class="mdi mdi-currency-gbp"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 p-b-15">
                <!-- Recent Order Table -->
                <div class="card card-table-border-none card-default recent-orders" id="recent-orders">
                    <div class="card-header justify-content-between">
                        <h2>Recent Orders</h2>
                        <div class="date-range-report">
                            <span></span>
                        </div>
                    </div>
                    <div class="card-body pt-0 pb-5">
                        <table class="table card-table table-responsive table-responsive-large"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product Name</th>
                                    <th class="d-none d-lg-table-cell">Units</th>
                                    <th class="d-none d-lg-table-cell">Order Date</th>
                                    <th class="d-none d-lg-table-cell">Order Cost</th>
                                    <th class="d-none d-lg-table-cell">Fulfilment</th>
                                    <th class="d-none d-lg-table-cell">Order Status</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch last 10 orders
                                $orders = $model->getRows("orders_mart", [
                                    "limit" => "10"
                                ]);
                                ?>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?= strtoupper($order['order_reference'] ?? "N/A"); ?></td>
                                            <td>
                                                <a class="text-dark" href="#">
                                                    <?= strtoupper(($order['firstname'] ?? '') . " " . ($order['lastname'] ?? 'N/A')); ?>
                                                </a>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <?php
                                                $countItem = $model->getRows("order_items_mart", [
                                                    "where" => ["order_item_id" => $order['order_tbl_id']],
                                                    "return_type" => "count"
                                                ]);
                                                echo $countItem . " Unit(s)";
                                                ?>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <?= date("M d, Y", strtotime($order['created_at'] ?? "now")); ?>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <?= qs_money($order['total_amount'] ?? 0); ?>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <?= qs_fulfilment_label($order['fulfilment_type'] ?? 'delivery'); ?>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <?= ucwords(str_replace('_', ' ', $order['order_status'] ?? 'pending')); ?>
                                            </td>
                                            <td>
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
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown show d-inline-block widget-dropdown">
                                                    <a class="dropdown-toggle icon-burger-mini" href="#"
                                                        role="button" id="dropdown-recent-order<?= $order['order_tbl_id']; ?>"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false" data-display="static"></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        <li class="dropdown-item">
                                                            <a href="order-details.php?id=<?= $order['order_tbl_id']; ?>">View</a>
                                                        </li>
                                                        
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">You have no orders yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

        </div>
    </div> <!-- End Content -->
</div>
<!-- End Content Wrapper -->

<?php
include './inc/footer.php';
?>
