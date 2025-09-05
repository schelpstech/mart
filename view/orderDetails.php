<?php

include './inc/head.php';
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch orders for this user
$orders = $model->getRows(
    "orders_mart",
    [
        "where" => [
            "order_reference" => $_GET['ref'],
            "user_id" => $userId
        ],
        "return_type" => "single"
    ]
);

if (!empty($orders)) {
    $orderedItems = $model->getRows('order_items_mart', [
        'where' => [
            'order_reference' => $_GET['ref'],
            'user_id' => $userId
        ],
        'left_join' => [
            'orders_mart' => ' on orders_mart.order_tbl_id = order_items_mart.order_item_id'
        ]
    ]);
    $currency = "£";
} else {
    header("Location: login.php");
}
?>

<body class="shop_page">
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

    <!-- Breadcrumb -->
    <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Order Details</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="ec-breadcrumb-item active">Order Details</li>
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
                <?php include './inc/sidebar.php'; ?>

                <!-- Orders Table -->
                <div class="ec-shop-rightside col-lg-9 col-md-12">
                    <div class="ec-vendor-dashboard-card">
                        <div class="ec-vendor-card-header">
                            <h5>Order Details</h5>
                            <div class="ec-header-btn">
                                <a class="btn btn-lg btn-secondary" href="javascript:void(0);" onclick="window.print();">Print</a>
                                <a class="btn btn-lg btn-primary" href="invoice-pdf.php?ref=<?= urlencode($orders['order_reference']) ?>" target="_blank">Download PDF</a>

                            </div>
                        </div>
                        <div class="ec-vendor-card-body padding-b-0">
                            <div class="page-content">
                                <div class="page-header text-blue-d2">
                                    <img src="assets/images/logo/nlogo.png" alt="Queenzy Logo">
                                </div>

                                <div class="container px-0">
                                    <div class="row mt-4">
                                        <div class="col-lg-12">
                                            <hr class="row brc-default-l1 mx-n1 mb-4" />

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="my-2">
                                                        <span class="text-sm text-grey-m2 align-middle">To : </span>
                                                        <span class="text-600 text-110 text-blue align-middle"><?= $orders["firstname"] . " " . $orders["lastname"] ?>
                                                        </span>
                                                    </div>
                                                    <div class="text-grey-m2">
                                                        <div class="my-2">
                                                            <?= $orders["address1"] . " " . $orders["address2"] ?? "" ?>
                                                        </div>
                                                        <div class="my-2">
                                                            <?= $orders["city"] . " " . $orders["county"] ?? "" ?>
                                                        </div>
                                                        <div class="my-2">
                                                            <?= $orders["postcode"] . " " . $orders["country"] ?? "" ?>
                                                        </div>
                                                        <div class="my-2"><b class="text-600">Phone : </b><?= $orders["phone"] ?? "" ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.col -->

                                                <div
                                                    class="text-95 col-sm-6 align-self-start d-sm-flex justify-content-end">
                                                    <hr class="d-sm-none" />
                                                    <div class="text-grey-m2">

                                                        <div class="my-2">
                                                            <span class="text-600 text-90">Payment Status :</span>
                                                            <?php
                                                            $status = strtolower($orders["payment_status"] ?? "");
                                                            $style = "";

                                                            if ($status === "paid") {
                                                                $style = "color: green; font-weight: bold; font-size: 1.2rem;";
                                                            } elseif ($status === "pending") {
                                                                $style = "color: black; font-weight: bold; font-size: 1.2rem;";
                                                            } elseif ($status === "failed") {
                                                                $style = "color: red; font-weight: bold; font-size: 1.2rem;";
                                                            }
                                                            ?>
                                                            <span style="<?= $style; ?>"><?= ucfirst($status); ?></span>
                                                        </div>

                                                        <div class="my-2"><span class="text-600 text-90">Issue Date :
                                                            </span> <?= $orders["created_at"] ?? "" ?></div>

                                                        <div class="my-2"><span class="text-600 text-90">Order No :
                                                            </span><?= $orders["order_reference"] ?? "" ?></div>
                                                    </div>
                                                </div>
                                                <!-- /.col -->
                                            </div>

                                            <div class="mt-4">

                                                <div class="text-95 text-secondary-d3">
                                                    <div class="ec-vendor-card-table">
                                                        <table class="table ec-table">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">ID</th>
                                                                    <th scope="col">Name</th>
                                                                    <th scope="col">Qty</th>
                                                                    <th scope="col">Price</th>
                                                                    <th scope="col">Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $count = 1;

                                                                ?>
                                                                <?php $subtotal = 0;
                                                                if (!empty($orderedItems)): ?>
                                                                    <?php foreach ($orderedItems as $items):
                                                                        $subtotal += (float)$items['subtotal'];
                                                                    ?>
                                                                        <tr>
                                                                            <th scope="row"><span><?php echo $count++; ?></span></th>
                                                                            <td><span><b><?php echo strtoupper($items['product_name'] ?? "N/A"); ?></b></span></td>
                                                                            <td><span><b><?php echo strtoupper($items['quantity'] ?? "N/A"); ?></b></span></td>
                                                                            <td><span><b><?php echo strtoupper($items['price'] ?? "N/A"); ?></b></span></td>
                                                                            <td><span><b><?php echo $currency . number_format($items['subtotal'], 2) ?? "N/A"; ?></b></span></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <tr>
                                                                        <td colspan="7" class="text-center">You have no orders yet.</td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td class="border-none" colspan="3">
                                                                        <span></span>
                                                                    </td>
                                                                    <td class="border-color" colspan="1">
                                                                        <span><strong>Sub Total</strong></span>
                                                                    </td>
                                                                    <td class="border-color">
                                                                        <span><b><?php
                                                                                    echo   $currency . number_format($subtotal, 2) ?? "N/A"; ?></b></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-none" colspan="3">
                                                                        <span></span>
                                                                    </td>
                                                                    <td class="border-color" colspan="1">
                                                                        <span><strong>Delivery Fee </strong></span>
                                                                    </td>
                                                                    <td class="border-color">
                                                                        <span><b><?php echo  $currency . number_format(($orders['total_amount'] - $subtotal), 2) ?? "N/A"; ?></b></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="border-none m-m15"
                                                                        colspan="3"><span class="note-text-color"></span></td>
                                                                    <td class="border-color m-m15"
                                                                        colspan="1"><span><strong>Total</strong></span>
                                                                    </td>
                                                                    <td class="border-color m-m15">
                                                                        <span><b><?php echo  $currency . number_format($orders['total_amount'], 2) ?? "N/A"; ?></b></span>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
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
</body>
