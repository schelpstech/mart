<?php
$pageTitle = "Reviews";
include './inc/head.php';
include_once '../controller/admin_helpers.php';
include './inc/navbar.php';
include './inc/header.php';

$tableReady = qs_admin_table_exists($model, 'testimonials');
$columns = $tableReady ? qs_admin_table_columns($model, 'testimonials') : [];
$idCol = qs_admin_pick_column($columns, ['testimonial_id', 'id']);
$nameCol = qs_admin_pick_column($columns, ['name', 'testimonial_name', 'customer_name', 'client_name']);
$roleCol = qs_admin_pick_column($columns, ['location', 'testimonial_role', 'role', 'designation']);
$messageCol = qs_admin_pick_column($columns, ['message', 'testimonial_message', 'content', 'review', 'testimonial']);
$ratingCol = qs_admin_pick_column($columns, ['testimonial_rating', 'rating']);
$statusCol = qs_admin_pick_column($columns, ['testimonial_status', 'status']);
$createdCol = qs_admin_pick_column($columns, ['testimonial_created_at', 'created_at']);
$orderCol = $createdCol ?: ($idCol ?: null);
$reviews = [];
$tableError = '';

if ($tableReady && $idCol && $nameCol && $messageCol) {
    try {
        $params = ['return_type' => 'all'];
        if ($orderCol) {
            $params['order_by'] = $orderCol . ' DESC';
        }
        $reviews = $model->getRows('testimonials', $params) ?: [];
    } catch (Exception $e) {
        $tableError = $e->getMessage();
    }
}
?>

<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Reviews</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Reviews
            </p>
        </div>

        <?php if (!$tableReady || !$idCol || !$nameCol || !$messageCol): ?>
            <div class="alert alert-warning">
                Review management needs the testimonials database fields. Run
                <strong>database_updates_admin_menu_integrations.sql</strong> before using this page.
            </div>
        <?php elseif ($tableError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($tableError); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-xl-4 col-lg-12">
                <div class="card card-default mb-24px">
                    <div class="card-body">
                        <h4>Add Review</h4>
                        <form method="POST" action="../app/admin/reviewHandler.php" autocomplete="off">
                            <input type="hidden" name="action" value="<?= $utility->inputEncode('add_review'); ?>">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Role / Label</label>
                                <input type="text" name="role" class="form-control" placeholder="Verified customer">
                            </div>
                            <div class="form-group">
                                <label>Rating</label>
                                <select name="rating" class="form-control">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?= $i; ?>"><?= $i; ?> star<?= $i === 1 ? '' : 's'; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Review</label>
                                <textarea name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" <?= (!$tableReady || !$idCol || !$nameCol || !$messageCol) ? 'disabled' : ''; ?>>Save Review</button>
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
                                        <th>Review</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($reviews)): ?>
                                        <?php foreach ($reviews as $review): ?>
                                            <?php
                                            $reviewId = (int)$review[$idCol];
                                            $status = strtolower($statusCol ? ($review[$statusCol] ?? 'active') : 'active');
                                            $badge = $status === 'active' ? 'success' : ($status === 'pending' ? 'warning' : 'secondary');
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($review[$nameCol] ?? 'Customer'); ?></strong><br>
                                                    <small><?= htmlspecialchars($roleCol ? ($review[$roleCol] ?? '') : ''); ?></small>
                                                </td>
                                                <td><?= htmlspecialchars(strlen($review[$messageCol] ?? '') > 140 ? substr($review[$messageCol], 0, 137) . '...' : ($review[$messageCol] ?? '')); ?></td>
                                                <td><?= $ratingCol ? (int)($review[$ratingCol] ?? 5) : '-'; ?></td>
                                                <td><span class="badge badge-<?= $badge; ?>"><?= strtoupper($status); ?></span></td>
                                                <td><?= qs_admin_format_date($createdCol ? ($review[$createdCol] ?? '') : ''); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-outline-success">Action</button>
                                                        <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="sr-only">Action</span>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <?php if ($statusCol): ?>
                                                                <?php foreach (['active' => 'Approve', 'inactive' => 'Hide', 'pending' => 'Mark Pending'] as $newStatus => $label): ?>
                                                                    <form method="POST" action="../app/admin/reviewHandler.php" class="px-3 py-1">
                                                                        <input type="hidden" name="action" value="<?= $utility->inputEncode('update_review_status'); ?>">
                                                                        <input type="hidden" name="review_id" value="<?= $reviewId; ?>">
                                                                        <input type="hidden" name="status" value="<?= $newStatus; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-link p-0"><?= $label; ?></button>
                                                                    </form>
                                                                <?php endforeach; ?>
                                                                <div class="dropdown-divider"></div>
                                                            <?php endif; ?>
                                                            <form method="POST" action="../app/admin/reviewHandler.php" class="px-3 py-1" onsubmit="return confirm('Delete this review?');">
                                                                <input type="hidden" name="action" value="<?= $utility->inputEncode('delete_review'); ?>">
                                                                <input type="hidden" name="review_id" value="<?= $reviewId; ?>">
                                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No reviews found.</td>
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
