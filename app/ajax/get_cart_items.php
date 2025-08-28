<?php
header('Content-Type: application/json');
require_once '../query.php'; // adjust path

$cartItems = $cart->getCartItems();
$subTotal = 0;
$output = '';

if ($cartItems) {
    foreach ($cartItems as $item) {
        $lineTotal = $item['price'] * $item['quantity'];
        $subTotal += $lineTotal;

        $output .= '
        <li>
            <a href="product.php?id='.$item['cart_id'].'" class="sidecart_pro_img">
                <img src="assets/images/product/'.htmlspecialchars($item['image_main']).'" alt="'.htmlspecialchars($item['name']).'">
            </a>
            <div class="ec-pro-content">
                <a href="product.php?id='.$item['cart_id'].'" class="cart_pro_title">'.htmlspecialchars($item['name']).'</a>
                <span class="cart-price"><span>£'.number_format($item['price'],2).'</span> x '.$item['quantity'].'</span>
                
                <a href="javascript:void(0)" class="removed" data-cartitemid="'.$item['cart_item_id'].'">remove</a>
            </div>
        </li>';
    }
}

$vat =  0;
$total = $subTotal + $vat;

echo json_encode([
    'html'      => $output,
    'subTotal'  => '£'.number_format($subTotal,2),
    'vat'       => '£'.number_format($vat,2),
    'total'     => '£'.number_format($total,2)
]);
