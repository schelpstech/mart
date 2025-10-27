<?php
require_once "../query.php";

$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Step 1️⃣: Get all product IDs
$productIDs = $model->getRows("products", [
    "columns" => ["product_id"]
]);

if ($productIDs && count($productIDs) > 0) {
    // Step 2️⃣: Extract just the IDs into an array
    $ids = array_column($productIDs, "product_id");

    // Step 3️⃣: Shuffle for random order
    shuffle($ids);

    // Step 4️⃣: Slice according to limit and offset
    $selectedIDs = array_slice($ids, $offset, $limit);

    // Step 5️⃣: Prepare IN clause
    $idList = implode(",", array_map('intval', $selectedIDs));

    // Step 6️⃣: Get full product details for the selected IDs
    $allproducts = $model->getRows("products", [
        "where" => "product_id IN ($idList)",
        'left_join' => [
            'categories' => ' ON products.category_id = categories.categoryTbl_id'
        ]
    ]);

    // Maintain original random order (MySQL IN doesn’t preserve order)
    usort($allproducts, function ($a, $b) use ($selectedIDs) {
        return array_search($a['product_id'], $selectedIDs) - array_search($b['product_id'], $selectedIDs);
    });

} else {
    $allproducts = [];
}

// Step 7️⃣: Get user cart items
$cartItems = $cart->getCartItems();
$cartLookup = [];
if ($cartItems) {
    foreach ($cartItems as $c) {
        $cartLookup[$c['product_id']] = $c['cart_item_id'];
    }
}

// Step 8️⃣: Display the randomized products
if ($allproducts) {
    foreach ($allproducts as $product) {
        $productName = htmlspecialchars($product['product_name']);
        $productId = htmlspecialchars($product['product_id']);
        $productImage = htmlspecialchars($product['image_main']);
        $productCategory = htmlspecialchars($product['category_name'] ?? '');
        $categoryId = htmlspecialchars($product['category_id'] ?? '');
        $priceNew = number_format($product['price'], 2);
        $priceOld = $product['discount_price'] ? number_format($product['discount_price'], 2) : '';

        // Cart button handling
        $inCart = isset($cartLookup[$productId]);
        $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
        $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
        $cartBtnIcon = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';

        include "../../view/inc/productCard.php";
    }
} else {
    echo ""; // no products available
}
?>
