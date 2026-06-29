<?php
$pageTitle = "Delivery Management";
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';

$settings = qs_get_delivery_settings($model);
$zones = [];
$editZone = null;

try {
    $zones = qs_get_delivery_zones($model, false);
    if (!empty($_GET['edit_zone'])) {
        $editZone = qs_get_delivery_zone($model, (int)$_GET['edit_zone']);
    }
} catch (Exception $e) {
    $utility->setFlash("warning", "Run database_updates_delivery_coupon_cart.sql to enable delivery management.");
}

$zoneAction = $editZone ? 'update_delivery_zone' : 'add_delivery_zone';
?>

<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Delivery Management</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Delivery
            </p>
        </div>

        <div class="row">
            <div class="col-xl-5 col-lg-12">
                <div class="card card-default mb-24px">
                    <div class="card-body">
                        <h4>Fulfilment Settings</h4>
                        <form method="POST" action="../app/admin/deliveryHandler.php">
                            <input type="hidden" name="action" value="<?= $utility->inputEncode('update_delivery_settings'); ?>">

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="delivery_enabled" value="1" <?= !empty($settings['delivery_enabled']) ? 'checked' : ''; ?>>
                                    Enable Delivery
                                </label>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="pickup_enabled" value="1" <?= !empty($settings['pickup_enabled']) ? 'checked' : ''; ?>>
                                    Enable Pickup
                                </label>
                            </div>

                            <div class="form-group">
                                <label>Default Delivery Fee</label>
                                <input type="number" step="0.01" min="0" name="default_delivery_fee" class="form-control" value="<?= htmlspecialchars($settings['default_delivery_fee']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Free Delivery Minimum</label>
                                <input type="number" step="0.01" min="0" name="free_delivery_minimum" class="form-control" value="<?= htmlspecialchars($settings['free_delivery_minimum']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Pickup Address</label>
                                <input name="pickup_address" class="form-control" value="<?= htmlspecialchars($settings['pickup_address']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Pickup Instruction</label>
                                <textarea name="pickup_instruction" class="form-control" rows="4"><?= htmlspecialchars($settings['pickup_instruction']); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Save Settings</button>
                        </form>
                    </div>
                </div>

                <div class="card card-default">
                    <div class="card-body">
                        <h4><?= $editZone ? 'Edit Delivery Zone' : 'Create Delivery Zone'; ?></h4>
                        <form method="POST" action="../app/admin/deliveryHandler.php">
                            <input type="hidden" name="action" value="<?= $utility->inputEncode($zoneAction); ?>">
                            <?php if ($editZone): ?>
                                <input type="hidden" name="zone_id" value="<?= (int)$editZone['zone_id']; ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Zone Name</label>
                                <input name="zone_name" class="form-control" value="<?= htmlspecialchars($editZone['zone_name'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Delivery Fee</label>
                                <input type="number" step="0.01" min="0" name="delivery_fee" class="form-control" value="<?= htmlspecialchars($editZone['delivery_fee'] ?? '0.00'); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <?php $zoneStatus = $editZone['status'] ?? 'active'; ?>
                                <select name="status" class="form-control">
                                    <option value="active" <?= $zoneStatus === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?= $zoneStatus === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <?= $editZone ? 'Update Zone' : 'Create Zone'; ?>
                            </button>
                            <?php if ($editZone): ?>
                                <a href="delivery_mgr.php" class="btn btn-secondary btn-block mt-2">Cancel Edit</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-12">
                <div class="card card-default">
                    <div class="card-body">
                        <h4>Delivery Zones</h4>
                        <div class="table-responsive">
                            <table id="responsive-data-table" class="table">
                                <thead>
                                    <tr>
                                        <th>Zone</th>
                                        <th>Fee</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($zones)): ?>
                                        <?php foreach ($zones as $zone): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($zone['zone_name']); ?></td>
                                                <td><?= qs_money($zone['delivery_fee']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $zone['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                        <?= ucfirst($zone['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a class="btn btn-sm btn-outline-primary" href="delivery_mgr.php?edit_zone=<?= (int)$zone['zone_id']; ?>">Edit</a>
                                                        <form method="POST" action="../app/admin/deliveryHandler.php" style="display:inline;">
                                                            <input type="hidden" name="action" value="<?= $utility->inputEncode('toggle_delivery_zone'); ?>">
                                                            <input type="hidden" name="zone_id" value="<?= (int)$zone['zone_id']; ?>">
                                                            <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                                <?= $zone['status'] === 'active' ? 'Disable' : 'Enable'; ?>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No delivery zones have been created.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './inc/footer.php'; ?>
