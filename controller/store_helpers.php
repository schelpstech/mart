<?php

if (!function_exists('qs_money')) {
    function qs_money($amount)
    {
        return '£' . number_format((float)$amount, 2);
    }
}

if (!function_exists('qs_delivery_defaults')) {
    function qs_delivery_defaults()
    {
        return [
            'id' => 0,
            'delivery_enabled' => 1,
            'pickup_enabled' => 1,
            'default_delivery_fee' => 10.00,
            'free_delivery_minimum' => 100.00,
            'pickup_address' => '10 London Street, Larkhall, ML9 1AG',
            'pickup_instruction' => 'Pickup is available during store opening hours. Bring your order confirmation when collecting.'
        ];
    }
}

if (!function_exists('qs_get_delivery_settings')) {
    function qs_get_delivery_settings($model)
    {
        $defaults = qs_delivery_defaults();

        try {
            $row = $model->getRows('delivery_settings', [
                'where' => ['id' => 1],
                'return_type' => 'single'
            ]);

            if (!$row) {
                return $defaults;
            }

            return array_merge($defaults, $row);
        } catch (Exception $e) {
            return $defaults;
        }
    }
}

if (!function_exists('qs_get_delivery_zones')) {
    function qs_get_delivery_zones($model, $activeOnly = true)
    {
        try {
            $params = ['order_by' => 'zone_name ASC'];
            if ($activeOnly) {
                $params['where'] = ['status' => 'active'];
            }

            return $model->getRows('delivery_zones', $params) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('qs_get_delivery_zone')) {
    function qs_get_delivery_zone($model, $zoneId)
    {
        $zoneId = (int)$zoneId;
        if ($zoneId <= 0) {
            return null;
        }

        try {
            return $model->getRows('delivery_zones', [
                'where' => ['zone_id' => $zoneId],
                'return_type' => 'single'
            ]) ?: null;
        } catch (Exception $e) {
            return null;
        }
    }
}

if (!function_exists('qs_normalize_fulfilment')) {
    function qs_normalize_fulfilment($fulfilment, array $settings, $hasProducts = false, $hasServices = false)
    {
        $fulfilment = strtolower(trim((string)$fulfilment));
        $deliveryEnabled = !empty($settings['delivery_enabled']);
        $pickupEnabled = !empty($settings['pickup_enabled']);

        if (!in_array($fulfilment, ['delivery', 'pickup'], true)) {
            $fulfilment = ($hasProducts && $deliveryEnabled) ? 'delivery' : 'pickup';
        }

        if ($fulfilment === 'delivery' && !$deliveryEnabled && $pickupEnabled) {
            return 'pickup';
        }

        if ($fulfilment === 'pickup' && !$pickupEnabled && $deliveryEnabled) {
            return 'delivery';
        }

        return in_array($fulfilment, ['delivery', 'pickup'], true) ? $fulfilment : 'delivery';
    }
}

if (!function_exists('qs_delivery_fee')) {
    function qs_delivery_fee($model, $fulfilment, $subtotal, $zoneId = null)
    {
        if ($fulfilment !== 'delivery') {
            return 0.00;
        }

        $settings = qs_get_delivery_settings($model);
        $fee = (float)$settings['default_delivery_fee'];
        $zone = qs_get_delivery_zone($model, $zoneId);

        if ($zone && strtolower((string)$zone['status']) === 'active') {
            $fee = (float)$zone['delivery_fee'];
        }

        $freeMinimum = (float)$settings['free_delivery_minimum'];
        if ($freeMinimum > 0 && (float)$subtotal >= $freeMinimum) {
            return 0.00;
        }

        return max(0.00, $fee);
    }
}

if (!function_exists('qs_validate_coupon')) {
    function qs_validate_coupon($model, $code, $subtotal)
    {
        $code = strtoupper(trim((string)$code));
        $subtotal = max(0.00, (float)$subtotal);

        if ($code === '') {
            return [
                'valid' => true,
                'message' => '',
                'coupon' => null,
                'discount' => 0.00,
                'code' => ''
            ];
        }

        try {
            $coupon = $model->getRows('coupons', [
                'where' => ['code' => $code],
                'return_type' => 'single'
            ]);
        } catch (Exception $e) {
            return [
                'valid' => false,
                'message' => 'Coupon support is not installed yet.',
                'coupon' => null,
                'discount' => 0.00,
                'code' => $code
            ];
        }

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Invalid coupon code.',
                'coupon' => null,
                'discount' => 0.00,
                'code' => $code
            ];
        }

        if (strtolower((string)$coupon['status']) !== 'active') {
            return [
                'valid' => false,
                'message' => 'This coupon is not active.',
                'coupon' => $coupon,
                'discount' => 0.00,
                'code' => $code
            ];
        }

        $now = time();
        if (!empty($coupon['start_date']) && strtotime($coupon['start_date']) > $now) {
            return [
                'valid' => false,
                'message' => 'This coupon is not available yet.',
                'coupon' => $coupon,
                'discount' => 0.00,
                'code' => $code
            ];
        }

        if (!empty($coupon['expiry_date']) && strtotime($coupon['expiry_date']) < $now) {
            return [
                'valid' => false,
                'message' => 'This coupon has expired.',
                'coupon' => $coupon,
                'discount' => 0.00,
                'code' => $code
            ];
        }

        $minimum = (float)$coupon['minimum_order_amount'];
        if ($minimum > 0 && $subtotal < $minimum) {
            return [
                'valid' => false,
                'message' => 'This coupon requires a minimum order of ' . qs_money($minimum) . '.',
                'coupon' => $coupon,
                'discount' => 0.00,
                'code' => $code
            ];
        }

        if ($coupon['usage_limit'] !== null && $coupon['usage_limit'] !== '' && (int)$coupon['used_count'] >= (int)$coupon['usage_limit']) {
            return [
                'valid' => false,
                'message' => 'This coupon has reached its usage limit.',
                'coupon' => $coupon,
                'discount' => 0.00,
                'code' => $code
            ];
        }

        $value = max(0.00, (float)$coupon['discount_value']);
        if ($coupon['discount_type'] === 'percentage') {
            $discount = $subtotal * min($value, 100) / 100;
        } else {
            $discount = $value;
        }

        $discount = min($subtotal, max(0.00, $discount));

        return [
            'valid' => true,
            'message' => 'Coupon applied.',
            'coupon' => $coupon,
            'discount' => round($discount, 2),
            'code' => $code
        ];
    }
}

if (!function_exists('qs_calculate_order_totals')) {
    function qs_calculate_order_totals($cart, $model, $fulfilment = '', $zoneId = null, $couponCode = '')
    {
        $summary = $cart->getCartSummary();
        $settings = qs_get_delivery_settings($model);
        $fulfilment = qs_normalize_fulfilment(
            $fulfilment,
            $settings,
            !empty($summary['product_items']),
            !empty($summary['service_items'])
        );

        $deliveryFee = qs_delivery_fee($model, $fulfilment, $summary['subtotal'], $zoneId);
        $coupon = qs_validate_coupon($model, $couponCode, $summary['subtotal']);
        $discount = $coupon['valid'] ? (float)$coupon['discount'] : 0.00;
        $total = max(0.00, (float)$summary['subtotal'] + $deliveryFee - $discount);
        $zone = qs_get_delivery_zone($model, $zoneId);

        return [
            'subtotal' => round((float)$summary['subtotal'], 2),
            'delivery_fee' => round($deliveryFee, 2),
            'discount' => round($discount, 2),
            'total' => round($total, 2),
            'fulfilment_type' => $fulfilment,
            'delivery_zone_id' => $zone ? (int)$zone['zone_id'] : null,
            'delivery_location' => $zone ? $zone['zone_name'] : null,
            'settings' => $settings,
            'coupon' => $coupon,
            'items' => $summary['items'],
            'product_items' => $summary['product_items'],
            'service_items' => $summary['service_items'],
            'count' => $summary['count']
        ];
    }
}

if (!function_exists('qs_order_financials')) {
    function qs_order_financials(array $order, $itemsSubtotal)
    {
        $itemsSubtotal = round((float)$itemsSubtotal, 2);
        $discount = array_key_exists('discount_amount', $order) && $order['discount_amount'] !== null
            ? (float)$order['discount_amount']
            : 0.00;
        $storedSubtotal = array_key_exists('subtotal_amount', $order) && $order['subtotal_amount'] !== null
            ? (float)$order['subtotal_amount']
            : 0.00;
        $subtotal = $storedSubtotal > 0 ? $storedSubtotal : $itemsSubtotal;

        $storedDelivery = array_key_exists('delivery_fee', $order) && $order['delivery_fee'] !== null
            ? (float)$order['delivery_fee']
            : null;
        $hasNewTotals = $storedSubtotal > 0 || $discount > 0 || strtolower($order['fulfilment_type'] ?? '') === 'pickup';
        $deliveryFee = ($storedDelivery !== null && ($storedDelivery > 0 || $hasNewTotals))
            ? $storedDelivery
            : max(0.00, (float)$order['total_amount'] - $itemsSubtotal);

        return [
            'subtotal' => round($subtotal, 2),
            'delivery_fee' => round($deliveryFee, 2),
            'discount' => round($discount, 2),
            'total' => round((float)$order['total_amount'], 2)
        ];
    }
}

if (!function_exists('qs_fulfilment_label')) {
    function qs_fulfilment_label($fulfilment)
    {
        return strtolower((string)$fulfilment) === 'pickup' ? 'Pickup' : 'Delivery';
    }
}

if (!function_exists('qs_record_coupon_usage')) {
    function qs_record_coupon_usage($model, array $order)
    {
        if (empty($order['coupon_id']) || empty($order['coupon_code']) || (float)($order['discount_amount'] ?? 0) <= 0) {
            return false;
        }

        try {
            $existing = $model->getRows('coupon_usage', [
                'where' => ['order_id' => $order['order_tbl_id']],
                'return_type' => 'single'
            ]);

            if ($existing) {
                return true;
            }

            $model->insert('coupon_usage', [
                'coupon_id' => $order['coupon_id'],
                'order_id' => $order['order_tbl_id'],
                'user_id' => $order['user_id'],
                'coupon_code' => $order['coupon_code'],
                'discount_amount' => $order['discount_amount']
            ]);

            $stmt = $model->prepare('UPDATE coupons SET used_count = used_count + 1 WHERE coupon_id = :coupon_id');
            $stmt->execute([':coupon_id' => $order['coupon_id']]);
            return true;
        } catch (Exception $e) {
            error_log('Coupon usage record failed: ' . $e->getMessage());
            return false;
        }
    }
}
