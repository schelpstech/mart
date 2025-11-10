<?php
$pageTitle = "Edit Service";
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';

if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $serviceSlug = $utility->inputDecode($_GET['slug']);

    // Fetch service details from DB
    $serviceData = $model->getRows("services", [
        "where" => ["slug" => $serviceSlug],
        "return_type" => "single"
    ]);

    if (!$serviceData) {
        $utility->setFlash("danger", "Service not found.");
        header("Location: servicemgr.php");
        exit;
    }

    $_SESSION['image'] = $serviceData['image'];
} else {
    $utility->setFlash("danger", "No service slug provided.");
    header("Location: servicemgr.php");
    exit;
}
?>

<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Edit Service</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs">
                <span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>
                <span><a href="servicemgr.php">Manage Services</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Edit Service
            </p>
        </div>

        <div class="row">
            <div class="col-xl-6 offset-xl-3 col-lg-12">
                <div class="ec-cat-list card card-default mb-24px">
                    <div class="card-body">
                        <div class="ec-cat-form">
                            <h4>Edit Service Details</h4>
                            <form
                                method="POST"
                                action="../app/admin/serviceHandler.php"
                                id="service_edit_form"
                                enctype="multipart/form-data"
                                autocomplete="off">

                                <!-- Hidden ID -->
                                <input type="hidden" name="service_id" value="<?= $serviceData['id'] ?>">

                                <!-- Category -->
                                <div class="form-group mb-3">
                                    <label for="category_id" class="form-label">Select Category</label>
                                    <select id="category_id" name="category_id" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                        <?php
                                        $categories = $model->getRows("categories", ["where" => ["category_status" => "Active"]]);
                                        if (!empty($categories)):
                                            foreach ($categories as $category): ?>
                                                <option value="<?= $category['categoryTbl_id'] ?>"
                                                    <?= ($category['categoryTbl_id'] == $serviceData['services_categoryID']) ? 'selected' : '' ?>>
                                                    <?= strtoupper($category['category_name']) ?>
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
                                        value="<?= htmlspecialchars($serviceData['name']) ?>"
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
                                        value="<?= htmlspecialchars($serviceData['slug']) ?>"
                                        required>
                                    <small class="text-muted">
                                        URL-friendly name (e.g. <b>bridal-makeup</b>).
                                    </small>
                                </div>

                                <!-- Current Image -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Current Service Image</label><br>
                                    <img src="../view/assets/images<?= htmlspecialchars($serviceData['image']) ?>" alt="Service Image" width="100" height="100" class="rounded mb-2">
                                </div>

                                <!-- Upload New Image -->
                                <div class="form-group mb-3">
                                    <label for="service_icon" class="form-label">Change Service Image (optional)</label>
                                    <input
                                        id="service_icon"
                                        name="service_icon"
                                        class="form-control"
                                        type="file"
                                        accept="image/*">
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
                                        value="<?= htmlspecialchars($serviceData['base_price']) ?>"
                                        required>
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
                                        value="<?= htmlspecialchars($serviceData['duration']) ?>"
                                        required>
                                </div>

                                <!-- Description -->
                                <div class="form-group mb-3">
                                    <label for="service_description" class="form-label">Service Description</label>
                                    <textarea
                                        id="service_description"
                                        name="service_description"
                                        rows="4"
                                        class="form-control"
                                        required><?= htmlspecialchars($serviceData['description']) ?></textarea>
                                </div>

                                <input hidden name="action" value="<?= $utility->inputEncode('update_service') ?>" type="text">

                                <div class="text-center">
                                    <button name="submit" type="submit" class="btn btn-primary w-100">
                                        <i class="mdi mdi-content-save"></i> Update Service
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Content -->
</div> <!-- End Content Wrapper -->

<?php include './inc/footer.php'; ?>
