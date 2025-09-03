<?php
include './inc/head.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
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

    <div class="container my-4">
        <h4>Search Results for: "<?= htmlspecialchars($q) ?>"</h4>
        <div class="row" id="product-container">

            <?php
            if ($q !== '') {
                // Search in product_name and description
                $products = $model->search('products', [
                    'columns' => ['product_name', 'description'],
                    'keyword' => $q,
                    'order_by' => 'product_tbl_record_time DESC'
                ]);

                if ($products) {
                    // Build cart lookup (for Add to Cart button)
                    $cartItems = $cart->getCartItems();
                    $cartLookup = [];
                    if ($cartItems) {
                        foreach ($cartItems as $c) {
                            $cartLookup[$c['product_id']] = $c['cart_item_id'];
                        }
                    }

                    foreach ($products as $product) {

                        $productName = htmlspecialchars($product['product_name']);
                        $productId = htmlspecialchars($product['product_id']);
                        $productImage = htmlspecialchars($product['image_main']);
                        $productCategory = htmlspecialchars($product['category_name'] ?? '');
                        $categoryId = htmlspecialchars($product['category_id'] ?? '');
                        $priceNew = number_format($product['price'], 2);
                        $priceOld = $product['discount_price'] ? number_format($product['discount_price'], 2) : '';

                        $inCart = isset($cartLookup[$productId]);
                        $cartItemId = $inCart ? $cartLookup[$productId] : '';
                        $cartBtnClass = $inCart ? 'add-to-cart in-cart' : 'add-to-cart';
                        $cartBtnTitle = $inCart ? 'Remove From Cart' : 'Add To Cart';
                        $cartBtnIcon  = $inCart ? 'fi-rr-trash' : 'fi-rr-shopping-basket';

                        include "./inc/productCard.php"; // ✅ reuse your product card template
                    }
                } else {
                    echo "<p>No products found matching your search.</p>";
                }
            } else {
                echo "<p>Please enter a search keyword.</p>";
            }
            ?>
        </div>
    </div>

    <?php include "./inc/footer.php"; ?>