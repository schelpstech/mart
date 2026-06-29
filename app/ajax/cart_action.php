<?php
header('Content-Type: application/json');
require_once '../query.php';

function cart_json_summary($cart)
{
    $summary = $cart->getCartSummary();
    return [
        'count' => $summary['count'],
        'subTotal' => qs_money($summary['subtotal']),
        'total' => qs_money($summary['subtotal']),
        'cart_subtotal' => qs_money($summary['subtotal']),
        'cart_grandtotal' => qs_money($summary['subtotal'])
    ];
}

function cart_side_html(array $items)
{
    if (empty($items)) {
        return '<li><p class="emp-cart-msg">Your cart is empty!</p></li>';
    }

    $output = '';
    foreach ($items as $item) {
        $name = htmlspecialchars($item['name'] ?? 'Item', ENT_QUOTES, 'UTF-8');
        $image = htmlspecialchars($item['image'] ?? '../view/assets/images/product/main/default.png', ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($item['url'] ?? 'viewcart.php', ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($item['item_type'] ?? 'product', ENT_QUOTES, 'UTF-8');
        $itemId = (int)($item['item_id'] ?? $item['cart_item_id']);
        $price = qs_money($item['price'] ?? 0);
        $quantity = (int)($item['quantity'] ?? 1);
        $label = $type === 'service' ? '<small class="cart-item-type">Service</small>' : '<small class="cart-item-type">Product</small>';

        $output .= '
        <li>
            <a href="' . $url . '" class="sidecart_pro_img">
                <img src="' . $image . '" alt="' . $name . '">
            </a>
            <div class="ec-pro-content">
                <a href="' . $url . '" class="cart_pro_title">' . $name . '</a>
                ' . $label . '
                <span class="cart-price"><span>' . $price . '</span> x ' . $quantity . '</span>
                <a href="javascript:void(0)" class="removed" data-cartitemid="' . $itemId . '" data-itemtype="' . $type . '">remove</a>
            </div>
        </li>';
    }

    return $output;
}

$action = $_POST['action'] ?? null;
$response = ["status" => "error", "msg" => "Invalid action"];

if ($action === "add") {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    $product = $model->getRows("products", [
        "where" => ["product_id" => $product_id, "status" => "Active"],
        "return_type" => "single"
    ]);

    if (!$product) {
        echo json_encode(["status" => "error", "msg" => "Product not found"]);
        exit;
    }

    $price = !empty($product['discount_price']) ? (float)$product['discount_price'] : (float)$product['price'];
    $item_id = $cart->addToCart($product_id, $quantity, $price);
    $summary = cart_json_summary($cart);

    echo json_encode(array_merge([
        "status" => "success",
        "msg" => "Added to cart",
        "item_id" => $item_id
    ], $summary));
    exit;
}

if ($action === "count") {
    echo json_encode(array_merge(["status" => "success"], cart_json_summary($cart)));
    exit;
}

if ($action === "get_cart_items") {
    $productItems = $cart->getCartItems();
    $summary = $cart->getCartSummary();
    echo json_encode([
        'status' => 'success',
        'items' => array_map(function ($item) {
            return [
                'product_id' => (int)$item['product_id'],
                'cart_item_id' => (int)$item['cart_item_id'],
                'item_type' => 'product'
            ];
        }, $productItems),
        'all_items' => $summary['items'],
        'count' => $summary['count']
    ]);
    exit;
}

if ($action === "get_cart_html") {
    echo cart_side_html($cart->getAllCartItems());
    exit;
}

if ($action === 'remove') {
    $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
    $itemType = strtolower(trim($_POST['item_type'] ?? 'product')) === 'service' ? 'service' : 'product';

    if ($cartItemId && $cart->removeFromCart($cartItemId, $itemType)) {
        echo json_encode(array_merge([
            'status' => 'success',
            'msg' => 'Item removed from cart'
        ], cart_json_summary($cart)));
        exit;
    }

    echo json_encode([
        'status' => 'error',
        'msg' => 'Failed to remove item.'
    ]);
    exit;
}

if ($action === "update_quantity") {
    $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $itemType = strtolower(trim($_POST['item_type'] ?? 'product')) === 'service' ? 'service' : 'product';

    if (!$cartItemId) {
        echo json_encode(["status" => "error", "msg" => "Invalid item."]);
        exit;
    }

    if (!$cart->updateQuantity($cartItemId, $quantity, $itemType)) {
        echo json_encode([
            "status" => "error",
            "msg" => "Cart item was not found."
        ]);
        exit;
    }

    $line = $cart->getLineItem($cartItemId, $itemType);
    $lineTotal = $line ? (float)$line['quantity'] * (float)$line['price'] : 0.00;

    echo json_encode(array_merge([
        "status" => "success",
        "msg" => "Quantity updated successfully",
        "line_total" => qs_money($lineTotal)
    ], cart_json_summary($cart)));
    exit;
}

echo json_encode($response);
exit;
