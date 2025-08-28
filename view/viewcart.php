<?php
include './inc/head.php';
?>

<body class="cart_page">
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
                            <h2 class="ec-breadcrumb-title">Cart</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <!-- ec-breadcrumb-list start -->
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="index.html">Home</a></li>
                                <li class="ec-breadcrumb-item active">Cart</li>
                            </ul>
                            <!-- ec-breadcrumb-list end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <!-- Ec cart page -->
    <?php
    // Get all cart items for this session
    $items = $cart->getCartItems();
    $total = 0;
    ?>

    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="ec-cart-leftside col-lg-8 col-md-12 ">
                    <div class="ec-cart-content">
                        <div class="ec-cart-inner">
                            <div class="row">
                                <form action="#">
                                    <div class="table-content cart-table-content">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th style="text-align: center;">Quantity</th>
                                                    <th>Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($items)) : ?>
                                                    <?php foreach ($items as $item) : ?>
                                                        <?php $lineTotal = $item['price'] * $item['quantity']; ?>
                                                        <?php $total += $lineTotal; ?>
                                                        <tr id="cart-item-<?= $item['cart_item_id']; ?>">
                                                            <td data-label="Product" class="ec-cart-pro-name">
                                                                <a href="product.php?id=<?= $item['product_id']; ?>">
                                                                    <img class="ec-cart-pro-img mr-4"
                                                                        src="assets/images/product/<?= $item['image_main']; ?>"
                                                                        alt="<?= htmlspecialchars($item['name']); ?>" />
                                                                    <?= htmlspecialchars($item['name']); ?>
                                                                </a>
                                                            </td>
                                                            <td data-label="Price" class="ec-cart-pro-price">
                                                                <span class="amount">$<?= number_format($item['price'], 2); ?></span>
                                                            </td>
                                                            <!-- Quantity -->
                                                            <td data-label="Quantity" class="ec-cart-pro-qty" style="text-align: center;">
                                                                <div class="cart-qty-plus-minus">
                                                                    <input class="cart-plus-minus"
                                                                        type="number"
                                                                        data-cartitemid="<?= $item['cart_item_id']; ?>"
                                                                        value="<?= $item['quantity']; ?>"
                                                                        min="1" />
                                                                </div>
                                                            </td>

                                                            <!-- Line total -->
                                                            <td data-label="Total" class="ec-cart-pro-subtotal">
                                                                <span class="line-total">£<?= number_format($lineTotal, 2); ?></span>
                                                            </td>
                                                            <td data-label="Remove" class="ec-cart-pro-remove">
                                                                <a href="javascript:void(0);"
                                                                    class="remove-from-cart"
                                                                    data-cartitemid="<?= $item['cart_item_id']; ?>">
                                                                    <i class="ecicon eci-trash-o"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" style="text-align:center;">Your cart is empty.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            </thead>
                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="ec-cart-update-bottom">
                                                <a href="shop.php">Continue Shopping</a>
                                                <a href="checkout.php" class="btn btn-primary">Check Out</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="ec-cart-rightside col-lg-4 col-md-12">
                    <div class="ec-sidebar-wrap">
                        <div class="ec-sidebar-block">
                            <div class="ec-sb-title">
                                <h3 class="ec-sidebar-title">Summary</h3>
                            </div>
                            <div class="ec-sb-block-content">
                                <div class="ec-cart-summary-bottom">
                                    <div class="ec-cart-summary">
                                        <div>
                                            <span class="text-left">Sub-Total</span>
                                            <span class="text-right cart-subtotal">£<?= number_format($total, 2); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-left">Delivery Charges</span>
                                            <span class="text-right">£0.00</span>
                                        </div>
                                        <div class="ec-cart-summary-total">
                                            <span class="text-left">Total Amount</span>
                                            <span class="text-right cart-grandtotal">£<?= number_format($total, 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar End -->
            </div>
        </div>
    </section>


    <!-- New Product end -->
    <?php include './inc/footer.php'; ?>