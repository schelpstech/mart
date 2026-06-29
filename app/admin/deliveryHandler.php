<?php
require_once "../query.php";

function delivery_redirect()
{
    header("Location: ../../console/delivery_mgr.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $utility->setFlash("danger", "Invalid delivery action.");
    delivery_redirect();
}

$action = $utility->inputDecode($_POST['action'] ?? '');

try {
    if ($action === 'update_delivery_settings') {
        $fields = [
            'delivery_enabled' => isset($_POST['delivery_enabled']) ? 1 : 0,
            'pickup_enabled' => isset($_POST['pickup_enabled']) ? 1 : 0,
            'default_delivery_fee' => max(0, (float)($_POST['default_delivery_fee'] ?? 0)),
            'free_delivery_minimum' => max(0, (float)($_POST['free_delivery_minimum'] ?? 0)),
            'pickup_address' => htmlspecialchars(trim($_POST['pickup_address'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'pickup_instruction' => htmlspecialchars(trim($_POST['pickup_instruction'] ?? ''), ENT_QUOTES, 'UTF-8')
        ];

        if (!$fields['delivery_enabled'] && !$fields['pickup_enabled']) {
            $utility->setFlash("danger", "At least one fulfilment option must be enabled.");
            delivery_redirect();
        }

        $existing = $model->getRows('delivery_settings', [
            'where' => ['id' => 1],
            'return_type' => 'single'
        ]);

        if ($existing) {
            $model->update('delivery_settings', $fields, ['id' => 1]);
        } else {
            $model->insert('delivery_settings', $fields);
        }

        $utility->setFlash("success", "Delivery settings updated.");
        delivery_redirect();
    }

    if (in_array($action, ['add_delivery_zone', 'update_delivery_zone'], true)) {
        $zoneName = trim($_POST['zone_name'] ?? '');
        $fee = max(0, (float)($_POST['delivery_fee'] ?? 0));
        $status = strtolower($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

        if ($zoneName === '') {
            $utility->setFlash("danger", "Zone name is required.");
            delivery_redirect();
        }

        $fields = [
            'zone_name' => htmlspecialchars($zoneName, ENT_QUOTES, 'UTF-8'),
            'delivery_fee' => $fee,
            'status' => $status
        ];

        if ($action === 'add_delivery_zone') {
            $model->insert('delivery_zones', $fields);
            $utility->setFlash("success", "Delivery zone created.");
            delivery_redirect();
        }

        $zoneId = (int)($_POST['zone_id'] ?? 0);
        $model->update('delivery_zones', $fields, ['zone_id' => $zoneId]);
        $utility->setFlash("success", "Delivery zone updated.");
        delivery_redirect();
    }

    if ($action === 'toggle_delivery_zone') {
        $zoneId = (int)($_POST['zone_id'] ?? 0);
        $zone = $model->getRows('delivery_zones', [
            'where' => ['zone_id' => $zoneId],
            'return_type' => 'single'
        ]);

        if (!$zone) {
            $utility->setFlash("danger", "Delivery zone not found.");
            delivery_redirect();
        }

        $newStatus = $zone['status'] === 'active' ? 'inactive' : 'active';
        $model->update('delivery_zones', ['status' => $newStatus], ['zone_id' => $zoneId]);
        $utility->setFlash("success", "Delivery zone status updated.");
        delivery_redirect();
    }
} catch (Exception $e) {
    $utility->setFlash("danger", "Delivery management error: " . $e->getMessage());
    delivery_redirect();
}

$utility->setFlash("danger", "Invalid delivery action.");
delivery_redirect();
