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
            <div class="row">
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
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">Log In</h2>
                        <h2 class="ec-title">Log In</h2>
                        <p class="sub-title mb-3">Best place to buy and sell digital products</p>
                    </div>
                </div>
                <div class="ec-login-wrapper">
                    <div class="ec-login-container">
                        <div class="ec-login-form">
                            <?php $utility->displayFlash(); ?>
                            <form action="../app/user_access_action.php" method="post" autocomplete="off">
                                <span class="ec-login-wrap">
                                    <label>Email Address*</label>
                                    <input type="text" name="email" placeholder="Enter your email add..." required />
                                </span>
                                <span class="ec-login-wrap">
                                    <label>Password*</label>
                                    <input type="password" name="password" placeholder="Enter your password" required />
                                </span>
                                <span class="ec-login-wrap ec-login-fp">
                                    <label><a href="./forgotpassword.php">Forgot Password</a></label>
                                </span>
                                <span class="ec-login-wrap ec-login-fp">
                                    <label><a href="./resendverification.php">Resend verification link</a></label>
                                </span>
                                <span class="ec-login-wrap ec-login-btn">
                                    <button class="btn btn-primary" name="action" value="<?php echo $utility->inputEncode('login'); ?>" type="submit">Login</button>
                                    <a href="register.php" class="btn btn-secondary">Register</a>
                                </span>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Ec Login end -->
    <?php include './inc/footer.php'; ?>