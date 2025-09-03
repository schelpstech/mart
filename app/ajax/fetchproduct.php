<?php
require_once "../query.php";

$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Get products with offset + limit
$allproducts = $model->getRows("products", [
    "order_by" => "product_tbl_record_time DESC",
    "limit" => $limit, // ✅ MySQL LIMIT with offset
    "offset" => $offset, // ✅ MySQL LIMIT with offset
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

        include "../../view/inc/productCard.php";
?>


<?php
    }
} else {
    echo ""; // no more products
}
