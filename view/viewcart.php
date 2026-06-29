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

    $summary = $cart->getCartSummary();
    $items = $summary['items'];
    $total = $summary['subtotal'];
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
                                        <h2 class="ec-breadcrumb-title">Cart</h2>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <ul class="ec-breadcrumb-list">
                                            <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                            <li class="ec-breadcrumb-item active">Cart</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ec-cart-leftside col-lg-8 col-md-12">
                    <div class="ec-cart-content">
                        <div class="ec-cart-inner">
                            <div class="row">
                                <form action="#">
                                    <div class="table-content cart-table-content">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Price</th>
                                                    <th style="text-align: center;">Quantity</th>
                                                    <th>Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($items)) : ?>
                                                    <?php foreach ($items as $item) : ?>
                                                        <?php
                                                        $lineTotal = (float)$item['price'] * (int)$item['quantity'];
                                                        $type = $item['item_type'] ?? 'product';
                                                        $itemId = (int)($item['item_id'] ?? $item['cart_item_id']);
                                                        ?>
                                                        <tr id="cart-item-<?= $type . '-' . $itemId; ?>">
                                                            <td data-label="Item" class="ec-cart-pro-name">
                                                                <a href="<?= htmlspecialchars($item['url'] ?? '#'); ?>">
                                                                    <img class="ec-cart-pro-img mr-4"
                                                                        src="<?= htmlspecialchars($item['image'] ?? 'assets/images/product/main/default.png'); ?>"
                                                                        alt="<?= htmlspecialchars($item['name']); ?>" />
                                                                    <?= htmlspecialchars($item['name']); ?>
                                                                </a>
                                                                <span class="cart-item-type"><?= ucfirst($type); ?></span>
                                                            </td>
                                                            <td data-label="Price" class="ec-cart-pro-price">
                                                                <span class="amount"><?= qs_money($item['price']); ?></span>
                                                            </td>
                                                            <td data-label="Quantity" class="ec-cart-pro-qty" style="text-align: center;">
                                                                <div class="cart-qty-plus-minus">
                                                                    <input class="cart-plus-minus"
                                                                        type="number"
                                                                        data-cartitemid="<?= $itemId; ?>"
                                                                        data-itemtype="<?= htmlspecialchars($type); ?>"
                                                                        value="<?= (int)$item['quantity']; ?>"
                                                                        min="1" />
                                                                </div>
                                                            </td>
                                                            <td data-label="Total" class="ec-cart-pro-subtotal">
                                                                <span class="line-total"><?= qs_money($lineTotal); ?></span>
                                                            </td>
                                                            <td data-label="Remove" class="ec-cart-pro-remove">
                                                                <a href="javascript:void(0);"
                                                                    class="remove-from-cart"
                                                                    data-cartitemid="<?= $itemId; ?>"
                                                                    data-itemtype="<?= htmlspecialchars($type); ?>">
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
                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="ec-cart-update-bottom cart-action-row">
                                                <a href="shop.php" class="cart-action-button">Continue Shopping</a>
                                                <?php if (!empty($items)): ?>
                                                    <a href="checkout.php" class="btn btn-success cart-action-button" style="color: white;">Check Out</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

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
                                            <span class="text-right cart-subtotal"><?= qs_money($total); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-left">Delivery / Pickup</span>
                                            <span class="text-right">Checkout</span>
                                        </div>
                                        <div class="ec-cart-summary-total">
                                            <span class="text-left">Current Total</span>
                                            <span class="text-right cart-grandtotal"><?= qs_money($total); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include './inc/footer.php'; ?>
