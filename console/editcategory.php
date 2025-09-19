<?php
$pageTitle = "Section Manager"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';

if (isset($_GET['categorySlug']) && !empty($_GET['categorySlug'])) {
    $categorySlug = $utility->inputDecode($_GET['categorySlug']);
    // Fetch category details from DB
    $categoryData = $model->getRows("categories", [
        "where" => ["category_slug" => $categorySlug],
        "return_type" => "single"
    ]);
    $_SESSION['category_icon'] = $categoryData['icon'];
    if (!$categoryData) {
        $utility->setFlash("danger", "Category not found.");
        header("Location: category_mgr.php");
        exit;
    }
} else {
    $utility->setFlash("danger", "No Category Slug provided.");
    header("Location: ./category_mgr.php");
    exit;
}
?>

<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Product Category</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Edit Product Category
            </p>
        </div>
        <div class="row">
            <div class="col-xl-6 offset-xl-3 col-lg-12">
                <div class="ec-cat-list card card-default mb-24px">
                    <div class="card-body">
                        <div class="ec-cat-form">
                            <h4>Edit Category Details</h4>
                            <form method="POST" action="../app/admin/categoryHandler.php" autocapitalize="true" autocomplete="off" id="section_add_form" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <label for="text" class="col-12 col-form-label">Select Section </label>
                                    <div class="col-12">
                                        <select id="section_id" name="section_id" class="form-control">
                                            <option value="">-- Select Section --</option>
                                            <?php
                                            $sections = $model->getRows("sections", ["where" => ["section_status" => "Active"]]);
                                            if (!empty($sections)):
                                                foreach ($sections as $section): ?>
                                                    <option value="<?= $section['id'] ?>"
                                                        <?= ($section['id'] == $categoryData['section_id']) ? 'selected' : '' ?>>
                                                        <?= strtoupper($section['section_name']) ?>
                                                    </option>
                                            <?php endforeach;
                                            endif; ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="text" class="col-12 col-form-label">Category Name</label>
                                    <div class="col-12">
                                        <input id="category_name" name="category_name" class="form-control here slug-title"
                                            type="text" value="<?= htmlspecialchars($categoryData['category_name']) ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="slug" class="col-12 col-form-label">Category Slug</label>
                                    <div class="col-12">
                                        <input id="slug" name="slug" class="form-control here set-slug"
                                            type="text" value="<?= htmlspecialchars($categoryData['category_slug']) ?>">

                                        <small>The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="text" class="col-12 col-form-label">Category Icon</label>
                                    <div class="col-12">
                                        <?php if (!empty($categoryData['icon'])): ?>
                                            <p>
                                                <img src="../view/assets/images/<?= $categoryData['icon'] ?>" alt="Category Icon"
                                                    style="width: 80px; height: 80px; object-fit: contain; border:1px solid #ccc;">
                                            </p>
                                        <?php endif; ?>
                                        <input id="category_icon" name="category_icon" class="form-control" type="file" >
                                        <small>Leave blank if you don’t want to change the icon</small>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Category Description</label>
                                    <div class="col-12">
                                        <textarea id="fulldescription" name="fulldescription" cols="40" rows="4" class="form-control"><?= htmlspecialchars($categoryData['description']) ?></textarea>
                                    </div>
                                </div>
                                <input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_updates_a_category') ?>">
                                <input type="hidden" name="category_id" value="<?= $categoryData['categoryTbl_id'] ?>">

                                <div class="row">
                                    <div class="col-12">
                                        <button name="submit" type="submit" class="btn btn-danger">Update Category</button>
                                    </div>
                                </div>
                            </form>
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