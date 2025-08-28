<?php
include './inc/head.php';
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch orders for this user
$orders = $model->getRows("orders_mart", ["user_id" => $userId]);
?>

<body class="shop_page">
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div>Q</div>
            <div>U</div>
            <div>E</div>
            <div>E</div>
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
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Order History</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="ec-breadcrumb-item active">Order History</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User history section -->
    <section class="ec-page-content ec-vendor-uploads ec-user-account section-space-p">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="ec-shop-leftside ec-vendor-sidebar col-lg-3 col-md-12">
                    <div class="ec-sidebar-wrap ec-border-box">
                        <div class="ec-sidebar-block">
                            <div class="ec-vendor-block">
                                <div class="ec-vendor-block-items">
                                    <ul>
                                        <li><a href="user-profile.php">User Profile</a></li>
                                        <li><a href="user-history.php" class="active">History</a></li>
                                        <li><a href="wishlist.php">Wishlist</a></li>
                                        <li><a href="cart.php">Cart</a></li>
                                        <li><a href="checkout.php">Checkout</a></li>
                                        <li><a href="track-order.php">Track Order</a></li>
                                        <li><a href="user-invoice.php">Invoice</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="ec-shop-rightside col-lg-9 col-md-12">
                    <div class="ec-vendor-dashboard-card">
                        <div class="ec-vendor-card-header">
                            <h5>Your Orders</h5>
                            <div class="ec-header-btn">
                                <a class="btn btn-lg btn-primary" href="shop.php">Shop Now</a>
                            </div>
                        </div>
                        <div class="ec-vendor-card-body">
                            <div class="ec-vendor-card-table">
                                <table class="table ec-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">S/N</th>
                                            <th scope="col">Order Ref:</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Num of Items</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1; ?>
                                        <?php if (!empty($orders)): ?>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <th scope="row"><span><?php echo $count++; ?></span></th>
                                                    <td><span><b><?php echo strtoupper($order['order_reference'] ?? "N/A"); ?></b></span></td>
                                                    <td><span><b><?php echo date("d M Y", strtotime($order['created_at'])); ?></b></span></td>
                                                    <td scope="col"><span><b>£<?php echo number_format($order['total_amount'], 2); ?></b></span></td>
                                                    <td>
                                                        <span class="<?php echo $order['payment_status'] === 'paid' ? 'text-success' : 'text-danger'; ?>"><b>
                                                            <?php echo ucfirst($order['payment_status']); ?></b>
                                                        </span>
                                                    </td>
                                                    <td scope="col"><span><b><?php $countItem = $ordercount = $model->getRows("order_items_mart", [
                                                                    "where" => ["order_item_id" => $order['order_tbl_id']],
                                                                    "return_type" => "count"
                                                                ]);
                                                                echo $countItem; ?></b></span></td>
                                                    <td><span class="tbl-btn">
                                                            <a class="btn btn-lg btn-primary" href="order-details.php?id=<?php echo $order['order_tbl_id']; ?>">View</a>
                                                        </span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">You have no orders yet.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include './inc/footer.php'; ?>
</body>