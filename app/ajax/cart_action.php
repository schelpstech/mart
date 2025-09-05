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

    $product = $model->getRows("products", [
        "where" => ["product_id" => $product_id],
        "return_type" => "single"
    ]);

    if ($product) {
        $price = $product['price'];

        // This now gives us the cart_item_id directly
        $addtocart = $cart->addToCart($product_id, $quantity, $price);
        $cartinfo = $cart->getCartItemID($product_id);
        $item_id = $cartinfo['cart_item_id'];
        $count = $cart->getCartCount();

        $response = [
            "status"       => "success",
            "msg"          => "Added to cart",
            "count"        => $count,
            "item_id" => $item_id
        ];
    } else {
        $response = ["status" => "error", "msg" => "Product not found"];
    }

    echo json_encode($response);
    exit;
}



if ($action === "count") {

    if (!empty($_SESSION['user_id'])) {
        $cartCount = $model->getRows("cart", [
            "where" => ["user_id" => $_SESSION['user_id']],
            "return_type" => "single"
        ]);
        if ($cartCount) {
            $cart_id = $cartCount['cart_id'];
            $countofCount = $model->getRows("cart_items", [
                "where" => ["cart_id" => $cart_id],
                "return_type" => "count"
            ]);
            if ($countofCount) {
                $response = ["status" => "success", "count" => $countofCount];
                echo json_encode($response);
                exit;
            }else{
                $response = ["status" => "success", "count" => 0];
                echo json_encode($response);
                exit;
            }
        } 
    } else {
        $count = $cart->getCartCount();
        $response = ["status" => "success", "count" => $count];
        echo json_encode($response);
        exit;
    }
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
    $cartItemId = intval($_POST['cart_item_id'] ?? null);

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
        $cartItemId = $_POST['cart_item_id'] ?? "nothing";
        $response = [
            'status'  => 'error',
            'message' => 'Failed to remove itemaa.' . $_POST['cart_item_id']
        ];
    }

    echo json_encode($response);
    exit;
}

if ($_POST['action'] === "update_quantity") {
    $cart_item_id = intval($_POST['cart_item_id']);
    $quantity = intval($_POST['quantity']);
    if ($cart_item_id && $quantity > 0) {
        // Update quantity
        $updateQty = $model->update("cart_items", ["quantity" => $quantity], ["cart_item_id" => $cart_item_id]);

        $line = $model->getRows("cart_items", [
            "where" => ["cart_item_id" => $cart_item_id],
            "return_type" => "single"
        ]);

        $linetotal = $line['quantity'] * $line['price'];

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
        echo json_encode([
            "status" => "success",
            "line_total" => '£' . number_format($linetotal, 2),
            "cart_subtotal" => '£' . number_format($subTotal, 2),
            "cart_grandtotal" => '£' . number_format($total, 2)
        ]);
    } else {
        echo json_encode(["status" => "error", "msg" => "Invalid update"]);
    }
    exit;
}

echo json_encode($response);
exit;
