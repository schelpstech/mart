<?php
header('Content-Type: application/json');
require_once '../query.php';

$summary = $cart->getCartSummary();
$output = '';

if (!empty($summary['items'])) {
    foreach ($summary['items'] as $item) {
        $name = htmlspecialchars($item['name'] ?? 'Item', ENT_QUOTES, 'UTF-8');
        $image = htmlspecialchars($item['image'] ?? '../view/assets/images/product/main/default.png', ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($item['url'] ?? 'viewcart.php', ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($item['item_type'] ?? 'product', ENT_QUOTES, 'UTF-8');
        $itemId = (int)($item['item_id'] ?? $item['cart_item_id']);
        $quantity = (int)($item['quantity'] ?? 1);

        $output .= '
        <li>
            <a href="' . $url . '" class="sidecart_pro_img">
                <img src="' . $image . '" alt="' . $name . '">
            </a>
            <div class="ec-pro-content">
                <a href="' . $url . '" class="cart_pro_title">' . $name . '</a>
                <small class="cart-item-type">' . ucfirst($type) . '</small>
                <span class="cart-price"><span>' . qs_money($item['price'] ?? 0) . '</span> x ' . $quantity . '</span>
                <a href="javascript:void(0)" class="removed" data-cartitemid="' . $itemId . '" data-itemtype="' . $type . '">remove</a>
            </div>
        </li>';
    }
}

echo json_encode([
    'html' => $output ?: '<li><p class="emp-cart-msg">Your cart is empty!</p></li>',
    'subTotal' => qs_money($summary['subtotal']),
    'vat' => qs_money(0),
    'total' => qs_money($summary['subtotal']),
    'count' => $summary['count']
]);
