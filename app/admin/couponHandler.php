<?php
require_once "../query.php";

function coupon_redirect()
{
    header("Location: ../../console/coupon_mgr.php");
    exit;
}

function coupon_datetime($value)
{
    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }
    return str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $utility->setFlash("danger", "Invalid coupon action.");
    coupon_redirect();
}

$action = $utility->inputDecode($_POST['action'] ?? '');
$code = strtoupper(trim($_POST['code'] ?? ''));
$description = trim($_POST['description'] ?? '');
$discountType = $_POST['discount_type'] ?? 'fixed';
$discountValue = (float)($_POST['discount_value'] ?? 0);
$minimumOrder = (float)($_POST['minimum_order_amount'] ?? 0);
$startDate = coupon_datetime($_POST['start_date'] ?? '');
$expiryDate = coupon_datetime($_POST['expiry_date'] ?? '');
$usageLimit = trim($_POST['usage_limit'] ?? '') === '' ? null : max(1, (int)$_POST['usage_limit']);
$status = strtolower($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

try {
    if (in_array($action, ['add_coupon', 'update_coupon'], true)) {
        if (!preg_match('/^[A-Z0-9_-]{3,50}$/', $code)) {
            $utility->setFlash("danger", "Coupon code must be 3-50 characters and use letters, numbers, hyphen, or underscore.");
            coupon_redirect();
        }

        if (!in_array($discountType, ['fixed', 'percentage'], true) || $discountValue <= 0) {
            $utility->setFlash("danger", "Enter a valid discount type and value.");
            coupon_redirect();
        }

        if ($discountType === 'percentage' && $discountValue > 100) {
            $utility->setFlash("danger", "Percentage discount cannot exceed 100%.");
            coupon_redirect();
        }

        $fields = [
            'code' => $code,
            'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'minimum_order_amount' => max(0, $minimumOrder),
            'start_date' => $startDate,
            'expiry_date' => $expiryDate,
            'usage_limit' => $usageLimit,
            'status' => $status
        ];

        if ($action === 'add_coupon') {
            if ($model->exists('coupons', ['code' => $code])) {
                $utility->setFlash("warning", "Coupon code already exists.");
                coupon_redirect();
            }

            $model->insert('coupons', $fields);
            $utility->setFlash("success", "Coupon created successfully.");
            coupon_redirect();
        }

        $couponId = (int)($_POST['coupon_id'] ?? 0);
        $existing = $model->getRows('coupons', [
            'where' => ['coupon_id' => $couponId],
            'return_type' => 'single'
        ]);

        if (!$existing) {
            $utility->setFlash("danger", "Coupon not found.");
            coupon_redirect();
        }

        $duplicate = $model->getRows('coupons', [
            'where' => ['code' => $code],
            'return_type' => 'single'
        ]);
        if ($duplicate && (int)$duplicate['coupon_id'] !== $couponId) {
            $utility->setFlash("warning", "Coupon code already exists.");
            coupon_redirect();
        }

        $model->update('coupons', $fields, ['coupon_id' => $couponId]);
        $utility->setFlash("success", "Coupon updated successfully.");
        coupon_redirect();
    }

    if ($action === 'toggle_coupon') {
        $couponId = (int)($_POST['coupon_id'] ?? 0);
        $coupon = $model->getRows('coupons', [
            'where' => ['coupon_id' => $couponId],
            'return_type' => 'single'
        ]);

        if (!$coupon) {
            $utility->setFlash("danger", "Coupon not found.");
            coupon_redirect();
        }

        $newStatus = $coupon['status'] === 'active' ? 'inactive' : 'active';
        $model->update('coupons', ['status' => $newStatus], ['coupon_id' => $couponId]);
        $utility->setFlash("success", "Coupon status updated.");
        coupon_redirect();
    }

    if ($action === 'delete_coupon') {
        $couponId = (int)($_POST['coupon_id'] ?? 0);
        $model->update('coupons', ['status' => 'inactive'], ['coupon_id' => $couponId]);
        $utility->setFlash("success", "Coupon deactivated safely.");
        coupon_redirect();
    }
} catch (Exception $e) {
    $utility->setFlash("danger", "Coupon error: " . $e->getMessage());
    coupon_redirect();
}

$utility->setFlash("danger", "Invalid coupon action.");
coupon_redirect();
