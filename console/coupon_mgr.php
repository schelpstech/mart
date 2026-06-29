<?php
$pageTitle = "Coupon Management";
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';

$editCoupon = null;
if (!empty($_GET['edit'])) {
    $editCoupon = $model->getRows('coupons', [
        'where' => ['coupon_id' => (int)$_GET['edit']],
        'return_type' => 'single'
    ]);
}

$coupons = [];
try {
    $coupons = $model->getRows('coupons', ['order_by' => 'created_at DESC']) ?: [];
} catch (Exception $e) {
    $utility->setFlash("warning", "Run database_updates_delivery_coupon_cart.sql to enable coupon management.");
}

$formAction = $editCoupon ? 'update_coupon' : 'add_coupon';
?>

<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Coupon Management</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Coupons
            </p>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-12">
                <div class="card card-default mb-24px">
                    <div class="card-body">
                        <h4><?= $editCoupon ? 'Edit Coupon' : 'Create Coupon'; ?></h4>
                        <form method="POST" action="../app/admin/couponHandler.php" autocomplete="off">
                            <?php if ($editCoupon): ?>
                                <input type="hidden" name="coupon_id" value="<?= (int)$editCoupon['coupon_id']; ?>">
                            <?php endif; ?>
                            <input type="hidden" name="action" value="<?= $utility->inputEncode($formAction); ?>">

                            <div class="form-group">
                                <label>Coupon Code</label>
                                <input name="code" class="form-control" value="<?= htmlspecialchars($editCoupon['code'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <input name="description" class="form-control" value="<?= htmlspecialchars($editCoupon['description'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label>Discount Type</label>
                                <select name="discount_type" class="form-control" required>
                                    <?php $type = $editCoupon['discount_type'] ?? 'fixed'; ?>
                                    <option value="fixed" <?= $type === 'fixed' ? 'selected' : ''; ?>>Fixed Amount</option>
                                    <option value="percentage" <?= $type === 'percentage' ? 'selected' : ''; ?>>Percentage</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Discount Value</label>
                                <input type="number" step="0.01" min="0" name="discount_value" class="form-control" value="<?= htmlspecialchars($editCoupon['discount_value'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Minimum Order Amount</label>
                                <input type="number" step="0.01" min="0" name="minimum_order_amount" class="form-control" value="<?= htmlspecialchars($editCoupon['minimum_order_amount'] ?? '0.00'); ?>">
                            </div>

                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="datetime-local" name="start_date" class="form-control" value="<?= !empty($editCoupon['start_date']) ? date('Y-m-d\TH:i', strtotime($editCoupon['start_date'])) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="datetime-local" name="expiry_date" class="form-control" value="<?= !empty($editCoupon['expiry_date']) ? date('Y-m-d\TH:i', strtotime($editCoupon['expiry_date'])) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label>Usage Limit</label>
                                <input type="number" min="1" name="usage_limit" class="form-control" value="<?= htmlspecialchars($editCoupon['usage_limit'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <?php $status = $editCoupon['status'] ?? 'active'; ?>
                                <select name="status" class="form-control">
                                    <option value="active" <?= $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <?= $editCoupon ? 'Update Coupon' : 'Create Coupon'; ?>
                            </button>
                            <?php if ($editCoupon): ?>
                                <a href="coupon_mgr.php" class="btn btn-secondary btn-block mt-2">Cancel Edit</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-12">
                <div class="card card-default">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="responsive-data-table" class="table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Discount</th>
                                        <th>Minimum</th>
                                        <th>Dates</th>
                                        <th>Usage</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($coupons)): ?>
                                        <?php foreach ($coupons as $coupon): ?>
                                            <?php
                                            $usage = $model->getRows('coupon_usage', [
                                                'select' => 'COUNT(*) AS usage_count, COALESCE(SUM(discount_amount),0) AS total_discount',
                                                'where' => ['coupon_id' => $coupon['coupon_id']],
                                                'return_type' => 'single'
                                            ]);
                                            ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($coupon['code']); ?></strong><br><small><?= htmlspecialchars($coupon['description'] ?? ''); ?></small></td>
                                                <td>
                                                    <?= $coupon['discount_type'] === 'percentage'
                                                        ? number_format($coupon['discount_value'], 2) . '%'
                                                        : qs_money($coupon['discount_value']); ?>
                                                </td>
                                                <td><?= qs_money($coupon['minimum_order_amount']); ?></td>
                                                <td>
                                                    <small>Start: <?= !empty($coupon['start_date']) ? htmlspecialchars($coupon['start_date']) : 'Anytime'; ?></small><br>
                                                    <small>Expiry: <?= !empty($coupon['expiry_date']) ? htmlspecialchars($coupon['expiry_date']) : 'No expiry'; ?></small>
                                                </td>
                                                <td>
                                                    <?= (int)($usage['usage_count'] ?? 0); ?>
                                                    <?php if (!empty($coupon['usage_limit'])): ?>
                                                        / <?= (int)$coupon['usage_limit']; ?>
                                                    <?php endif; ?>
                                                    <br><small>Saved <?= qs_money($usage['total_discount'] ?? 0); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $coupon['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                        <?= ucfirst($coupon['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a class="btn btn-sm btn-outline-primary" href="coupon_mgr.php?edit=<?= (int)$coupon['coupon_id']; ?>">Edit</a>
                                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                                                        <div class="dropdown-menu">
                                                            <form method="POST" action="../app/admin/couponHandler.php">
                                                                <input type="hidden" name="action" value="<?= $utility->inputEncode('toggle_coupon'); ?>">
                                                                <input type="hidden" name="coupon_id" value="<?= (int)$coupon['coupon_id']; ?>">
                                                                <button class="dropdown-item" type="submit">
                                                                    <?= $coupon['status'] === 'active' ? 'Disable' : 'Enable'; ?>
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="../app/admin/couponHandler.php" onsubmit="return confirm('Deactivate this coupon?');">
                                                                <input type="hidden" name="action" value="<?= $utility->inputEncode('delete_coupon'); ?>">
                                                                <input type="hidden" name="coupon_id" value="<?= (int)$coupon['coupon_id']; ?>">
                                                                <button class="dropdown-item text-danger" type="submit">Deactivate</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No coupons have been created.</td>
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
