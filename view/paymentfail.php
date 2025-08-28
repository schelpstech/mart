<?php
include './inc/head.php';
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
                    <div class="col-lg-6offset-3 col-md-6 offset-3">
                        <!-- Payment Fail Start -->
                        <div class="ec-payment-content">
                            <div class="section-title">
                                <h2 class="ec-title">Payment Error</h2>
                                <p class="sub-title">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                            </div>
                            <ul>
                                <li><i class="ecicon eci-check-circle-o"></i>Items Added to Cart Successfully</li>
                                <li><i class="ecicon eci-check-circle-o"></i>Order Created Successfully</li>
                                <li><i class="ecicon eci-check-circle-o"></i>Payment Session created Successfully</li>
                                <li><i class="ecicon eci-times-circle-o"></i>Payment Failed</li>
                                <li><i class="ecicon eci-times-circle-o"></i>Order Status Set as Failed</li>
                            </ul>
                            <a href="order.php" class="btn btn-primary">View Order history</a>
                        </div>
                        <!-- Payment Fail End -->
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- End User history section -->
    <?php include './inc/footer.php'; ?>