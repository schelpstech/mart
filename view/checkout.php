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

    <!-- Ec breadcrumb start -->

    <!-- Ec breadcrumb end -->


    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="row ec_breadcrumb_inner">
                                    <div class="col-md-6 col-sm-12">
                                        <h2 class="ec-breadcrumb-title">Checkout</h2>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <!-- ec-breadcrumb-list start -->
                                        <ul class="ec-breadcrumb-list">
                                            <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                            <li class="ec-breadcrumb-item active">Checkout</li>
                                        </ul>
                                        <!-- ec-breadcrumb-list end -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (empty($_SESSION['user_id'])): ?>
                    <!-- Ec Login on Checkout page -->
                    <div class="ec-checkout col-lg-8 offset-2 col-md-12 ">
                        <?php $utility->displayFlash(); ?>
                        <div class="ec-checkout-content">
                            <div class="ec-checkout-inner">
                                <div class="ec-checkout-wrap margin-bottom-30">
                                    <div class="ec-checkout-block ec-check-new">
                                        <h3 class="ec-checkout-title"> Customer</h3>
                                        <div class="ec-check-block-content">
                                            <div class="ec-check-subtitle">Checkout Options</div>
                                            <div class="ec-new-desc">
                                                By logging into your account you will be able to shop faster,
                                                be up to date on an order's status, and keep track of your previous orders.
                                            </div>
                                            <div class="ec-new-btn">
                                                <a href="login.php" class="btn btn-primary">Login / Register</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Sidebar -->
                    <div class="ec-checkout-leftside col-lg-4 col-md-12">
                        <div class="ec-sidebar-wrap">
                            <!-- Sidebar Summary Block -->
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Summary</h3>
                                </div>
                                <?php
                                $subtotal = 0;
                                $delivery = 5.00; // Default delivery cost
                                $button = '
                                        <div class="ec-bg-swipe">
                                            <button class="ec-btn-bg-swipe">
                                                    <span class="circle" aria-hidden="true">
                                                        <span class="icon arrow"></span>
                                                    </span>
                                                    <span class="button-text">Start Shopping</span>
                                            </button>
                                        </div>
                                '; // default button

                                if (!empty($_SESSION['user_id'])) {
                                    // Initialize
                                    $productItems = $cart->getCartItems();
                                    $serviceItems = $cart->getServiceCartItems();
                                    $subtotal = 0;
                                    $hasProducts = !empty($productItems);
                                    $hasServices = !empty($serviceItems);

                                    // Combine subtotals separately
                                    $productSubtotal = 0;
                                    foreach ($productItems as $item) {
                                        $productSubtotal += $item['line_total'];
                                    }

                                    $serviceSubtotal = 0;
                                    foreach ($serviceItems as $srv) {
                                        $serviceSubtotal += $srv['line_total'];
                                    }

                                    // Overall subtotal
                                    $subtotal = $productSubtotal + $serviceSubtotal;

                                    // Delivery: 0 for services-only, normal for product orders
                                    if ($hasProducts && !$hasServices) {
                                        $delivery = ($subtotal >= 50) ? 0 : 5.00;
                                    } elseif ($hasProducts && $hasServices) {
                                        // Mixed order: apply delivery for product portion only
                                        $delivery = ($productSubtotal >= 50) ? 0 : 5.00;
                                    } else {
                                        // Services only — no delivery
                                        $delivery = 0;
                                    }

                                    // Total
                                    $total = $subtotal + $delivery;

                                    // Checkout button
                                    if ($subtotal > 0) {
                                        $button = '
            <button class="btn btn-lg btn-success btn-jittery" name="action" value="' . $utility->inputEncode('place_order') . '" type="submit">
                Proceed to Payment
            </button>';
                                    } else {
                                        $subtotal = $delivery = $total = 0;
                                        $button = '
            <div class="ec-bg-swipe">
                <button class="ec-btn-bg-swipe">
                    <span class="circle" aria-hidden="true">
                        <span class="icon arrow"></span>
                    </span>
                    <span class="button-text">Start Shopping</span>
                </button>
            </div>';
                                    }
                                }


                                ?>


                                <div class="ec-sb-block-content">
                                    <div class="ec-checkout-summary">
                                        <div>
                                            <span class="text-left">Sub-Total</span>
                                            <span class="text-right">£<?= number_format($subtotal, 2) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-left">Delivery</span>
                                            <span class="text-right">£<?= number_format($delivery, 2) ?></span>
                                        </div>
                                        <div class="ec-checkout-summary-total">
                                            <span class="text-left">Total Amount</span>
                                            <span class="text-right">£<?= number_format($total, 2) ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- Sidebar Summary Block -->
                        </div>
                        <div class="ec-sidebar-wrap ec-checkout-del-wrap">
                            <!-- Sidebar Summary Block -->
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Delivery Method</h3>
                                </div>
                                <div class="ec-sb-block-content">
                                    <div class="ec-checkout-del">
                                        <label><input type="radio" name="delivery" checked /> Standard Delivery (2-4 working days)</label><br>
                                        <label><input type="radio" name="delivery" /> Paid Delivery - Orders below £49.99</label><br>
                                        <label><input type="radio" name="delivery" /> Free Delivery - Orders over £50</label>
                                    </div>

                                    <hr>

                                    <div class="ec-checkout-links" style="margin-top: 10px;">
                                        <p><strong>Delivery & Policies:</strong></p>
                                        <ul style="padding-left: 18px; margin-top: 5px;">
                                            <li><a href="delivery-policy.php"><strong>Delivery Policy</strong></a></li>
                                            <li><a href="return-policy.php"><strong>Return & Refund Policy</strong></a></li>
                                            <li><a href="privacy-policy.php"><strong>Privacy Policy</strong></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar Summary Block -->
                        </div>
                        <div class="ec-sidebar-wrap ec-checkout-pay-wrap">
                            <!-- Sidebar Payment Block -->
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Payment Method</h3>
                                </div>
                                <div class="ec-sb-block-content">
                                    <div class="ec-checkout-pay">
                                        <div class="ec-pay-desc">Please select the preferred payment method to use on this
                                            order.</div>
                                        <form action="#">
                                            <span class="ec-pay-option">
                                                <span>
                                                    <input type="radio" id="pay1" name="radio-group" checked>
                                                    <label for="pay1">Online Payment</label>
                                                </span>
                                            </span>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Sidebar Payment Block -->
                        </div>
                        <div class="ec-sidebar-wrap ec-check-pay-img-wrap">
                            <!-- Sidebar Payment Block -->
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Supported Payment Method</h3>
                                </div>
                                <div class="ec-sb-block-content">
                                    <div class="ec-check-pay-img-inner">
                                        <div class="ec-check-pay-img">
                                            <img src="assets/images/icons/payment1.png" alt="">
                                        </div>
                                        <div class="ec-check-pay-img">
                                            <img src="assets/images/icons/payment2.png" alt="">
                                        </div>
                                        <div class="ec-check-pay-img">
                                            <img src="assets/images/icons/payment3.png" alt="">
                                        </div>
                                        <div class="ec-check-pay-img">
                                            <img src="assets/images/icons/payment4.png" alt="">
                                        </div>
                                        <div class="ec-check-pay-img">
                                            <img src="assets/images/icons/payment5.png" alt="">
                                        </div>
                                        <div class="ec-check-pay-img">
                                            <img src="assets/images/icons/payment6.png" alt="">
                                        </div>
                                        <div class="ec-check-pay-img">
                                            <img src="assets/images/icons/payment7.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Sidebar Payment Block -->
                        </div>
                    </div>
                    <!-- Ec checkout page for Logged in User -->
                    <div class="ec-checkout-rightside col-lg-8 col-md-12">
                        <div class="ec-checkout-content">
                            <div class="ec-checkout-inner">
                                <!-- Billing Details -->
                                <div class="ec-checkout-wrap margin-bottom-30 padding-bottom-3">
                                    <div class="ec-checkout-block ec-check-bill">
                                        <h3 class="ec-checkout-title">Billing & Delivery Details</h3>
                                        <?php
                                        try {
                                            $profile = $user->getUserProfile($_SESSION['user_id']);

                                            // fallback values if no profile row yet
                                            $firstname   = $profile['firstname']   ?? '';
                                            $lastname    = $profile['lastname']    ?? '';
                                            $email       = $profile['email']       ?? '';
                                            $phone       = $profile['phone']       ?? '';
                                            $phone2      = $profile['phone2']      ?? '';
                                            $address1    = $profile['address1']    ?? '';
                                            $address2    = $profile['address2']    ?? '';
                                            $city        = $profile['city']        ?? '';
                                            $county      = $profile['county']      ?? '';
                                            $postcode    = $profile['postcode']    ?? '';
                                        } catch (Exception $e) {
                                            // If DB lookup fails, fall back to empty values
                                            $firstname = $lastname = $email = $phone = $phone2 = '';
                                            $address1 = $address2 = $city = $county = $postcode = '';
                                        }
                                        ?>
                                        <div class="ec-check-bill-form">
                                            <form id="checkoutForm" action="../app/orderHandler.php" method="post">
                                                <span class="ec-bill-wrap ec-bill-half">
                                                    <label>First Name*</label>
                                                    <input type="text" name="firstname" id="firstname" placeholder="John"
                                                        value="<?= htmlspecialchars($firstname) ?>" required tabindex="1" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half">
                                                    <label>Last Name*</label>
                                                    <input type="text" name="lastname" id="lastname" placeholder="Doe"
                                                        value="<?= htmlspecialchars($lastname) ?>" required tabindex="2" />
                                                </span>
                                                <span class="ec-bill-wrap">
                                                    <label>Email Address*</label>
                                                    <input type="email" name="email" id="email" placeholder="you@example.com"
                                                        value="<?= htmlspecialchars($email) ?>" required tabindex="3" />
                                                </span>
                                                <span class="ec-bill-wrap">
                                                    <label>Phone Number*</label>
                                                    <input type="tel" name="phone" id="phone" placeholder="+44 7123 456789"
                                                        value="<?= htmlspecialchars($phone) ?>" required tabindex="4" />
                                                </span>
                                                <span class="ec-bill-wrap">
                                                    <label>Address Line 1*</label>
                                                    <input type="text" name="address1" id="address1" placeholder="123 High Street"
                                                        value="<?= htmlspecialchars($address1) ?>" required tabindex="5" />
                                                </span>
                                                <span class="ec-bill-wrap">
                                                    <label>Address Line 2 (optional)</label>
                                                    <input type="text" name="address2" id="address2"
                                                        placeholder="Apartment, suite, etc. (optional)"
                                                        value="<?= htmlspecialchars($address2) ?>" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half">
                                                    <label>Town / City*</label>
                                                    <input type="text" name="city" id="city" placeholder="London"
                                                        value="<?= htmlspecialchars($city) ?>" required tabindex="6" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half">
                                                    <label>County (optional)</label>
                                                    <input type="text" name="county" id="county" placeholder="Greater London"
                                                        value="<?= htmlspecialchars($county) ?>" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half">
                                                    <label>Postcode*</label>
                                                    <input type="text" name="postcode" id="postcode" placeholder="SW1A 1AA"
                                                        value="<?= htmlspecialchars($postcode) ?>" required tabindex="7" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half">
                                                    <label>Country*</label>
                                                    <input type="text" value="United Kingdom" disabled />
                                                </span>
                                                <?php if ($hasServices): ?>
                                                    <span class="ec-bill-wrap ec-bill-half">
                                                        <label>Appointment Date*</label>
                                                        <input type="date" name="appointment_date" id="appointment_date" required tabindex="8" />
                                                    </span>
                                                    <span class="ec-bill-wrap ec-bill-half">
                                                        <label>Appointment Time*</label>
                                                        <input type="time" name="appointment_time" id="appointment_time" required tabindex="9" />
                                                    </span>
                                                <?php endif; ?>

                                                <span class="ec-bill-wrap">
                                                    <div class="ec-checkout-wrap margin-bottom-30">
                                                        <div class="ec-checkout-block">
                                                            <h3 class="ec-checkout-title">Order Notes (optional)</h3>
                                                            <textarea name="order-notes" id="order-notes"
                                                                placeholder="Notes about your order, e.g. delivery instructions."
                                                                rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                </span>
                                                <div class="ec-bill-wrap">
                                                    <label>
                                                        <input type="checkbox" name="privacy_consent" required tabindex="8" />
                                                        I agree to the processing of my personal data in accordance with the
                                                        <a href="privacy-policy.php" target="_blank"><b>Privacy Policy</b></a>.
                                                    </label>
                                                </div>
                                                <div class="ec-bill-wrap">
                                                    <span class="ec-check-order-btn">
                                                        <?= $button ?>
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                                <!-- Place Order Button -->

                            </div>
                        </div>
                    </div>


                <?php endif; ?>
            </div>
        </div>
    </section>


    <?php include './inc/footer.php'; ?>