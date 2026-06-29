<?php
$pageTitle = "Admins";
include './inc/head.php';
include_once '../controller/admin_helpers.php';
include './inc/navbar.php';
include './inc/header.php';

$tableReady = qs_admin_table_exists($model, 'admins');
$columns = $tableReady ? qs_admin_table_columns($model, 'admins') : [];
$hasStatus = in_array('admin_status', $columns, true);
$admins = [];
$tableError = '';

if ($tableReady) {
    try {
        $admins = $model->getRows('admins', [
            'order_by' => 'admin_id DESC',
            'return_type' => 'all'
        ]) ?: [];
    } catch (Exception $e) {
        $tableError = $e->getMessage();
    }
}
?>

<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Admins</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Admins
            </p>
        </div>

        <?php if (!$tableReady): ?>
            <div class="alert alert-warning">Admin management needs the admins table. Run the database migration if this is a fresh install.</div>
        <?php elseif ($tableError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($tableError); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-xl-4 col-lg-12">
                <div class="card card-default mb-24px">
                    <div class="card-body">
                        <h4>Add Admin</h4>
                        <form method="POST" action="../app/admin/adminHandler.php" autocomplete="off">
                            <input type="hidden" name="action" value="<?= $utility->inputEncode('add_admin'); ?>">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="admin_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="admin_email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control">
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                            <button type="submit" class="btn btn-primary" <?= !$tableReady ? 'disabled' : ''; ?>>Create Admin</button>
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
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($admins)): ?>
                                        <?php foreach ($admins as $admin): ?>
                                            <?php
                                            $adminId = (int)$admin['admin_id'];
                                            $status = $hasStatus ? strtolower($admin['admin_status'] ?? 'active') : 'active';
                                            $isSelf = !empty($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === $adminId;
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($admin['admin_name'] ?? ''); ?><?= $isSelf ? ' <span class="badge badge-info">You</span>' : ''; ?></td>
                                                <td><?= htmlspecialchars($admin['admin_email'] ?? ''); ?></td>
                                                <td><?= htmlspecialchars(ucfirst($admin['role'] ?? 'admin')); ?></td>
                                                <td><span class="badge badge-<?= $status === 'active' ? 'success' : 'secondary'; ?>"><?= strtoupper($status); ?></span></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-outline-success">Action</button>
                                                        <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Action</span>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right p-3" style="min-width: 300px;">
                                                            <form method="POST" action="../app/admin/adminHandler.php" autocomplete="off">
                                                                <input type="hidden" name="action" value="<?= $utility->inputEncode('update_admin'); ?>">
                                                                <input type="hidden" name="admin_id" value="<?= $adminId; ?>">
                                                                <div class="form-group">
                                                                    <label>Name</label>
                                                                    <input type="text" name="admin_name" class="form-control" value="<?= htmlspecialchars($admin['admin_name'] ?? ''); ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Email</label>
                                                                    <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars($admin['admin_email'] ?? ''); ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Role</label>
                                                                    <select name="role" class="form-control">
                                                                        <?php foreach (['admin', 'manager', 'staff'] as $role): ?>
                                                                            <option value="<?= $role; ?>" <?= strtolower($admin['role'] ?? '') === $role ? 'selected' : ''; ?>><?= ucfirst($role); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>New Password</label>
                                                                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                                                                </div>
                                                                <?php if ($hasStatus): ?>
                                                                    <div class="form-group">
                                                                        <label>Status</label>
                                                                        <select name="admin_status" class="form-control" <?= $isSelf ? 'disabled' : ''; ?>>
                                                                            <option value="active" <?= $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                                                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                            </form>
                                                            <?php if (!$isSelf): ?>
                                                                <hr>
                                                                <form method="POST" action="../app/admin/adminHandler.php" onsubmit="return confirm('Deactivate this admin account?');">
                                                                    <input type="hidden" name="action" value="<?= $utility->inputEncode('deactivate_admin'); ?>">
                                                                    <input type="hidden" name="admin_id" value="<?= $adminId; ?>">
                                                                    <button type="submit" class="btn btn-warning btn-sm">Deactivate</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No admins found.</td>
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
