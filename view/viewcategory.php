<?php
include './inc/head.php';
$_SESSION['categoryId'] = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$categoryname = $model->getRows("categories", [
    "where" => [
        "categoryTbl_id" => $_SESSION['categoryId']
    ],
    "return_type" => "single"
]);

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

    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="ec-shop-rightside col-lg-12 col-md-12">
                    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row ec_breadcrumb_inner">
                                        <div class="col-md-6 col-sm-12">
                                            <h2 class="ec-breadcrumb-title"><?= htmlspecialchars($categoryname['category_name']) ?></h2>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <!-- ec-breadcrumb-list start -->
                                            <ul class="ec-breadcrumb-list">
                                                <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                                <li class="ec-breadcrumb-item active"><?= htmlspecialchars($categoryname['category_name']) ?></li>
                                            </ul>
                                            <!-- ec-breadcrumb-list end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shop content Start -->
                    <div class="shop-pro-content">
                        <div class="shop-pro-inner">
                            <div class="row" id="product-category-container">
                                <!-- 🚀 Products will be injected here by loadproduct.js -->
                            </div>
                            <div id="loader" style="text-align:center; display:none;">
                                <p>Loading more products...</p>
                            </div>
                        </div>

                        <!-- Ec Pagination Start -->
                        <div class="ec-pro-pagination">
                            <span class="pagination-info"></span>
                            <ul class="ec-pro-pagination-inner">
                                <!-- 🚀 Pagination will be injected here -->
                            </ul>
                        </div>
                        <!-- Ec Pagination End -->

                    </div>
                    <!-- Shop content End -->

                </div>
            </div>
        </div>
    </section>

    <!-- ec Product tab Area End -->
    <?php include './inc/footer.php'; ?>