
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

                                $categories_prod = $model->getRows('categories', [
                                    'where' => ['section_id' => 1],
                                    'order_by' => 'category_name ASC'
                                ]);

                                if ($categories_prod) {
                                    foreach ($categories_prod as $cat) {
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
                                        ?>

                                                <?php include "./inc/productCard.php"; ?>

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
                                                $categoryId = htmlspecialchars($product['category_id'] ?? '');
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
                                                                <a href="viewproduct.php?id=<?= $productId ?>" class="image">
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
                                                            <a href="viewcategory.php?id=<?= $categoryId ?>">
                                                                <h6 class="ec-pro-stitle"><?= $productCategory ?></h6>
                                                            </a>
                                                            <h5 class="ec-pro-title"><a href="viewproduct.php?id=<?= $productId ?>"><?= $productName ?></a></h5>
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
                                                                <a href="viewproduct.php?id=<?= $productId ?>" class="image">
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
                                                            <h5 class="ec-pro-title"><a href="viewproduct.php?id=<?= $productId ?>"><?= $productName ?></a></h5>
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
                                                                <a href="viewproduct.php?id=<?= $productId ?>" class="image">
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
                                                            <h5 class="ec-pro-title"><a href="viewproduct.php?id=<?= $productId ?>"><?= $productName ?></a></h5>
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


                <!-- Product area Ends -->