<?php
include './inc/mainhead.php';
?>

<body >
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





    <!-- Main Slider Start -->
    <div class="ec-main-slider section section-space-pb">
        <div class="container">
            <div class="ec-slider swiper-container main-slider-nav main-slider-dot">
                <!-- Main slider -->
                <div class="swiper-wrapper">
                    <!-- Salon -->
                    <div class="ec-slide-item swiper-slide d-flex slide-1">
                        <div class="container align-self-center">
                            <div class="row">
                                <div class="col-sm-12 align-self-center">
                                    <div class="ec-slide-content slider-animation">
                                        <h2 class="ec-slide-stitle">Pamper Yourself</h2>
                                        <h1 class="ec-slide-title">Salon Services</h1>
                                        <div class="ec-slide-desc">
                                            <p>Hair, Nails & More from <b>£15</b>.00</p>
                                            <a href="#" class="btn btn-lg btn-primary">Book Appointment <i
                                                    class="ecicon eci-angle-double-right" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fashion Store -->
                    <div class="ec-slide-item swiper-slide d-flex slide-2">
                        <div class="container align-self-center">
                            <div class="row">
                                <div class="col-sm-12 align-self-center">
                                    <div class="ec-slide-content slider-animation">
                                        <h2 class="ec-slide-stitle">Trending Styles</h2>
                                        <h1 class="ec-slide-title">Queenzy Fashion</h1>
                                        <div class="ec-slide-desc">
                                            <p>Custom Outfits from <b>£25</b>.00</p>
                                            <a href="#" class="btn btn-lg btn-primary">Shop Now <i
                                                    class="ecicon eci-angle-double-right" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Beauty Store -->
                    <div class="ec-slide-item swiper-slide d-flex slide-3">
                        <div class="container align-self-center">
                            <div class="row">
                                <div class="col-sm-12 align-self-center">
                                    <div class="ec-slide-content slider-animation">
                                        <h2 class="ec-slide-stitle">Beauty Essentials</h2>
                                        <h1 class="ec-slide-title">Makeup & Haircare</h1>
                                        <div class="ec-slide-desc">
                                            <p>Top Brands from <b>£10</b>.00</p>
                                            <a href="#" class="btn btn-lg btn-primary">Explore Products <i
                                                    class="ecicon eci-angle-double-right" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grocery Store -->
                    <div class="ec-slide-item swiper-slide d-flex slide-4">
                        <div class="container align-self-center">
                            <div class="row">
                                <div class="col-sm-12 align-self-center">
                                    <div class="ec-slide-content slider-animation">
                                        <h2 class="ec-slide-stitle">Fresh & Affordable</h2>
                                        <h1 class="ec-slide-title">Groceries</h1>
                                        <div class="ec-slide-desc">
                                            <p>Daily Needs from <b>£5</b>.00</p>
                                            <a href="#" class="btn btn-lg btn-primary">Start Shopping <i
                                                    class="ecicon eci-angle-double-right" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-pagination swiper-pagination-white"></div>
                <div class="swiper-buttons">
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Slider End -->

    <!-- About Start -->
    <section class="section ec-about-sec section-space-p">
        <div class="container">
            <div class="row">
                <div class="section-title d-none">
                    <h2 class="ec-title">About</h2>
                </div>
                <div class="col-lg-6">
                    <div class="ec-about">
                        <img src="assets/images/banner/banner.png" alt="about-image">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ec-about-detail">
                        <h4>Beauty. Fashion. Convenience.</h4>
                        <h5>From elegant salon appointments to stylish African fashion and everyday essentials — all in one
                            place.</h5>
                        <p><strong>Queenzy African Stores and Braid Salon</strong> is your one-stop destination for premium
                            beauty services and quality products. Whether you're booking a manicure, getting your hair
                            braided, or shopping for custom-made outfits, groceries, hair care, or makeup — we bring it all
                            together under one roof.</p>
                        <p>We are committed to excellence, cultural pride, and customer satisfaction — offering you a
                            seamless shopping and salon experience with a modern touch.</p>
                        <a class="btn btn-lg btn-primary" href="shop-banner-left-sidebar-col-3.html">Explore Store</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About End -->



    <!--  category Section Start -->
    <section class="section ec-category-section section-space-p">
        <div class="container">
            <div class="row d-none">
                <div class="col-md-12">
                    <div class="section-title">
                        <h2 class="ec-title">Top Category</h2>
                    </div>
                </div>
            </div>
            <div class="row margin-minus-b-15 margin-minus-t-15">
                <div id="ec-cat-slider" class="ec-cat-slider owl-carousel">
                    <?php
                    $categories = $model->getRows('categories', [
                        'order_by' => 'category_name ASC'
                    ]);

                    if (!empty($categories)) {
                        foreach ($categories as $cat) {
                            $productCount = $model->getRows('products', [
                                'where' => [
                                    'category_id' => $cat['categoryTbl_id'],
                                    'status' => 'Active'
                                ],
                                'return_type' => 'count'
                            ]);
                    ?>
                            <div class="ec_cat_content ec_cat_content_<?= $cat['categoryTbl_id'] ?>">
                                <div class="ec_cat_inner ec_cat_inner-<?= $cat['categoryTbl_id'] ?>">
                                    <div class="ec-category-image">
                                        <img src="assets/images<?= $cat['icon'] ?? 'default.png' ?>" class="svg_img"
                                            alt="<?= $cat['category_name'] ?>" />
                                    </div>
                                    <div class="ec-category-desc">
                                        <h3><?= $cat['category_name'] ?> <span title="Category Items">(<?= $productCount ?>)</span>
                                        </h3>
                                        <a href="shop-left-sidebar-col-3.php?category=<?= $cat['categoryTbl_id'] ?>"
                                            class="cat-show-all">
                                            Show All <i class="ecicon eci-angle-double-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<p>No categories found.</p>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </section>
    <!--category Section End -->

    <!-- Product tab Area Start -->
    <section class="section ec-product-tab section-space-p">
        <div class="container">
            <div class="row">
                <!-- Product area start -->
                <div class="col-lg-12 col-md-12">
                    <!-- Product tab area start -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="section-title">
                                <h2 class="ec-title">Salon Services</h2>
                            </div>
                        </div>

                        <!-- Tab Start -->
                        <div class="col-md-12 ec-pro-tab">
                            <ul class="ec-pro-tab-nav nav justify-content-end">
                                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#all">All</a>
                                </li>
                                <?php
                                // Assuming you have $model already initialized and connected

                                $categories = $model->getRows('categories', [
                                    'where' => ['section_id' => 1],
                                    'order_by' => 'category_name ASC'
                                ]);

                                if ($categories) {
                                    foreach ($categories as $cat) {
                                        echo '<li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#cat' . $cat['categoryTbl_id'] . '">' . htmlspecialchars($cat['category_name']) . '</a>
                                            </li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <!-- Tab End -->
                    </div>
                    <div class="row margin-minus-b-15">
                        <div class="col">
                            <div class="tab-content">
                                <!-- 1st Product tab start -->
                                <?php
                                $cartItems = $cart->getCartItems(); // get all current cart items

                                // Build a quick lookup by product_id for easier checking
                                $cartLookup = [];
                                if ($cartItems) {
                                    foreach ($cartItems as $c) {
                                        $cartLookup[$c['product_id']] = $c['cart_item_id'];
                                    }
                                }
                                ?>

                                <div class="tab-pane fade show active" id="all">
                                    <div class="row">
                                        <?php
                                        if ($products_in_section_1) {
                                            foreach ($products_in_section_1 as $product) {
                                                $productName = htmlspecialchars($product['product_name']);
                                                $productId = htmlspecialchars($product['product_id']);
                                                $productImage = htmlspecialchars($product['image_main']);
                                                $productCategory = htmlspecialchars($product['category_name'] ?? '');
                                                $priceNew = number_format($product['price'], 2);
                                                $priceOld = $product['discount_price'] ? number_format($product['discount_price'], 2) : '';

                                                // Check if product is already in cart
                                                $inCart = isset($cartLookup[$productId]);
                                                $cartItemId = $inCart ? $cartLookup[$productId] : '';

                                                // Determine button state
                                                $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
                                                $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
                                                $cartBtnIcon = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';
                                        ?>

                                                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                                    <div class="ec-product-inner">
                                                        <div class="ec-pro-image-outer">
                                                            <div class="ec-pro-image">
                                                                <a href="product-left-sidebar.html" class="image">
                                                                    <img class="main-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                                                                    <img class="hover-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                                                                </a>
                                                                <span class="flags"><span class="new">New</span></span>
                                                                <div class="ec-pro-actions">
                                                                    <a class="ec-btn-group wishlist" title="Wishlist"><i class="fi-rr-heart"></i></a>
                                                                    <a href="#" class="ec-btn-group quickview" data-id="<?= $productId ?>" title="Quick view"
                                                                        data-bs-toggle="modal" data-bs-target="#ec_quickview_modal">
                                                                        <i class="fi-rr-eye"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0)" title="<?= $cartBtnTitle ?>"
                                                                        class="ec-btn-group <?= $cartBtnClass ?>"
                                                                        data-productid="<?= $productId ?>"
                                                                        data-cartitemid="<?= $cartItemId ?>"
                                                                        data-quantity="1"
                                                                        data-action="add">
                                                                        <i class="<?= $cartBtnIcon ?>"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="ec-pro-content">
                                                            <a href="shop-left-sidebar-col-3.html">
                                                                <h6 class="ec-pro-stitle"><?= $productCategory ?></h6>
                                                            </a>
                                                            <h5 class="ec-pro-title"><a href="product-left-sidebar.html"><?= $productName ?></a></h5>
                                                            <div class="ec-pro-rat-price">
                                                                <span class="ec-pro-rating">
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                </span>
                                                                <span class="ec-price">
                                                                    <span class="new-price">£<?= $priceNew ?></span>
                                                                    <?php if ($priceOld) echo "<span class='old-price'>£{$priceOld}</span>"; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                        <?php
                                            }
                                        } else {
                                            echo '<p>No products found in this section.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- ec 1st Product tab end -->
                                <!-- ec 2nd Product tab start -->
                                <div class="tab-pane fade" id="cat1">
                                    <div class="row">
                                        <?php
                                        if ($products_in_salon_nail) {
                                            foreach ($products_in_salon_nail as $product) {
                                                $productName = htmlspecialchars($product['product_name']);
                                                $productId = htmlspecialchars($product['product_id']);
                                                $productImage = htmlspecialchars($product['image_main']);
                                                $productCategory = htmlspecialchars($product['category_name'] ?? '');
                                                $priceNew = number_format($product['price'], 2);
                                                $priceOld = $product['discount_price'] ? number_format($product['discount_price'], 2) : '';

                                                // Check if product is already in cart
                                                $inCart = isset($cartLookup[$productId]);
                                                $cartItemId = $inCart ? $cartLookup[$productId] : '';

                                                // Determine button state
                                                $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
                                                $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
                                                $cartBtnIcon = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';
                                        ?>

                                                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                                    <div class="ec-product-inner">
                                                        <div class="ec-pro-image-outer">
                                                            <div class="ec-pro-image">
                                                                <a href="product-left-sidebar.html" class="image">
                                                                    <img class="main-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                                                                    <img class="hover-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                                                                </a>
                                                                <span class="flags"><span class="new">New</span></span>
                                                                <div class="ec-pro-actions">
                                                                    <a class="ec-btn-group wishlist" title="Wishlist"><i class="fi-rr-heart"></i></a>
                                                                    <a href="#" class="ec-btn-group quickview" data-id="<?= $productId ?>" title="Quick view"
                                                                        data-bs-toggle="modal" data-bs-target="#ec_quickview_modal">
                                                                        <i class="fi-rr-eye"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0)" title="<?= $cartBtnTitle ?>"
                                                                        class="ec-btn-group <?= $cartBtnClass ?>"
                                                                        data-productid="<?= $productId ?>"
                                                                        data-cartitemid="<?= $cartItemId ?>"
                                                                        data-quantity="1"
                                                                        data-action="add">
                                                                        <i class="<?= $cartBtnIcon ?>"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="ec-pro-content">
                                                            <a href="shop-left-sidebar-col-3.html">
                                                                <h6 class="ec-pro-stitle"><?= $productCategory ?></h6>
                                                            </a>
                                                            <h5 class="ec-pro-title"><a href="product-left-sidebar.html"><?= $productName ?></a></h5>
                                                            <div class="ec-pro-rat-price">
                                                                <span class="ec-pro-rating">
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                </span>
                                                                <span class="ec-price">
                                                                    <span class="new-price">£<?= $priceNew ?></span>
                                                                    <?php if ($priceOld) echo "<span class='old-price'>£{$priceOld}</span>"; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                        <?php
                                            }
                                        } else {
                                            echo '<p>No products found in this section.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!-- ec 2nd Product tab end -->
                                <!-- ec 3rd Product tab start -->

                                <div class="tab-pane fade" id="cat2">
                                    <div class="row">
                                        <?php
                                        if ($products_in_salon_facial) {
                                            foreach ($products_in_salon_facial as $product) {
                                                $productName = htmlspecialchars($product['product_name']);
                                                $productId = htmlspecialchars($product['product_id']);
                                                $productImage = htmlspecialchars($product['image_main']);
                                                $productCategory = htmlspecialchars($product['category_name'] ?? '');
                                                $priceNew = number_format($product['price'], 2);
                                                $priceOld = $product['discount_price'] ? number_format($product['discount_price'], 2) : '';

                                                // Check if product is already in cart
                                                $inCart = isset($cartLookup[$productId]);
                                                $cartItemId = $inCart ? $cartLookup[$productId] : '';

                                                // Determine button state
                                                $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
                                                $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
                                                $cartBtnIcon = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';
                                        ?>

                                                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                                    <div class="ec-product-inner">
                                                        <div class="ec-pro-image-outer">
                                                            <div class="ec-pro-image">
                                                                <a href="product-left-sidebar.html" class="image">
                                                                    <img class="main-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                                                                    <img class="hover-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                                                                </a>
                                                                <span class="flags"><span class="new">New</span></span>
                                                                <div class="ec-pro-actions">
                                                                    <a class="ec-btn-group wishlist" title="Wishlist"><i class="fi-rr-heart"></i></a>
                                                                    <a href="#" class="ec-btn-group quickview" data-id="<?= $productId ?>" title="Quick view"
                                                                        data-bs-toggle="modal" data-bs-target="#ec_quickview_modal">
                                                                        <i class="fi-rr-eye"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0)" title="<?= $cartBtnTitle ?>"
                                                                        class="ec-btn-group <?= $cartBtnClass ?>"
                                                                        data-productid="<?= $productId ?>"
                                                                        data-cartitemid="<?= $cartItemId ?>"
                                                                        data-quantity="1"
                                                                        data-action="add">
                                                                        <i class="<?= $cartBtnIcon ?>"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="ec-pro-content">
                                                            <a href="shop-left-sidebar-col-3.html">
                                                                <h6 class="ec-pro-stitle"><?= $productCategory ?></h6>
                                                            </a>
                                                            <h5 class="ec-pro-title"><a href="product-left-sidebar.html"><?= $productName ?></a></h5>
                                                            <div class="ec-pro-rat-price">
                                                                <span class="ec-pro-rating">
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                </span>
                                                                <span class="ec-price">
                                                                    <span class="new-price">£<?= $priceNew ?></span>
                                                                    <?php if ($priceOld) echo "<span class='old-price'>£{$priceOld}</span>"; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                        <?php
                                            }
                                        } else {
                                            echo '<p>No products found in this section.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!-- ec 3rd Product tab end -->
                                <!-- ec 3rd Product tab start -->
                                <div class="tab-pane fade" id="cat3">
                                    <div class="row">
                                        <?php
                                        if ($products_in_salon_hair) {
                                            foreach ($products_in_salon_hair as $product) {
                                                $productName = htmlspecialchars($product['product_name']);
                                                $productId = htmlspecialchars($product['product_id']);
                                                $productImage = htmlspecialchars($product['image_main']);
                                                $productCategory = htmlspecialchars($product['category_name'] ?? '');
                                                $priceNew = number_format($product['price'], 2);
                                                $priceOld = $product['discount_price'] ? number_format($product['discount_price'], 2) : '';

                                                // Check if product is already in cart
                                                $inCart = isset($cartLookup[$productId]);
                                                $cartItemId = $inCart ? $cartLookup[$productId] : '';

                                                // Determine button state
                                                $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
                                                $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
                                                $cartBtnIcon = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';
                                        ?>

                                                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 ec-product-content">
                                                    <div class="ec-product-inner">
                                                        <div class="ec-pro-image-outer">
                                                            <div class="ec-pro-image">
                                                                <a href="product-left-sidebar.html" class="image">
                                                                    <img class="main-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                                                                    <img class="hover-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                                                                </a>
                                                                <span class="flags"><span class="new">New</span></span>
                                                                <div class="ec-pro-actions">
                                                                    <a class="ec-btn-group wishlist" title="Wishlist"><i class="fi-rr-heart"></i></a>
                                                                    <a href="#" class="ec-btn-group quickview" data-id="<?= $productId ?>" title="Quick view"
                                                                        data-bs-toggle="modal" data-bs-target="#ec_quickview_modal">
                                                                        <i class="fi-rr-eye"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0)" title="<?= $cartBtnTitle ?>"
                                                                        class="ec-btn-group <?= $cartBtnClass ?>"
                                                                        data-productid="<?= $productId ?>"
                                                                        data-cartitemid="<?= $cartItemId ?>"
                                                                        data-quantity="1"
                                                                        data-action="add">
                                                                        <i class="<?= $cartBtnIcon ?>"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="ec-pro-content">
                                                            <a href="shop-left-sidebar-col-3.html">
                                                                <h6 class="ec-pro-stitle"><?= $productCategory ?></h6>
                                                            </a>
                                                            <h5 class="ec-pro-title"><a href="product-left-sidebar.html"><?= $productName ?></a></h5>
                                                            <div class="ec-pro-rat-price">
                                                                <span class="ec-pro-rating">
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                    <i class="ecicon eci-star fill"></i>
                                                                </span>
                                                                <span class="ec-price">
                                                                    <span class="new-price">£<?= $priceNew ?></span>
                                                                    <?php if ($priceOld) echo "<span class='old-price'>£{$priceOld}</span>"; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                        <?php
                                            }
                                        } else {
                                            echo '<p>No products found in this section.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!-- ec 3rd Product tab end -->
                            </div>
                        </div>
                    </div>
                    <!-- Product tab area end -->
                </div>
            </div>
        </div>
    </section>
    <!-- ec Product tab Area End -->

    <!--  Testimonial, Banner & Service Section Start -->
    <section class="section ec-ser-spe-section section-space-p">
        <div class="container" data-animation="fadeIn">
            <div class="row">
                <!-- ec Testimonial Start -->
                <div class="ec-test-section col-lg-3 col-md-6 col-sm-12 col-xs-6 sectopn-spc-mb"
                    data-animation="slideInRight">
                    <div class="col-md-12">
                        <div class="section-title">
                            <h2 class="ec-title">Testimonial</h2>
                        </div>
                    </div>
                    <div class="ec-test-outer">
                        <ul id="ec-testimonial-slider">

                            <?php
                            if ($latest_testimonials):
                                foreach ($latest_testimonials as $test):
                            ?>
                                    <li class="ec-test-item">
                                        <div class="ec-test-inner">
                                            <div class="ec-test-img">
                                                <img alt="testimonial" title="testimonial" src="<?= htmlspecialchars($test['photo']) ?>" />
                                            </div>
                                            <div class="ec-test-content">
                                                <div class="ec-test-name"><?= htmlspecialchars($test['name']) ?></div>
                                                <div class="ec-test-designation">- <?= htmlspecialchars($test['location']) ?></div>
                                                <div class="ec-test-divider">
                                                    <i class="fi-rr-quote-right"></i>
                                                </div>
                                                <div class="ec-test-desc"><?= nl2br(htmlspecialchars($test['message'])) ?></div>
                                            </div>
                                        </div>
                                    </li>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                </div>
                <!-- ec Testimonial end -->
                <!-- ec Banner Start -->
                <div class="col-md-6 col-sm-12" data-animation="fadeIn">
                    <div class="ec-banner-inner">
                        <div class="ec-banner-block ec-banner-block-1">
                            <div class="banner-block">
                                <div class="banner-content">
                                    <div class="banner-text">
                                        <span class="ec-banner-disc">25% discount</span>
                                        <span class="ec-banner-title">Sports & Formal</span>
                                        <span class="ec-banner-stitle">Starting @ £20</span>
                                    </div>
                                    <span class="ec-banner-btn"><a href="shop-left-sidebar-col-3.html">Shop Now <i
                                                class="ecicon eci-angle-double-right" aria-hidden="true"></i></a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ec Banner End -->
                <!--  Service Section Start -->
                <div class="ec-services-section col-lg-3 col-md-3 col-sm-3" data-animation="slideInLeft">
                    <div class="col-md-12">
                        <div class="section-title">
                            <h2 class="ec-title">Our Services</h2>
                        </div>
                    </div>
                    <div class="ec_ser_block">
                        <div class="ec_ser_content ec_ser_content_1 col-sm-12">
                            <div class="ec_ser_inner">
                                <div class="ec-service-image">
                                    <i class="fi fi-ts-truck-moving"></i>
                                </div>
                                <div class="ec-service-desc">
                                    <h2>Worldwide Delivery</h2>
                                    <p>For Order Over $100</p>
                                </div>
                            </div>
                        </div>
                        <div class="ec_ser_content ec_ser_content_2 col-sm-12">
                            <div class="ec_ser_inner">
                                <div class="ec-service-image">
                                    <i class="fi fi-ts-tachometer-fast"></i>
                                </div>
                                <div class="ec-service-desc">
                                    <h2>Next Day delivery</h2>
                                    <p>UK Orders Only</p>
                                </div>
                            </div>
                        </div>
                        <div class="ec_ser_content ec_ser_content_3 col-sm-12">
                            <div class="ec_ser_inner">
                                <div class="ec-service-image">
                                    <i class="fi fi-ts-circle-phone"></i>
                                </div>
                                <div class="ec-service-desc">
                                    <h2>Best Online Support</h2>
                                    <p>Hours: 8AM -11PM</p>
                                </div>
                            </div>
                        </div>
                        <div class="ec_ser_content ec_ser_content_4 col-sm-12">
                            <div class="ec_ser_inner">
                                <div class="ec-service-image">
                                    <i class="fi fi-ts-badge-percent"></i>
                                </div>
                                <div class="ec-service-desc">
                                    <h2>Return Policy</h2>
                                    <p>Easy & Free Return</p>
                                </div>
                            </div>
                        </div>
                        <div class="ec_ser_content ec_ser_content_5 col-sm-12">
                            <div class="ec_ser_inner">
                                <div class="ec-service-image">
                                    <i class="fi fi-ts-donate"></i>
                                </div>
                                <div class="ec-service-desc">
                                    <h2>30% money back</h2>
                                    <p>For Order Over $100</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ec Service End -->
            </div>
        </div>
    </section>
    <!--  End Testimonial, Banner & Service Section Start -->

    <!-- Ec Instagram Start -->
    <section class="section ec-instagram-section section-space-pt">
        <div class="container">
            <h2 class="d-none">Instagram</h2>
            <div class="ec-insta-wrapper" data-animation="fadeIn">
                <div class="ec-insta-outer">
                    <div class="insta-auto">
                        <!-- instagram item -->
                        <div class="ec-insta-item">
                            <div class="ec-insta-inner">
                                <a href="#" target="_blank"><img src="assets/images/instragram-image/1.jpg" alt="">

                                </a>
                            </div>
                        </div>
                        <!-- instagram item -->
                        <div class="ec-insta-item">
                            <div class="ec-insta-inner">
                                <a href="#" target="_blank"><img src="assets/images/instragram-image/2.jpg" alt="">

                                </a>
                            </div>
                        </div>
                        <!-- instagram item -->
                        <div class="ec-insta-item">
                            <div class="ec-insta-inner">
                                <a href="#" target="_blank"><img src="assets/images/instragram-image/3.jpg" alt="">

                                </a>
                            </div>
                        </div>
                        <!-- instagram item -->
                        <div class="ec-insta-item">
                            <div class="ec-insta-inner">
                                <a href="#" target="_blank"><img src="assets/images/instragram-image/4.jpg" alt="">

                                </a>
                            </div>
                        </div>
                        <!-- instagram item -->
                        <!-- instagram item -->
                        <div class="ec-insta-item">
                            <div class="ec-insta-inner">
                                <a href="#" target="_blank"><img src="assets/images/instragram-image/5.jpg" alt="">

                                </a>
                            </div>
                        </div>
                        <!-- instagram item -->
                        <div class="ec-insta-item">
                            <div class="ec-insta-inner">
                                <a href="#" target="_blank"><img src="assets/images/instragram-image/6.jpg" alt="">

                                </a>
                            </div>
                        </div>
                        <!-- instagram item -->
                        <div class="ec-insta-item">
                            <div class="ec-insta-inner">
                                <a href="#" target="_blank"><img src="assets/images/instragram-image/7.jpg" alt="">

                                </a>
                            </div>
                        </div>
                        <!-- instagram item -->
                        <div class="ec-insta-item">
                            <div class="ec-insta-inner">
                                <a href="#" target="_blank"><img src="assets/images/instragram-image/3.jpg" alt="">

                                </a>
                            </div>
                        </div>
                        <!-- instagram item -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Ec Instagram End -->
    <?php include './inc/footer.php'; ?>