<?php
$pageTitle = "Settings";
include './inc/head.php';
include_once '../controller/admin_helpers.php';
include './inc/navbar.php';
include './inc/header.php';

$settingsReady = qs_admin_table_exists($model, 'site_settings');
$settings = qs_admin_fetch_settings($model);
$settingsFields = [
    'site_name' => ['label' => 'Site Name', 'type' => 'text', 'default' => APP_NAME],
    'site_url' => ['label' => 'Site URL', 'type' => 'url', 'default' => env_value('APP_URL', 'https://queenzystores.com')],
    'support_email' => ['label' => 'Support Email', 'type' => 'email', 'default' => env_value('MAIL_FROM_ADDRESS', 'noreply@queenzystores.com')],
    'orders_email' => ['label' => 'Orders Email', 'type' => 'email', 'default' => env_value('ADMIN_ORDER_EMAIL', 'orders@queenzystores.com')],
    'support_phone' => ['label' => 'Support Phone', 'type' => 'text', 'default' => ''],
    'cart_expiry_days' => ['label' => 'Cart Expiry Days', 'type' => 'number', 'default' => '30'],
];
?>

<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Settings</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Settings
            </p>
        </div>

        <?php if (!$settingsReady): ?>
            <div class="alert alert-warning">
                Settings management needs the <strong>site_settings</strong> table. Run
                <strong>database_updates_admin_menu_integrations.sql</strong> before saving settings.
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card card-default">
                    <div class="card-body">
                        <h4>General Settings</h4>
                        <form method="POST" action="../app/admin/settingsHandler.php" autocomplete="off">
                            <input type="hidden" name="action" value="<?= $utility->inputEncode('save_settings'); ?>">
                            <div class="row">
                                <?php foreach ($settingsFields as $key => $field): ?>
                                    <div class="form-group col-md-6">
                                        <label><?= htmlspecialchars($field['label']); ?></label>
                                        <input
                                            type="<?= htmlspecialchars($field['type']); ?>"
                                            name="settings[<?= htmlspecialchars($key); ?>]"
                                            class="form-control"
                                            value="<?= htmlspecialchars(qs_admin_setting($settings, $key, $field['default'])); ?>"
                                            <?= $field['type'] === 'number' ? 'min="1"' : ''; ?>>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="form-group">
                                <label>Public Notice</label>
                                <textarea name="settings[public_notice]" class="form-control" rows="4"><?= htmlspecialchars(qs_admin_setting($settings, 'public_notice', '')); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" <?= !$settingsReady ? 'disabled' : ''; ?>>Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-12">
                <div class="card card-default">
                    <div class="card-body">
                        <h4>Configuration Notes</h4>
                        <p>Security keys still belong in the private <code>.env</code> file. This page is for operational site settings only.</p>
                        <p>Delivery fees, pickup address, and zones are managed from <a href="delivery_mgr.php">Delivery</a>.</p>
                        <p>Coupons are managed from <a href="coupon_mgr.php">Coupons</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './inc/footer.php'; ?>
