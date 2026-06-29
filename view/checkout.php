<?php
include './inc/head.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$summary = ['items' => [], 'product_items' => [], 'service_items' => [], 'subtotal' => 0, 'count' => 0];
$settings = qs_get_delivery_settings($model);
$zones = qs_get_delivery_zones($model);
$hasProducts = false;
$hasServices = false;
$requiresFulfilmentChoice = false;
$selectedFulfilment = '';
$totals = [
    'subtotal' => 0,
    'delivery_fee' => 0,
    'discount' => 0,
    'total' => 0,
    'fulfilment_type' => ''
];

if ($isLoggedIn) {
    $summary = $cart->getCartSummary();
    $hasProducts = !empty($summary['product_items']);
    $hasServices = !empty($summary['service_items']);
    $requiresFulfilmentChoice = $hasProducts;
    $selectedFulfilment = $requiresFulfilmentChoice ? '' : qs_normalize_fulfilment('', $settings, $hasProducts, $hasServices);
    $totalsFulfilment = $selectedFulfilment !== '' ? $selectedFulfilment : 'pickup';
    $totals = qs_calculate_order_totals($cart, $model, $totalsFulfilment);
}
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

    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="row ec_breadcrumb_inner">
                                    <div class="col-md-6 col-sm-12">
                                        <h2 class="ec-breadcrumb-title">Checkout</h2>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <ul class="ec-breadcrumb-list">
                                            <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                            <li class="ec-breadcrumb-item active">Checkout</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!$isLoggedIn): ?>
                    <div class="ec-checkout col-lg-8 offset-2 col-md-12">
                        <?php $utility->displayFlash(); ?>
                        <div class="ec-checkout-content">
                            <div class="ec-checkout-inner">
                                <div class="ec-checkout-wrap margin-bottom-30">
                                    <div class="ec-checkout-block ec-check-new">
                                        <h3 class="ec-checkout-title">Customer</h3>
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
                    <div class="ec-checkout-leftside col-lg-4 col-md-12">
                        <div class="ec-sidebar-wrap">
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Summary</h3>
                                </div>
                                <div class="ec-sb-block-content">
                                    <div class="ec-checkout-summary">
                                        <div>
                                            <span class="text-left">Sub-Total</span>
                                            <span class="text-right" id="checkout-subtotal"><?= qs_money($totals['subtotal']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-left">Delivery / Pickup</span>
                                            <span class="text-right" id="checkout-delivery"><?= qs_money($totals['delivery_fee']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-left">Discount</span>
                                            <span class="text-right" id="checkout-discount"><?= qs_money($totals['discount']) ?></span>
                                        </div>
                                        <div class="ec-checkout-summary-total">
                                            <span class="text-left">Total Amount</span>
                                            <span class="text-right" id="checkout-total"><?= qs_money($totals['total']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ec-sidebar-wrap ec-checkout-items-wrap">
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Cart Items</h3>
                                </div>
                                <div class="ec-sb-block-content">
                                    <?php if (!empty($summary['items'])): ?>
                                        <div class="checkout-cart-items">
                                            <?php foreach ($summary['items'] as $item): ?>
                                                <?php
                                                $itemType = ucfirst($item['item_type'] ?? 'product');
                                                $itemQuantity = (int)($item['quantity'] ?? 1);
                                                $lineTotal = (float)($item['price'] ?? 0) * $itemQuantity;
                                                ?>
                                                <div class="checkout-cart-item">
                                                    <a href="<?= htmlspecialchars($item['url'] ?? 'viewcart.php'); ?>" class="checkout-cart-item-image">
                                                        <img src="<?= htmlspecialchars($item['image'] ?? 'assets/images/product/main/default.png'); ?>"
                                                            alt="<?= htmlspecialchars($item['name'] ?? 'Item'); ?>">
                                                    </a>
                                                    <div class="checkout-cart-item-main">
                                                        <a href="<?= htmlspecialchars($item['url'] ?? 'viewcart.php'); ?>" class="checkout-cart-item-title">
                                                            <?= htmlspecialchars($item['name'] ?? 'Item'); ?>
                                                        </a>
                                                        <small><?= $itemType; ?> &times; <?= $itemQuantity; ?></small>
                                                    </div>
                                                    <strong><?= qs_money($lineTotal); ?></strong>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <p class="checkout-cart-edit-note">Need to remove or update an item? Please go back to your cart before payment.</p>
                                        <a href="viewcart.php" class="btn btn-primary btn-sm checkout-edit-cart-btn">Edit Cart</a>
                                    <?php else: ?>
                                        <p>Your cart is empty.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="ec-sidebar-wrap ec-checkout-del-wrap">
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title"><?= $requiresFulfilmentChoice ? 'Delivery / Pickup' : 'Service Booking'; ?></h3>
                                </div>
                                <div class="ec-sb-block-content">
                                    <?php if ($requiresFulfilmentChoice): ?>
                                        <div class="fulfilment-choice-alert" id="fulfilment-choice-announcement" aria-live="polite">
                                            <strong>Required before payment</strong>
                                            <span id="fulfilment-selected-label">Choose delivery or pickup for the product items in your cart.</span>
                                        </div>
                                        <div class="ec-checkout-del fulfilment-options" role="radiogroup" aria-describedby="fulfilment-choice-announcement fulfilment-error">
                                            <?php if (!empty($settings['delivery_enabled'])): ?>
                                                <label class="fulfilment-option-card">
                                                    <input type="radio" name="fulfilment_type" value="delivery" form="checkoutForm"
                                                        class="fulfilment-radio" required>
                                                    <span class="fulfilment-option-indicator" aria-hidden="true"></span>
                                                    <span class="fulfilment-option-copy">
                                                        <strong>Delivery</strong>
                                                        <small>Send product items to my delivery address. Delivery fee is calculated below.</small>
                                                    </span>
                                                </label>
                                            <?php endif; ?>
                                            <?php if (!empty($settings['pickup_enabled'])): ?>
                                                <label class="fulfilment-option-card">
                                                    <input type="radio" name="fulfilment_type" value="pickup" form="checkoutForm"
                                                        class="fulfilment-radio" required>
                                                    <span class="fulfilment-option-indicator" aria-hidden="true"></span>
                                                    <span class="fulfilment-option-copy">
                                                        <strong>Pickup</strong>
                                                        <small>Collect from our pickup address. No delivery fee will be charged.</small>
                                                    </span>
                                                </label>
                                            <?php endif; ?>
                                        </div>
                                        <small id="fulfilment-error" class="fulfilment-error-message">Please select delivery or pickup before proceeding to payment.</small>

                                        <?php if (!empty($zones)): ?>
                                            <div class="form-group mt-3 delivery-zone-wrap">
                                                <label for="delivery_zone_id">Delivery Location</label>
                                                <select name="delivery_zone_id" id="delivery_zone_id" class="form-control" form="checkoutForm">
                                                    <option value="">Default Delivery</option>
                                                    <?php foreach ($zones as $zone): ?>
                                                        <option value="<?= (int)$zone['zone_id']; ?>">
                                                            <?= htmlspecialchars($zone['zone_name']); ?> - <?= qs_money($zone['delivery_fee']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        <?php endif; ?>

                                        <div class="pickup-instructions mt-3" id="pickup-instructions" style="display:none;">
                                            <p><strong>Pickup Address:</strong><br><?= htmlspecialchars($settings['pickup_address']); ?></p>
                                            <?php if (!empty($settings['pickup_instruction'])): ?>
                                                <p><?= nl2br(htmlspecialchars($settings['pickup_instruction'])); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="service-only-checkout-note">Your cart contains services only, so no delivery fee or delivery address is required.</p>
                                    <?php endif; ?>

                                    <hr>

                                    <div class="coupon-box">
                                        <label for="coupon_code">Coupon Code</label>
                                        <div class="input-group">
                                            <input type="text" name="coupon_code" id="coupon_code" class="form-control" form="checkoutForm" placeholder="Code">
                                            <button class="btn btn-primary" type="button" id="apply-coupon">Apply</button>
                                        </div>
                                        <small id="coupon-message" class="d-block mt-2"></small>
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
                        </div>

                        <div class="ec-sidebar-wrap ec-checkout-pay-wrap">
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title">
                                    <h3 class="ec-sidebar-title">Payment Method</h3>
                                </div>
                                <div class="ec-sb-block-content">
                                    <div class="ec-checkout-pay">
                                        <div class="ec-pay-desc">Please select the preferred payment method to use on this order.</div>
                                        <span class="ec-pay-option">
                                            <span>
                                                <input type="radio" id="pay1" name="radio-group" checked>
                                                <label for="pay1">Online Payment</label>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ec-checkout-rightside col-lg-8 col-md-12">
                        <div class="ec-checkout-content">
                            <div class="ec-checkout-inner">
                                <div class="ec-checkout-wrap margin-bottom-30 padding-bottom-3">
                                    <div class="ec-checkout-block ec-check-bill">
                                        <h3 class="ec-checkout-title"><?= $requiresFulfilmentChoice ? 'Billing & Delivery Details' : 'Booking Details'; ?></h3>
                                        <?php
                                        try {
                                            $profile = $user->getUserProfile($_SESSION['user_id']);
                                            $firstname = $profile['firstname'] ?? '';
                                            $lastname = $profile['lastname'] ?? '';
                                            $email = $profile['email'] ?? '';
                                            $phone = $profile['phone'] ?? '';
                                            $address1 = $profile['address1'] ?? '';
                                            $address2 = $profile['address2'] ?? '';
                                            $city = $profile['city'] ?? '';
                                            $county = $profile['county'] ?? '';
                                            $postcode = $profile['postcode'] ?? '';
                                        } catch (Exception $e) {
                                            $firstname = $lastname = $email = $phone = '';
                                            $address1 = $address2 = $city = $county = $postcode = '';
                                        }
                                        $deliveryRequired = $selectedFulfilment === 'delivery' ? 'required' : '';
                                        ?>
                                        <div class="ec-check-bill-form">
                                            <form id="checkoutForm" action="../app/orderHandler.php" method="post">
                                                <?php if (!$requiresFulfilmentChoice): ?>
                                                    <input type="hidden" name="fulfilment_type" value="pickup">
                                                <?php endif; ?>
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
                                                <span class="ec-bill-wrap delivery-address-field">
                                                    <label>Address Line 1<span class="delivery-required-marker">*</span></label>
                                                    <input type="text" name="address1" id="address1" placeholder="123 High Street"
                                                        value="<?= htmlspecialchars($address1) ?>" <?= $deliveryRequired; ?> tabindex="5" />
                                                </span>
                                                <span class="ec-bill-wrap delivery-address-field">
                                                    <label>Address Line 2 (optional)</label>
                                                    <input type="text" name="address2" id="address2"
                                                        placeholder="Apartment, suite, etc. (optional)"
                                                        value="<?= htmlspecialchars($address2) ?>" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half delivery-address-field">
                                                    <label>Town / City<span class="delivery-required-marker">*</span></label>
                                                    <input type="text" name="city" id="city" placeholder="London"
                                                        value="<?= htmlspecialchars($city) ?>" <?= $deliveryRequired; ?> tabindex="6" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half delivery-address-field">
                                                    <label>County (optional)</label>
                                                    <input type="text" name="county" id="county" placeholder="Greater London"
                                                        value="<?= htmlspecialchars($county) ?>" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half delivery-address-field">
                                                    <label>Postcode<span class="delivery-required-marker">*</span></label>
                                                    <input type="text" name="postcode" id="postcode" placeholder="SW1A 1AA"
                                                        value="<?= htmlspecialchars($postcode) ?>" <?= $deliveryRequired; ?> tabindex="7" />
                                                </span>
                                                <span class="ec-bill-wrap ec-bill-half delivery-address-field">
                                                    <label>Country</label>
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
                                                                placeholder="Notes about your order."
                                                                rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                </span>
                                                <div class="ec-bill-wrap">
                                                    <label>
                                                        <input type="checkbox" name="privacy_consent" required tabindex="10" />
                                                        I agree to the processing of my personal data in accordance with the
                                                        <a href="privacy-policy.php" target="_blank"><b>Privacy Policy</b></a>.
                                                    </label>
                                                </div>
                                                <div class="ec-bill-wrap">
                                                    <span class="ec-check-order-btn">
                                                        <?php if ($summary['subtotal'] > 0): ?>
                                                            <button class="btn btn-lg btn-success btn-jittery" name="action" value="<?= $utility->inputEncode('place_order') ?>" type="submit">
                                                                Proceed to Payment
                                                            </button>
                                                        <?php else: ?>
                                                            <a href="shop.php" class="btn btn-lg btn-primary">Start Shopping</a>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <style>
        .fulfilment-choice-alert {
            background: #fff8e6;
            border: 1px solid #ffe0a3;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #5f4b20;
        }

        .fulfilment-choice-alert strong {
            display: block;
            color: #2f3542;
            margin-bottom: 4px;
        }

        .fulfilment-options {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 12px;
        }

        .fulfilment-option-card {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 0;
        }

        .fulfilment-option-card:hover {
            border-color: #3474d4;
            box-shadow: 0 8px 20px rgba(52, 116, 212, 0.12);
        }

        .fulfilment-option-card.active,
        .fulfilment-option-card:has(.fulfilment-radio:checked) {
            border-color: #3474d4;
            background: #f4f8ff;
            box-shadow: 0 8px 20px rgba(52, 116, 212, 0.15);
        }

        .fulfilment-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .fulfilment-option-indicator {
            width: 22px;
            height: 22px;
            min-width: 22px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            margin-top: 2px;
            background: #ffffff;
            position: relative;
        }

        .fulfilment-option-card.active .fulfilment-option-indicator,
        .fulfilment-option-card:has(.fulfilment-radio:checked) .fulfilment-option-indicator {
            border-color: #3474d4;
            background: #3474d4;
        }

        .fulfilment-option-card.active .fulfilment-option-indicator::after,
        .fulfilment-option-card:has(.fulfilment-radio:checked) .fulfilment-option-indicator::after {
            content: "";
            position: absolute;
            left: 6px;
            top: 3px;
            width: 6px;
            height: 11px;
            border: solid #ffffff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .fulfilment-option-copy strong {
            display: block;
            font-size: 16px;
            color: #2f3542;
            margin-bottom: 4px;
        }

        .fulfilment-option-copy small {
            display: block;
            font-size: 13px;
            line-height: 1.5;
            color: #6b7280;
        }

        .fulfilment-error-message {
            display: none;
            color: #dc3545;
            margin-top: 8px;
        }

        .fulfilment-error-message.show {
            display: block;
        }

        .pickup-instructions {
            background: #f7fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            color: #374151;
        }

        @media (min-width: 576px) {
            .fulfilment-options {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575px) {
            .fulfilment-option-card {
                padding: 14px;
            }

            .fulfilment-option-copy strong {
                font-size: 15px;
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const radios = document.querySelectorAll('input[name="fulfilment_type"]');
            const cards = document.querySelectorAll(".fulfilment-option-card");
            const selectedLabel = document.getElementById("fulfilment-selected-label");
            const pickupBox = document.getElementById("pickup-instructions");
            const deliveryZoneWrap = document.querySelector(".delivery-zone-wrap");
            const deliveryFields = document.querySelectorAll(".delivery-address-field input");
            const checkoutForm = document.getElementById("checkoutForm");
            const errorMessage = document.getElementById("fulfilment-error");

            function setDeliveryFieldsRequired(isDelivery) {
                deliveryFields.forEach(function(input) {
                    const fieldName = input.getAttribute("name");

                    if (["address1", "city", "postcode"].includes(fieldName)) {
                        input.required = isDelivery;
                    }

                    input.closest(".delivery-address-field").style.display = isDelivery ? "" : "none";
                });
            }

            function updateFulfilmentUI(value) {
                cards.forEach(function(card) {
                    const radio = card.querySelector('input[name="fulfilment_type"]');
                    card.classList.toggle("active", radio && radio.value === value);
                });

                if (errorMessage) {
                    errorMessage.classList.remove("show");
                }

                if (value === "delivery") {
                    if (selectedLabel) {
                        selectedLabel.textContent = "Delivery selected. Please confirm your delivery details below.";
                    }

                    if (pickupBox) {
                        pickupBox.style.display = "none";
                    }

                    if (deliveryZoneWrap) {
                        deliveryZoneWrap.style.display = "";
                    }

                    setDeliveryFieldsRequired(true);
                }

                if (value === "pickup") {
                    if (selectedLabel) {
                        selectedLabel.textContent = "Pickup selected. No delivery fee will be charged.";
                    }

                    if (pickupBox) {
                        pickupBox.style.display = "";
                    }

                    if (deliveryZoneWrap) {
                        deliveryZoneWrap.style.display = "none";
                    }

                    setDeliveryFieldsRequired(false);
                }
            }

            radios.forEach(function(radio) {
                radio.addEventListener("change", function() {
                    updateFulfilmentUI(this.value);
                });

                if (radio.checked) {
                    updateFulfilmentUI(radio.value);
                }
            });

            if (checkoutForm && radios.length > 0) {
                checkoutForm.addEventListener("submit", function(event) {
                    const selected = document.querySelector('input[name="fulfilment_type"]:checked');

                    if (!selected) {
                        event.preventDefault();

                        if (errorMessage) {
                            errorMessage.classList.add("show");
                        }

                        if (selectedLabel) {
                            selectedLabel.textContent = "Please choose how you want to receive your order before proceeding to payment.";
                        }

                        document.querySelector(".fulfilment-options").scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                    }
                });
            }
        });
    </script>

    <?php include './inc/footer.php'; ?>