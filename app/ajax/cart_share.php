<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once '../query.php';
require_once '../../controller/whatsapp_helpers.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        throw new InvalidArgumentException('Please use the cart share button.');
    }
    // This endpoint reads only the current session's cart and never creates an order.
    $summary = $cart->getCartSummary();
    $totals = null;
    $fulfilment = strtolower(trim((string)($_POST['fulfilment_type'] ?? '')));
    $couponCode = trim((string)($_POST['coupon_code'] ?? ''));
    $hasProducts = !empty($summary['product_items']);
    if (($_POST['context'] ?? '') === 'checkout' && (!$hasProducts || in_array($fulfilment, ['delivery', 'pickup'], true))) {
        $settings = qs_get_delivery_settings($model);
        if ($hasProducts && empty($settings[$fulfilment . '_enabled'])) throw new InvalidArgumentException('This fulfilment option is currently unavailable. Please select another option.');
        $zoneId = (int)($_POST['delivery_zone_id'] ?? 0);
        if ($fulfilment === 'delivery' && $zoneId > 0) {
            $zone = qs_get_delivery_zone($model, $zoneId);
            if (!$zone || strtolower($zone['status']) !== 'active') throw new InvalidArgumentException('Please select an available delivery location.');
        }
        $totals = qs_calculate_order_totals($cart, $model, $fulfilment, $zoneId, $couponCode);
        if (!$totals['coupon']['valid']) throw new InvalidArgumentException($totals['coupon']['message']);
        $summary['items'] = $totals['items'];
    } elseif ($couponCode !== '') {
        throw new InvalidArgumentException('Choose delivery or pickup to include your coupon and final checkout total.');
    }
    $date = trim((string)($_POST['appointment_date'] ?? ''));
    $time = trim((string)($_POST['appointment_time'] ?? ''));
    if ($date !== '' || $time !== '') {
        $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) throw new InvalidArgumentException('Enter a valid appointment date and time before sharing.');
    }
    $message = qs_whatsapp_cart_message($summary, $totals, ['date' => $date, 'time' => $time]);
    $chatUrl = 'https://wa.me/' . qs_whatsapp_number();
    $url = $chatUrl . '?text=' . rawurlencode($message);
    $copyRequired = strlen($url) > 7000;
    echo json_encode(['status' => 'success', 'message' => $message, 'url' => $copyRequired ? $chatUrl : $url, 'copy_required' => $copyRequired, 'count' => $summary['count']], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (InvalidArgumentException $e) {
    if (http_response_code() !== 405) http_response_code(422);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Cart sharing failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'We could not load your cart. Please refresh and try again.']);
}
