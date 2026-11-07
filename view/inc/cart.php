<?php
if (defined('QS_CART_DRAWER_RENDERED')) return;
define('QS_CART_DRAWER_RENDERED', true);
?>

    <!-- Ekka Cart Start -->
    <div class="ec-side-cart-overlay"></div>
    <div id="ec-side-cart" class="ec-side-cart" role="dialog" aria-modal="true" aria-label="Shopping cart" aria-hidden="true" inert>
        <div class="ec-cart-inner">
            <div class="ec-cart-top">
                <div class="ec-cart-title">
                    <span class="cart_title">My Cart</span>
                    <button type="button" class="ec-close" aria-label="Close cart">×</button>
                </div>
                <ul class="eccart-pro-items" id="ajax-cart-items">
                    <!-- Cart AJAX content will be loaded here -->
                </ul>


            </div>
            <div class="ec-cart-bottom">

                <div class="cart-sub-total">
                    <table class="table cart-table">
                        <tbody>
                            <tr>
                                <td class="text-left">Items total :</td>
                                <td class="text-right" id="ajax-subtotal">£0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="cart_btn">
                    <a href="viewcart.php" class="btn btn-primary">View Cart</a>
                    <a href="checkout.php" class="btn btn-secondary">Checkout</a>
                </div>
                <p class="cart-delivery-note">Delivery and discounts are calculated at checkout.</p>
                <?php $cartShareContext = 'cart'; include __DIR__ . '/cartShare.php'; ?>
            </div>
        </div>
    </div>
