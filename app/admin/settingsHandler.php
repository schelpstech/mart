<?php
include "../query.php";
include_once "../../controller/admin_helpers.php";

function settings_redirect()
{
    header("Location: ../../console/settings.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $utility->setFlash("warning", "Restricted action.");
    settings_redirect();
}

$action = $utility->inputDecode($_POST['action'] ?? '');
if ($action !== 'save_settings') {
    $utility->setFlash("warning", "Unknown settings action.");
    settings_redirect();
}

if (!qs_admin_table_exists($model, 'site_settings')) {
    $utility->setFlash("danger", "Settings table is not installed. Run the admin menu integration migration.");
    settings_redirect();
}

$allowedKeys = [
    'site_name',
    'site_url',
    'support_email',
    'orders_email',
    'support_phone',
    'cart_expiry_days',
    'public_notice'
];

$settings = is_array($_POST['settings'] ?? null) ? $_POST['settings'] : [];

try {
    $stmt = $model->prepare("
        INSERT INTO site_settings (setting_key, setting_value, updated_at)
        VALUES (:setting_key, :setting_value, NOW())
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()
    ");

    foreach ($allowedKeys as $key) {
        $value = trim((string)($settings[$key] ?? ''));

        if (in_array($key, ['support_email', 'orders_email'], true) && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $utility->setFlash("danger", "Please enter valid email addresses.");
            settings_redirect();
        }

        if ($key === 'site_url' && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
            $utility->setFlash("danger", "Please enter a valid site URL.");
            settings_redirect();
        }

        if ($key === 'cart_expiry_days') {
            $value = (string)max(1, (int)$value);
        }

        $stmt->execute([
            ':setting_key' => $key,
            ':setting_value' => htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
        ]);
    }

    $utility->setFlash("success", "Settings saved successfully.");
} catch (Exception $e) {
    $utility->setFlash("danger", "Settings save failed: " . $e->getMessage());
}

settings_redirect();
