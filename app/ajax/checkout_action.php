<?php
header('Content-Type: application/json');
require_once '../query.php';

$action = $_POST['action'] ?? '';

if ($action !== 'calculate_totals') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid action.']);
    exit;
}

$fulfilment = $_POST['fulfilment_type'] ?? '';
$zoneId = $_POST['delivery_zone_id'] ?? null;
$couponCode = $_POST['coupon_code'] ?? '';
$summary = $cart->getCartSummary();
$hasProducts = !empty($summary['product_items']);
$validFulfilments = ['delivery', 'pickup'];
$needsFulfilmentChoice = $hasProducts && !in_array(strtolower(trim((string)$fulfilment)), $validFulfilments, true);
$totalsFulfilment = $needsFulfilmentChoice ? 'pickup' : $fulfilment;
$totals = qs_calculate_order_totals($cart, $model, $totalsFulfilment, $zoneId, $couponCode);

if (!$totals['coupon']['valid']) {
    echo json_encode([
        'status' => 'error',
        'msg' => $totals['coupon']['message'],
        'subtotal' => qs_money($totals['subtotal']),
        'delivery_fee' => qs_money($totals['delivery_fee']),
        'discount' => qs_money(0),
        'total' => qs_money($totals['subtotal'] + $totals['delivery_fee'])
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'msg' => $needsFulfilmentChoice ? 'Please choose delivery or pickup before payment.' : $totals['coupon']['message'],
    'fulfilment_type' => $needsFulfilmentChoice ? '' : $totals['fulfilment_type'],
    'subtotal' => qs_money($totals['subtotal']),
    'delivery_fee' => qs_money($totals['delivery_fee']),
    'discount' => qs_money($totals['discount']),
    'total' => qs_money($totals['total'])
]);
