<?php
require_once "../query.php";

$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$allproducts = $model->getRows("products", [
    'where' => ["category_id" => $_SESSION['categoryId'], 'status' => 'Active'],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
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
        $displayPrices = qs_product_prices($product);
                        $priceNew = number_format($displayPrices['current'], 2);
        $priceOld = $displayPrices['original'] !== null ? number_format($displayPrices['original'], 2) : '';


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
