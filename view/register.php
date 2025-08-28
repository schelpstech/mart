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
    <!-- Ec breadcrumb start -->
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Register</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <!-- ec-breadcrumb-list start -->
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="index.html">Home</a></li>
                                <li class="ec-breadcrumb-item active">Register</li>
                            </ul>
                            <!-- ec-breadcrumb-list end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <!-- Start Register -->
    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">Register</h2>
                        <h2 class="ec-title">Create Your Account</h2>
                        <p class="sub-title mb-3">Join us today and start shopping</p>
                    </div>
                </div>
                
                <div class="ec-register-wrapper">
                    <div class="ec-register-container">
                        <div class="ec-register-form">
                            <?php $utility->displayFlash(); ?>
                            <form action="../app/user_access_action.php" method="post" autocomplete="off" id="registerForm">

                                <!-- Email -->
                                <span class="ec-register-wrap">
                                    <label>Email Address *</label>
                                    <input type="email" name="email" placeholder="Enter your email" required />
                                </span>

                                <!-- Phone -->
                                <span class="ec-register-wrap">
                                    <label>Phone Number *</label>
                                    <input type="tel" name="phone" placeholder="+44 7XXX XXXXXX" required />
                                </span>

                                <!-- Password -->
                                <span class="ec-register-wrap">
                                    <label>Password *</label>
                                    <input type="password" id="password" name="password" placeholder="Create a password" required />
                                    <small id="passwordMsg" class="text-danger d-block mt-1"></small>
                                </span>

                                <!-- Confirm Password -->
                                <span class="ec-register-wrap">
                                    <label>Confirm Password *</label>
                                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required />
                                    <small id="confirmMsg" class="text-danger d-block mt-1"></small>
                                </span>
                                <!-- Human Check -->
                                <span class="ec-register-wrap">
                                    <label>Solve: <span id="num1"></span> + <span id="num2"></span> = ?</label>
                                    <input type="text" id="humanAnswer" placeholder="Enter answer" required />
                                    <small id="captchaMsg" class="text-danger d-block mt-1"></small>
                                </span>

                                <!-- Terms -->
                                <span class="ec-register-wrap">
                                    <label class="ec-register-agree">
                                        <input type="checkbox" id="humanVerified" name="terms" disabled required>
                                        I am not a robot & I agree to the
                                        <a href="terms.php">Terms & Conditions</a> and
                                        <a href="privacy-policy.php">Privacy Policy</a>
                                    </label>
                                </span>


                                <!-- Register Button -->
                                <span class="ec-register-wrap ec-register-btn">
                                    <button class="btn btn-primary" name="action" value="<?php echo $utility->inputEncode('register'); ?>" type="submit">Register</button>
                                </span>

                                <!-- Already Have Account -->
                                <span class="ec-register-wrap text-center mt-3">
                                    Already have an account? <a href="login.php">Sign In</a>
                                </span>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Register -->
    <?php include './inc/footer.php'; ?>