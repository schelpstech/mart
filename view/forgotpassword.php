<?php
include './inc/head.php';
?>

<body class="register_page">
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




    <!-- Ec login page -->
    <section class="ec-page-content section-space-p">
        <div class="container">
            <!-- Ec breadcrumb start -->
            <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="row ec_breadcrumb_inner">
                                <div class="col-md-6 col-sm-12">
                                    <h2 class="ec-breadcrumb-title">Login</h2>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <!-- ec-breadcrumb-list start -->
                                    <ul class="ec-breadcrumb-list">
                                        <li class="ec-breadcrumb-item"><a href="index.html">Home</a></li>
                                        <li class="ec-breadcrumb-item active">Login</li>
                                    </ul>
                                    <!-- ec-breadcrumb-list end -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Ec breadcrumb end -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">Reset Password </h2>
                        <h2 class="ec-title">Reset Password</h2>
                        <p class="sub-title mb-3">Get email verification link</p>
                    </div>
                </div>
                <div class="ec-login-wrapper">
                    <div class="ec-login-container">
                        <div class="ec-login-form">
                            <?php $utility->displayFlash(); ?>
                            <form id="forgotPasswordForm" autocomplete="off">
                                <span class="ec-login-wrap">
                                    <label>Email Address*</label>
                                    <input type="text" id="email" name="email"placeholder="Enter your email add..." required="yes" />
                                </span>
                                <span class="ec-login-wrap ec-login-btn">
                                    <button class="btn btn-primary" type="submit">Send Reset Link</button>
                                </span>
                            </form>
                        </div>
                        <div id="forgotMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Ec Login end -->
    <?php include './inc/footer.php'; ?>