<?php
include './inc/head.php';

// Check order ID from session
if (!isset($_SESSION['orderId'])) {
    header("Location: ./index.php");
    exit;
}
$orderId = $_SESSION['orderId'];

// Fetch order details
$orderReference = $model->getRows("orders_mart", [
    "where" => ["order_tbl_id" => $orderId],
    "return_type" => "single"
]);

$ordercount = $model->getRows("order_items_mart", [
    "where" => ["order_item_id" => $orderId],
    "return_type" => "count"
]);
$amountPaid = isset($orderReference['total_amount']) ? number_format($orderReference['total_amount'], 2) : "0.00";
?>

<body class="checkout_page">
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <?php
    include './inc/header.php';
    include './inc/cart.php';
    include './inc/category.php';
    ?>

    <!-- Redirect after 30s -->
    <script>
        setTimeout(function() {
            window.location.href = "order.php";
        }, 30000); // 30 seconds
    </script>

    <!-- Ec breadcrumb start -->
    <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Orders</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <!-- ec-breadcrumb-list start -->
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="ec-breadcrumb-item active">Order Success! Thank You</li>
                            </ul>
                            <!-- ec-breadcrumb-list end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <!-- Ec Thank You page -->
    <section class="ec-thank-you-page section-space-p">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="ec-thank-you section-space-p">
                        <!-- thank content Start -->
                        <div class="ec-thank-content text-center">
                            <i class="ecicon eci-check-circle" aria-hidden="true" style="font-size:60px;color:green;"></i>
                            <div class="section-title">
                                <h2 class="ec-title">Thank You!</h2>
                                
                                
                            </div>
                        </div>
                        <!--thank content End -->
                        <div class="ec-hunger">
                            <div class="ec-hunger-detial">
                                <h3>Order Completed Successfully</h3>
                                <h6>Your payment of <strong>£<?php echo $amountPaid; ?></strong> covering
                                    <strong><?php echo $ordercount; ?> item(s)</strong> was successful.</h6>
                                    <p>You’ll be redirected in 30 seconds...</p>
                                <a href="order.php" class="btn btn-primary">My Orders</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include './inc/footer.php'; ?>