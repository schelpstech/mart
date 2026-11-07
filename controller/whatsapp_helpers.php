<?php

function qs_whatsapp_number()
{
    return '447767918847';
}

function qs_whatsapp_label($value)
{
    return trim(preg_replace('/[\s\x00-\x1f\x7f]+/u', ' ', strip_tags(html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));
}

function qs_whatsapp_cart_message(array $summary, $totals = null, array $appointment = [])
{
    if (empty($summary['items'])) throw new InvalidArgumentException('Your cart is empty. Add an item before sharing.');
    $lines = ['Hello Queenzy, please confirm availability for my cart:', ''];
    $subtotal = 0.00;
    $hasProducts = false;
    $hasServices = false;
    foreach ($summary['items'] as $index => $item) {
        $quantity = max(1, (int)$item['quantity']);
        $price = max(0.00, (float)$item['price']);
        $lineTotal = round($price * $quantity, 2);
        $subtotal += $lineTotal;
        $service = ($item['item_type'] ?? 'product') === 'service';
        $hasProducts = $hasProducts || !$service;
        $hasServices = $hasServices || $service;
        $lines[] = ($index + 1) . '. ' . qs_whatsapp_label($item['name']) . ($service ? ' (Service)' : '');
        if (!empty($item['sku'])) $lines[] = 'SKU: ' . qs_whatsapp_label($item['sku']);
        $lines[] = 'Quantity: ' . $quantity;
        $lines[] = 'Unit price: ' . qs_money($price);
        $lines[] = 'Line total: ' . qs_money($lineTotal);
        $lines[] = '';
    }
    $lines[] = 'Items total: ' . qs_money($subtotal);
    if ($totals !== null) {
        if ($hasProducts) {
            $lines[] = 'Fulfilment: ' . qs_fulfilment_label($totals['fulfilment_type']);
            if ($totals['fulfilment_type'] === 'delivery' && !empty($totals['delivery_location'])) $lines[] = 'Delivery zone: ' . qs_whatsapp_label($totals['delivery_location']);
            $lines[] = 'Delivery fee: ' . qs_money($totals['delivery_fee']);
        }
        if ((float)$totals['discount'] > 0) $lines[] = 'Discount' . (!empty($totals['coupon']['code']) ? ' (' . qs_whatsapp_label($totals['coupon']['code']) . ')' : '') . ': -' . qs_money($totals['discount']);
        $lines[] = 'Total: ' . qs_money($totals['total']);
    } elseif ($hasProducts) {
        $lines[] = 'Delivery / pickup: to be confirmed.';
        $lines[] = 'Delivery charges and any discounts will be confirmed at checkout.';
    } else {
        $lines[] = 'Total: ' . qs_money($subtotal);
    }
    if ($hasServices) {
        $lines[] = !empty($appointment['date']) && !empty($appointment['time'])
            ? 'Requested appointment: ' . $appointment['date'] . ' at ' . $appointment['time']
            : 'Appointment date / time: to be confirmed.';
    }
    $lines[] = '';
    $lines[] = 'Please confirm these items and the final amount before I pay.';
    return implode("\n", $lines);
}
