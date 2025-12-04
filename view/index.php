<?php
include './inc/mainhead.php';
?>

<body>
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
                                            <a href="https://queenzystores.com/view/booking.php" class="btn btn-lg btn-primary">Book Appointment <i
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
                                            <a href="https://queenzystores.com/view/viewcategory.php?id=10" class="btn btn-lg btn-primary">Shop Now <i
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
                                            <a href="https://queenzystores.com/view/viewcategory.php?id=15" class="btn btn-lg btn-primary">Explore Products <i
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
                                            <a href="https://queenzystores.com/view/viewcategory.php?id=1" class="btn btn-lg btn-primary">Start Shopping <i
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
                        <p><strong>Queenzy African Store and Braids Salon</strong> is your one-stop destination for premium
                            beauty services and quality products. Whether you're booking a manicure, getting your hair
                            braided, or shopping for custom-made outfits, groceries, hair care, or makeup — we bring it all
                            together under one roof.</p>
                        <p>We are committed to excellence, cultural pride, and customer satisfaction — offering you a
                            seamless shopping and salon experience with a modern touch.</p>
                        <a class="btn btn-lg btn-primary" href="./shop.php">Explore Store</a>
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
                                        <a href="viewcategory.php?category=<?= $cat['categoryTbl_id'] ?>"
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

                <!-- New Product Listing  area start -->
                <?php
                // Fetch all sections
                $sections = $model->getRows("sections", [
                    "order_by" => "section_name ASC"
                ]);

                if ($sections):
                    foreach ($sections as $section):
                        $sectionId = $section['id'];
                        $sectionName = htmlspecialchars($section['section_name']);
                ?>
                        <!-- Product area start -->
                        <div class="col-lg-12 col-md-12 mb-5">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="section-title">
                                        <h2 class="ec-title"><?= $sectionName ?></h2>
                                    </div>
                                </div>

                                <!-- Tabs Start -->
                                <div class="col-md-12 ec-pro-tab">
                                    <ul class="ec-pro-tab-nav nav justify-content-end">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#all<?= $sectionId ?>">All</a>
                                        </li>
                                        <?php
                                        // Fetch latest 4 categories in this section
                                        $categories = $model->getRows("categories", [
                                            "where" => ["section_id" => $sectionId],
                                            "order_by" => "record_time DESC",
                                            "limit" => 4
                                        ]);

                                        if ($categories) {
                                            foreach ($categories as $cat) {
                                                echo '<li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#cat' . $cat['categoryTbl_id'] . '">'
                                                    . htmlspecialchars($cat['category_name']) .
                                                    '</a>
                                  </li>';
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <!-- Tabs End -->

                                <div class="col">
                                    <div class="tab-content">

                                        <!-- All Products Tab -->
                                        <div class="tab-pane fade show active" id="all<?= $sectionId ?>">
                                            <div class="row">
                                                <?php
                                                // Get all products in this section
                                                $products = $model->getRows("products", [
                                                    "where" => ["section_id" => $sectionId],
                                                    'left_join' => [
                                                        'categories' => ' on products.category_id = categories.categoryTbl_id',
                                                        'sections' => ' on categories.section_id = sections.id'
                                                    ],
                                                    "order_by" => "product_tbl_record_time DESC",
                                                    "limit" => 8
                                                ]);

                                                if ($products) {
                                                    foreach ($products as $product) {

                                                        $productName = htmlspecialchars($product['product_name']);
                                                        $productId = htmlspecialchars($product['product_id']);
                                                        $productImage = htmlspecialchars($product['image_main']);
                                                        $productCategory = htmlspecialchars($product['category_name'] ?? '');
                                                        $categoryId = htmlspecialchars($product['category_id'] ?? '');
                                                        $priceNew = number_format($product['price'], 2);
                                                        $priceOld = $product['discount_price'] ? number_format($product['discount_price'], 2) : '';

                                                        // Check if product is already in cart
                                                        $inCart = isset($cartLookup[$productId]);
                                                        $cartItemId = $inCart ? $cartLookup[$productId] : '';

                                                        // Determine button state
                                                        $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
                                                        $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
                                                        $cartBtnIcon = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';

                                                        include "./inc/productCard.php";
                                                    }
                                                } else {
                                                    echo "<p>No products found in this section.</p>";
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <?php
                                        // Category specific tabs
                                        if ($categories) {
                                            foreach ($categories as $cat) {
                                                $catId = $cat['categoryTbl_id'];
                                                $catName = htmlspecialchars($cat['category_name']);
                                        ?>
                                                <div class="tab-pane fade" id="cat<?= $catId ?>">
                                                    <div class="row">
                                                        <?php
                                                        $catProducts = $model->getRows("products", [
                                                            "where" => ["category_id" => $catId],
                                                            "order_by" => "product_tbl_record_time DESC",
                                                            "limit" => 8
                                                        ]);

                                                        if ($catProducts) {
                                                            foreach ($catProducts as $productd) {
                                                                $productName = htmlspecialchars($productd['product_name']);
                                                                $productId = htmlspecialchars($productd['product_id']);
                                                                $productImage = htmlspecialchars($productd['image_main']);
                                                                $productCategory = htmlspecialchars($productd['category_name'] ?? '');
                                                                $categoryId = htmlspecialchars($productd['category_id'] ?? '');
                                                                $priceNew = number_format($productd['price'], 2);
                                                                $priceOld = $product['discount_price'] ? number_format($product['discount_price'], 2) : '';

                                                                // Check if product is already in cart
                                                                $inCart = isset($cartLookup[$productId]);
                                                                $cartItemId = $inCart ? $cartLookup[$productId] : '';

                                                                // Determine button state
                                                                $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
                                                                $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
                                                                $cartBtnIcon = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';

                                                                include "./inc/productCard.php";
                                                            }
                                                        } else {
                                                            echo "<p>No products found in $catName.</p>";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Product area end -->
                <?php
                    endforeach;
                endif;
                ?>

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
                                                <img alt="testimonial" title="testimonial" src="../view/assets/images/banner/customer.jpg" alt="Testimonial" />
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
                        <div class="ec-banner-block  ec-banner-block-1">
                            <div class="banner-block">
                                <div class="banner-content">
                                    <div class="banner-text">
                                        <span class="ec-banner-disc">Quality products you can trust</span>
                                        <span class="ec-banner-title">Shop the Latest </span>
                                        <span class="ec-banner-stitle">Affordable prices for everyone</span>
                                    </div>
                                    <span class="ec-banner-btn">
                                        <a href="shop.php">Shop Now
                                            <i class="ecicon eci-angle-double-right" aria-hidden="true"></i>
                                        </a>
                                    </span>
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
                                    <p>For Order Over £100</p>
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
                                    <p>For Order Over £100</p>
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
            <h2 class="d-none">Recently Added Products</h2>
            <div class="ec-insta-wrapper" data-animation="fadeIn">
                <div class="ec-insta-outer">
                    <div class="insta-auto">
                        <?php
                        // Get all products in this section
                        $productImgs = $model->getRows("products", [
                            "order_by" => "product_tbl_record_time DESC",
                            "limit" => 20
                        ]);

                        if (!empty($productImgs)) {
                            foreach ($productImgs as $product) {
                                $productId = htmlspecialchars($product['product_id']);
                                $productImage = htmlspecialchars($product['image_main']);
                        ?>
                                <!-- instagram item -->
                                <div class="ec-insta-item">
                                    <div class="ec-insta-inner">
                                        <a href="viewproduct.php?id=<?= $productId ?>" target="_blank">
                                            <img src="../view/assets/images/product/main/<?= $productImage ?>" alt="Product">
                                        </a>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p>No products found.</p>";
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Ec Instagram End -->
    <?php include './inc/footer.php'; ?>