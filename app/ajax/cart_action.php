<?php
header('Content-Type: application/json');
require_once '../query.php'; // adjust path
// Identify user or session
$session_id = session_id();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


$action = $_POST['action'] ?? null;

$response = ["status" => "error", "msg" => "Invalid action"];

if ($action === "add") {
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity   = intval($_POST['quantity'] ?? 1);

    // Fetch price from products table
    $product = $model->getRows("products", [
        "where" => ["product_id" => $product_id],
        "return_type" => "single"
    ]);

    if ($product) {
        $price = $product['price'];
        $additemtoCart = $cart->addToCart($product_id, $quantity, $price);
        if ($additemtoCart) {
            // Fetch cart_item_id from cart_items table
            $cartItem = $model->getRows("cart_items", [
                "where" => [
                    "cart_items.product_id" => $product_id,
                    "cart.session_id"       => $session_id
                ],
                "join" => [
                    "cart" => "ON cart.cart_id = cart_items.cart_id"
                ],
                "return_type" => "single"
            ]);
        }

        $count = $cart->getCartCount();
        $response = [
            "status" => "success",
            "msg" => "Added to cart",
            "count" => $count,
            "cart_item_id" => $cartItem['cart_item_id']
        ];
    } else {
        $response = ["status" => "error", "msg" => "Product not found"];
    }

    echo json_encode($response);
    exit;
}

if ($action === "count") {
    $count = $cart->getCartCount();
    $response = ["status" => "success", "count" => $count];
    echo json_encode($response);
    exit;
}

if ($_POST['action'] === 'get_cart_items') {
    $items = $cart->getCartItems(); // or session
    $ids = array_column($items, 'product_id');
    echo json_encode([
        'status' => 'success',
        'items'  => $ids
    ]);
    exit;
}


if ($action === 'remove') {
    $cartItemId = $_POST['cart_item_id'] ?? null;

    if ($cartItemId && $cart->removeFromCart($cartItemId)) {
        // Re-fetch items safely
        $items = $cart->getCartItems();
        if (!$items) {
            $items = [];
        }

        // Recalculate totals
        $subTotal = 0.00;
        foreach ($items as $item) {
            $subTotal += ((float)$item['price']) * ((int)$item['quantity']);
        }
        $vat   = $subTotal * 0.20;
        $total = $subTotal + $vat;

        $response = [
            'status'   => 'success',
            // If you prefer the "unique items" count, use count($items).
            // If you have a method that sums quantities, you can swap it in here.
            'count'    => count($items),
            'subTotal' => '£' . number_format($subTotal, 2),
            'vat'      => '£' . number_format($vat, 2),
            'total'    => '£' . number_format($total, 2),
        ];
    } else {
        $response = [
            'status'  => 'error',
            'message' => 'Failed to remove item.'
        ];
    }

    echo json_encode($response);
    exit;
}
echo json_encode($response);
exit;
