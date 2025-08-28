<?php
include './inc/head.php';
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


    <!-- Breadcrumb -->
    <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-2">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Payment</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="ec-breadcrumb-item active">Payment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Content -->
    <!-- Ec Payment Fail page -->
    <section class="ec-payment-fail-page section-space-p">
        <div class="container">
            <div class="ec-payment-fail">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">
                        <!-- Payment Fail Start -->
                        <div class="ec-payment-content text-center">
                            <i class="ecicon eci-times-circle text-danger" style="font-size:60px;" aria-hidden="true"></i>
                            <div class="section-title mt-3">
                                <h2 class="ec-title text-danger">Payment Failed</h2>
                                <p class="sub-title">
                                    Unfortunately, your payment for Order with Reference Num:: <b>#<?= strtoupper($orderReference['order_reference']) ?></b> could not be completed.
                                    Please check your payment details or try again with another method.
                                    Your order has been saved but marked as <strong>Payment Failed</strong>.
                                </p>
                            </div>
                            <ul class="text-left mt-4">
                                <li><i class="ecicon eci-check-circle-o text-success"></i> Items Added to Cart Successfully</li>
                                <li><i class="ecicon eci-check-circle-o text-success"></i> Order Created Successfully</li>
                                <li><i class="ecicon eci-check-circle-o text-success"></i> Payment Session Started</li>
                                <li><i class="ecicon eci-times-circle-o text-danger"></i> Payment Failed</li>
                                <li><i class="ecicon eci-times-circle-o text-danger"></i> Order Status Updated as Failed</li>
                            </ul>
                            <div class="mt-4">
                                <a href="orders.php" class="btn btn-primary">View Order History</a>
                            </div>
                        </div>
                        <!-- Payment Fail End -->
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- End User history section -->
    <?php include './inc/footer.php'; ?>