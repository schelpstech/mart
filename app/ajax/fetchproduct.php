<?php
require_once "../query.php";

$limit  = max(1, min(48, (int)($_GET['limit'] ?? 8)));
$offset = max(0, (int)($_GET['offset'] ?? 0));

// Stable ordering prevents duplicate/skipped products between infinite-scroll pages.
$allproducts = $model->getRows("products", [
    'where' => ['status' => 'Active'],
    "order_by" => "product_tbl_record_time DESC, product_id DESC",
    "limit" => $limit,
    "offset" => $offset,
    'left_join' => [
        'categories' => ' ON products.category_id = categories.categoryTbl_id'
    ]
]);

$cartItems = $cart->getCartItems(); // get all current cart items

// Build lookup for cart items
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
        $displayPrices = qs_product_prices($product);
                        $priceNew = number_format($displayPrices['current'], 2);
        $priceOld = $displayPrices['original'] !== null ? number_format($displayPrices['original'], 2) : '';

        // Check if product is in cart
        $inCart = isset($cartLookup[$productId]);
        $cartItemId = $inCart ? $cartLookup[$productId] : '';

        // Button state
        $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
        $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
        $cartBtnIcon = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';

        include "../../view/inc/productCard.php";
    }
} else {
    echo ""; // no more products
}
?>
