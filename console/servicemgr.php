<?php
$pageTitle = "Category Manager"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';
?>

<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Manage Services</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Manage Services
            </p>

        </div>
        <div class="row">
            <div class="col-xl-4 col-lg-12">
                <div class="ec-cat-list card card-default mb-24px">
                    <div class="card-body">
                        <div class="ec-cat-form">
                            <h4>Add New Service to Category</h4>
                            <form
                                method="POST"
                                action="../app/admin/serviceHandler.php"
                                id="service_add_form"
                                enctype="multipart/form-data"
                                autocomplete="off">
                                <!-- Category -->
                                <div class="form-group mb-3">
                                    <label for="category_id" class="form-label">Select Category</label>
                                    <select id="category_id" name="category_id" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                        <?php
                                        $cat = $model->getRows("categories", ["where" => ["category_status" => "Active"]]);
                                        if (!empty($cat)):
                                            foreach ($cat as $catg): ?>
                                                <option value="<?= $catg['categoryTbl_id'] ?>">
                                                    <?= strtoupper($catg['category_name']) ?>
                                                </option>
                                        <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>

                                <!-- Service Name -->
                                <div class="form-group mb-3">
                                    <label for="service_name" class="form-label">Service Name</label>
                                    <input
                                        id="service_name"
                                        name="service_name"
                                        class="form-control slug-title"
                                        type="text"
                                        placeholder="e.g. Bridal Makeup, Pedicure"
                                        required>
                                </div>

                                <!-- Slug -->
                                <div class="form-group mb-3">
                                    <label for="slug" class="form-label">Service Slug</label>
                                    <input
                                        id="slug"
                                        name="slug"
                                        class="form-control set-slug"
                                        type="text"
                                        placeholder="auto-generated or custom-slug"
                                        required>
                                    <small class="text-muted">
                                        The “slug” is the URL-friendly version of the name (e.g. <b>bridal-makeup</b>).
                                    </small>
                                </div>

                                <!-- Image Upload -->
                                <div class="form-group mb-3">
                                    <label for="service_icon" class="form-label">Service Image</label>
                                    <input
                                        id="service_icon"
                                        name="service_icon"
                                        class="form-control"
                                        type="file"
                                        accept="image/*"
                                        required>
                                </div>

                                <!-- Service Price -->
                                <div class="form-group mb-3">
                                    <label for="service_price" class="form-label">Service Price (£)</label>
                                    <input
                                        id="service_price"
                                        name="service_price"
                                        class="form-control"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="e.g. 45.50"
                                        required>
                                    <small class="text-muted">Enter price with decimals if needed (e.g. 15.75).</small>
                                </div>

                                <!-- Duration -->
                                <div class="form-group mb-3">
                                    <label for="service_duration" class="form-label">Service Duration</label>
                                    <input
                                        id="service_duration"
                                        name="service_duration"
                                        class="form-control"
                                        type="time"
                                        step="900"
                                        required>
                                    <small class="text-muted">Specify the expected duration (HH:MM, e.g. 01:30).</small>
                                </div>

                                <!-- Description -->
                                <div class="form-group mb-3">
                                    <label for="service_description" class="form-label">Service Description</label>
                                    <textarea
                                        id="service_description"
                                        name="service_description"
                                        rows="4"
                                        class="form-control"
                                        placeholder="Describe what the service includes..."
                                        required></textarea>
                                </div>

                                <input hidden name="action" value="<?= $utility->inputEncode('this_form_adds_a_new_service') ?>" type="text">

                                <div class="text-center">
                                    <button name="submit" type="submit" class="btn btn-primary w-100">
                                        <i class="mdi mdi-content-save"></i> Add New Service
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-12">
                <div class="card card-default">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="responsive-data-table" class="table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Icon</th>
                                        <th>Service Name</th>
                                        <th>Price (£)</th>
                                        <th>Category</th>
                                        <th>Duration (h:m)</th>
                                        <th>Status</th>
                                        <th>Date Added</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch all services (active or inactive)
                                    $services = $model->getRows("services", [
                                        "where" => ["status" => ["active", "inactive"]],
                                        "order_by" => "created_at DESC"
                                    ]);
                                    ?>

                                    <?php if (!empty($services)): ?>
                                        <?php foreach ($services as $srv): ?>
                                            <?php
                                            // Get category name (if category_id is set)
                                            $category = $model->getRows("categories", [
                                                "where" => ["categoryTbl_id" => $srv['services_categoryID']],
                                                "return_type" => "single"
                                            ]);
                                            ?>
                                            <tr>
                                                <td>
                                                    <img class="tbl-thumb"
                                                        src="../view/assets/images<?= !empty($srv['image']) ? $srv['image'] : 'default.png' ?>"
                                                        alt="Service Icon" />
                                                </td>

                                                <td><?= htmlspecialchars($srv['name'] ?? 'N/A'); ?></td>
                                                <td><?= number_format($srv['base_price'] ?? 0, 2); ?></td>
                                                <td><?= htmlspecialchars($category['category_name'] ?? 'Uncategorized'); ?></td>
                                                <td><?= htmlspecialchars($srv['duration'] ?? 'N/A'); ?></td>

                                                <td>
                                                    <?php if (strtolower($srv['status']) === 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td><?= htmlspecialchars($srv['created_at'] ?? '—'); ?></td>

                                                <td>
                                                    <div class="btn-group mb-1">
                                                        <button type="button" class="btn btn-outline-success">Action</button>
                                                        <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
                                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                                                            <span class="sr-only">Action</span>
                                                        </button>

                                                        <div class="dropdown-menu">

                                                            <!-- View -->
                                                            <a class="dropdown-item"
                                                               target="_blank" href="../view/viewservice.php?slug=<?=$srv['slug']; ?>">
                                                                <i class="mdi mdi-eye"></i> View
                                                            </a>

                                                            <!-- Edit -->
                                                            <a class="dropdown-item"
                                                                href="editservice.php?slug=<?= $utility->inputEncode($srv['slug']); ?>">
                                                                <i class="mdi mdi-pencil"></i> Edit
                                                            </a>

                                                            <!-- Toggle Active/Inactive -->
                                                            <form method="POST" action="../app/admin/serviceHandler.php"
                                                                onsubmit="return confirm('Are you sure you want to <?= strtolower($srv['status']) === 'active' ? 'deactivate' : 'activate' ?> Service - <?= htmlspecialchars($srv['name']); ?> ?');">
                                                                <input type="hidden" name="action" value="<?= $utility->inputEncode('toggle_service_status'); ?>">
                                                                <input type="hidden" name="service_id" value="<?= $srv['id']; ?>">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="mdi <?= strtolower($srv['status']) === 'active' ? 'mdi-close-circle' : 'mdi-check-circle'; ?>"></i>
                                                                    <?= strtolower($srv['status']) === 'active' ? 'Deactivate' : 'Activate'; ?>
                                                                </button>
                                                            </form>

                                                            <!-- Delete -->
                                                            <form method="POST" action="../app/admin/serviceHandler.php"
                                                                onsubmit="return confirm('Are you sure you want to delete Service - <?= htmlspecialchars($srv['name']); ?> ?');">
                                                                <input type="hidden" name="action" value="<?= $utility->inputEncode('delete_service'); ?>">
                                                                <input type="hidden" name="service_id" value="<?= $srv['id']; ?>">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="mdi mdi-delete"></i> Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No services have been added yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div> <!-- End Content -->
</div> <!-- End Content Wrapper -->
<?php
include './inc/footer.php';
?>