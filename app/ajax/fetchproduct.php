<?php
require_once "../query.php";

$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Get products with offset + limit
$allproducts = $model->getRows("products", [
    "order_by" => "product_tbl_record_time DESC",
    "limit" => "8", // ✅ MySQL LIMIT with offset
    "offset" => "0", // ✅ MySQL LIMIT with offset
    'left_join' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);



$cartItems = $cart->getCartItems(); // get all current cart items

// Build a quick lookup by product_id for easier checking
$cartLookup = [];
if ($cartItems) {
    foreach ($cartItems as $c) {
        $cartLookup[$c['product_id']] = $c['cart_item_id'];
    }
}

if ($allproducts) {
    foreach ($allproducts as $product) {
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

        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content">
            <div class="ec-product-inner">
                <div class="ec-pro-image-outer">
                    <div class="ec-pro-image">
                        <a href="viewproduct.php?id=<?= $productId ?>" class="image">
                            <img class="main-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                            <img class="hover-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                        </a>
                        <span class="flags"><span class="new">New</span></span>
                        <div class="ec-pro-actions">
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
    echo ""; // no more products
}
