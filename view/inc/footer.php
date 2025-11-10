<!-- Footer Start -->
<footer class="ec-footer" style="background-color: #222222; color: #ffffff;">
    <div class="footer-container">
        <div class="footer-top section-space-footer-p">
            <div class="container">
                <div class="row text-white">
                    <!-- Popular Categories -->
                    <div class="col-sm-12 col-lg-4 ec-footer-cat">
                        <div class="ec-footer-widget">
                            <h4 class="ec-footer-heading text-white">Popular Categories</h4>
                            <div class="ec-footer-links">
                                <ul class="align-items-center">
                                    <?php
                                    // Fetch categories under this section
                                    $categories = $model->getRows("categories", [
                                        "where" => ["category_status" => 'Active'],
                                        "limit" => 5,
                                        "order" => ["category_name" => "ASC"]
                                    ]);

                                    if ($categories) {
                                        foreach ($categories as $cat) {
                                    ?>
                                            <li class="ec-footer-link">
                                                <a href="viewcategory.php?id=<?= $cat['categoryTbl_id'] ?>" class="text-white">
                                                    <?= htmlspecialchars($cat['category_name']) ?>
                                                </a>
                                            </li>
                                    <?php
                                        }
                                    } else {
                                        echo "<li><a href='#'>No categories</a></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Service -->
                    <div class="col-sm-12 col-lg-4 ec-footer-info">
                        <div class="ec-footer-widget">
                            <h4 class="ec-footer-heading text-white">Contact Us</h4>
                            <div class="ec-footer-contact">
                                <div class="ec-footer-widget">
                                    <div class="ec-footer-links">
                                        <ul class="align-items-center">
                                            <li class="ec-footer-link ec-foo-location text-white">
                                                <span><i class="fi fi-rr-marker"></i></span>
                                                <p>10 London Street, Larkhall, ML9 1AG</p>
                                            </li>
                                            <li class="ec-footer-link ec-foo-call">
                                                <span><i class="fi-rr-phone-call"></i></span>
                                                <a href="tel:+4401698640067" class="text-white">+44 01698640067</a>
                                            </li>
                                            <li class="ec-footer-link ec-foo-mail">
                                                <span><i class="fi fi-rr-envelope"></i></span>
                                                <a href="mailto:support@queenzystore.com" class="text-white">support@queenzystore.com</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-4 ec-footer-info">
                        <div class="ec-footer-widget">
                            <h4 class="ec-footer-heading text-white">Opening Hours</h4>
                            <div class="ec-footer-contact">
                                <div class="ec-footer-widget">
                                    <div class="ec-footer-links">
                                        <ul class="align-items-center">
                                            <li class="ec-footer-link ec-foo-location text-white">
                                                <span><i class="fi fi-rr-calendar"></i></span>
                                                <p>Mon - Fri: 10am - 8pm</p>
                                            </li>
                                            <li class="ec-footer-link ec-foo-location text-white">
                                                <span><i class="fi fi-rr-calendar"></i></span>
                                                <p>Saturday: 10:30am - 9pm</p>
                                            </li>
                                            <li class="ec-footer-link ec-foo-location text-white">
                                                <span><i class="fi fi-rr-calendar"></i></span>
                                                <p>Sunday: Closed</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Info -->
                    <div class="col-sm-12 col-lg-4 ec-footer-account">
                        <div class="ec-footer-widget">
                             <h4 class="ec-footer-heading text-white">Follow Us</h4>
                            <div class="ec-footer-links">
                                <ul class="align-items-center">
                                    <li class="ec-footer-link"><a href="https://www.instagram.com/queenzylooksuk" target="_blank"><i class="ecicon eci-instagram" aria-hidden="true"></i></a>@queenzylooksuk</li>
                                    <li class="ec-footer-link"><a href="https://www.tiktok.com/@queenzylooks_uk" target="_blank"><i class="ecicon eci-twitter-square" aria-hidden="true"></i></a>@queenzylooks_uk</li>
                                    <li class="ec-footer-link"><a href="https://web.facebook.com/queenzylooks" target="_blank"><i class="ecicon eci-facebook-square" aria-hidden="true"></i></a>@queenzylooks</li>
                                    <li class="ec-footer-link"><a href="https://www.youtube.com/@queenzylooks" target="_blank"><i class="ecicon eci-youtube-square" aria-hidden="true"></i></a>@queenzylooks</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> <!-- /row -->
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom" style="background-color: #1a1a1a; color: #cccccc;">
            <div class="container">
                <div class="row">
                    <!-- Payment Icons -->
                    <div class="footer-bottom-right">
                        <div class="footer-bottom-payment d-flex justify-content-center">
                            <div class="payment-link">
                                <img src="assets/images/icons/payment.png" alt="Payment Methods">
                            </div>
                        </div>
                    </div>

                    <!-- Copyright -->
                    <div class="footer-copy text-center">
                        <div class="footer-bottom-copy">
                            <div class="ec-copy">
                                &copy; <span id="copyright_year"></span> <a class="site-name text-white" href="/">Queenzy Stores</a>.
                                All rights reserved. <br>
                                Registered in England & Wales, Company No. 12345678.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Modal -->
<div class="modal fade" id="ec_quickview_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close qty_close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="row">
                    <!-- Product Images -->
                    <div class="col-md-5 col-sm-12 col-xs-12">
                        <!-- Main Images -->
                        <div class="qty-product-cover">
                            <!-- AJAX will insert images here -->
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-md-7 col-sm-12 col-xs-12">
                        <div class="quickview-pro-content">
                            <!-- Product Title -->
                            <h5 class="ec-quick-title">
                                <a href="#">Product Name</a>
                            </h5>

                            <!-- Product Rating -->
                            <div class="ec-quickview-rating">
                                <i class="ecicon eci-star fill"></i>
                                <i class="ecicon eci-star fill"></i>
                                <i class="ecicon eci-star fill"></i>
                                <i class="ecicon eci-star fill"></i>
                                <i class="ecicon eci-star"></i>
                            </div>

                            <!-- Product Description -->
                            <div class="ec-quickview-desc"></div>

                            <!-- Product Price -->
                            <div class="ec-quickview-price">
                                <span class="new-price"></span>
                                <span class="old-price"></span>
                            </div>
                        </div>
                    </div> <!-- End Product Info -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal end -->

<div id="addtocart_toast" class="toast">
    <span class="desc"></span>
</div>

<!-- Click To Call -->
<div class="ec-cc-style cc-right-bottom">
    <!-- Start Floating Panel Container -->
    <div class="cc-panel">
        <!-- Panel Content -->
        <div class="cc-header">
            <img src="assets/images/whatsapp/profile_01.jpg" alt="profile image" />
            <h2>Queenzy Support</h2>
            <p>Technical Manager</p>
        </div>
        <div class="cc-body">
            <p><b>Hey there &#128515;</b></p>
            <p>Need help ? just give me a call.</p>
        </div>
        <div class="cc-footer">
            <a href="tel:+44 01698640067" class="cc-call-button">
                <span>Call me</span>
                <svg width="13px" height="10px" viewBox="0 0 13 10">
                    <path d="M1,5 L11,5"></path>
                    <polyline points="8 1 12 5 8 9"></polyline>
                </svg>
            </a>
        </div>
    </div>
    <!--/ End Floating Panel Container -->

    <!-- Start Right Floating Button-->
    <div class="cc-button cc-right-bottom">
        <i class="fi-rr-phone-call"></i>
    </div>
    <!--/ End Right Floating Button-->

</div>
<!-- Click To Call end -->

<!-- 
<div id="ec-popnews-bg"></div>
<div id="ec-popnews-box">
    <div id="ec-popnews-close"><i class="ecicon eci-close"></i></div>
    <div class="row">
        <div class="col-md-7 disp-no-767">
            <img src="assets/images/banner/newsletter-9.png" alt="newsletter">
        </div>
        <div class="col-md-5">
            <div id="ec-popnews-box-content">
                <h2>Subscribe Newsletter.</h2>
                <p>Subscribe the ekka ecommerce to get in touch and get the future update. </p>
                <form id="ec-popnews-form" action="#" method="post">
                    <input type="email" name="newsemail" placeholder="Email Address" required />
                    <button type="button" class="btn btn-primary" name="subscribe">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</div> -->

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Close Button -->
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="modal-body text-center p-4">
                <h5 class="mb-3">Confirm Logout</h5>
                <p class="mb-4">Are you sure you want to log out of your account?</p>

                <div class="d-flex justify-content-center gap-3">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <form action="../app/user_access_action.php" method="post">
                        <span class="ec-login-wrap ec-login-btn">
                            <button class="btn btn-danger" name="action" value="<?php echo $utility->inputEncode('logout'); ?>" type="submit">Logout</button>
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal end -->

<!-- Footer navigation panel for responsive display -->
<div class="ec-nav-toolbar">
    <div class="container">
        <div class="ec-nav-panel">
            <div class="ec-nav-panel-icons">
                <a href="#ec-mobile-menu" class="navbar-toggler-btn ec-header-btn ec-side-toggle"><i
                        class="fi fi-rr-menu-burger"></i></a>
            </div>
            <div class="ec-nav-panel-icons">
                <a href="#ec-side-cart" class="toggle-cart ec-header-btn ec-side-toggle"><i
                        class="fi-rr-shopping-basket"></i><span id="cart-count"
                        class="ec-cart-noti ec-header-count cart-count-lable">0</span></a>
            </div>
            <div class="ec-nav-panel-icons">
                <a href="index.php" class="ec-header-btn"><i class="fi-rr-home"></i></a>
            </div>
            <div class="ec-nav-panel-icons dropdown dropup footer-dropup">
                <button class="dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static">
                    <i class="fi-rr-user"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <li>
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                Logout
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="register.php">Register</a></li>
                        <li><a class="dropdown-item" href="login.php">Login</a></li>
                        <li><a class="dropdown-item" href="checkout.php">Checkout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Footer navigation panel for responsive display end -->

<!-- 
<div class="recent-purchase">
    <img src="assets/images/product-image/102_1.jpg" alt="payment image">
    <div class="detail">
        <p>Someone in new just bought</p>
        <h6>Mens Party Wear Shoes</h6>
        <p>5 Minutes ago</p>
    </div>
    <a href="javascript:void(0)" class="icon-btn recent-close">×</a>
</div> -->
<style>
    #addtocart_toast {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #4BB543;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 16px;
        position: fixed;
        z-index: 9999;
        /* 👈 keep it on top */
        left: 50%;
        bottom: 30px;
        font-size: 17px;
        opacity: 0;
        transition: opacity 0.5s, visibility 0.5s;
    }

    #addtocart_toast.show {
        visibility: visible;
        opacity: 1;
    }

    /* Fix dropdown inside footer */
    .footer-dropup {
        position: relative;
        overflow: visible !important;
        z-index: 1050 !important;
    }

    /* Ensure dropdown always shows above footer */
    .footer-dropup .dropdown-menu {
        position: absolute !important;
        bottom: 100% !important;
        top: auto !important;
        margin-bottom: 8px;
        z-index: 9999 !important;

        /* optional animation */
        transition: all 0.2s ease-in-out;
        transform-origin: bottom;
        opacity: 0;
    }

    .footer-dropup .dropdown-menu.show {
        transform: translateY(-5px);
        opacity: 1;
    }
</style>
<!-- Theme Custom Cursors -->
<div class="ec-cursor"></div>
<div class="ec-cursor-2"></div>

<!-- Vendor JS -->
<script src="assets/js/vendor/jquery-3.5.1.min.js"></script>
<script src="assets/js/vendor/popper.min.js"></script>
<script src="assets/js/vendor/bootstrap.min.js"></script>
<script src="assets/js/vendor/jquery-migrate-3.3.0.min.js"></script>
<script src="assets/js/vendor/modernizr-3.11.2.min.js"></script>

<!--Plugins JS-->

<script src="assets/js/plugins/jquery.sticky-sidebar.js"></script>
<script src="assets/js/plugins/swiper-bundle.min.js"></script>
<script src="assets/js/plugins/countdownTimer.min.js"></script>
<script src="assets/js/plugins/nouislider.js"></script>
<script src="assets/js/plugins/scrollup.js"></script>
<script src="assets/js/plugins/jquery.zoom.min.js"></script>
<script src="assets/js/plugins/slick.min.js"></script>
<script src="assets/js/plugins/owl.carousel.min.js"></script>
<script src="assets/js/plugins/infiniteslidev2.js"></script>
<script src="assets/js/plugins/click-to-call.js"></script>

<!-- Main Js -->
<script src="assets/js/vendor/jquery.magnific-popup.min.js"></script>
<script src="assets/js/vendor/index.js"></script>
<script src="assets/js/demo-9.js"></script>
<script src="assets/js/populateQuickView.js"></script>
<script src="assets/js/manageCart.js"></script>
<script src="assets/js/renderCart.js"></script>
<script src="assets/js/register.js"></script>
<script src="assets/js/checkoutFormValidator.js"></script>
<script src="assets/js/fetchproduct.js"></script>
<script src="assets/js/userprofile.js"></script>
<script src="assets/js/forgotpassword.js"></script>
<script src="assets/js/validatepwdresetpage.js"></script>
<script src="assets/js/bookingform.js"></script>
</body>

</html>