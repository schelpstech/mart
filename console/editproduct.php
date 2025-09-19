<?php
$pageTitle = "Product Manager"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';

if (isset($_GET['productSlug']) && !empty($_GET['productSlug'])) {
    $productSlug = $utility->inputDecode($_GET['productSlug']);
    // Fetch category details from DB
    $productData = $model->getRows("products", [
        "where" => ["product_slug" => $productSlug],
        "return_type" => "single"
    ]);
    $_SESSION['main_icon'] = $productData['image_main'];
    $_SESSION['image_gallery'] = $productData['image_gallery'];

    if (!$productData) {
        $utility->setFlash("danger", "Product not found.");
        header("Location: product_mgr.php");
        exit;
    }
} else {
    $utility->setFlash("danger", "No Product Slug provided.");
    header("Location: ./product_mgr.php");
    exit;
}

?>

<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper d-flex align-items-center justify-content-between">
            <div>
                <h1>Add Product</h1>
                <?php $utility->displayFlash(); ?>
                <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                    <span><i class="mdi mdi-chevron-right"></i></span>Product
                </p>
            </div>
            <div>
                <a href="product_mgr.php" class="btn btn-primary"> View All
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header card-header-border-bottom">
                        <h2>Add Product</h2>
                    </div>
                    <form class="row g-3" method="POST" action="../app/admin/productHandler.php" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="row ec-vendor-uploads">
                                <!-- Product Image Upload -->
                                <div class="col-lg-4">
                                    <div class="ec-vendor-img-upload">
                                        <div class="ec-vendor-main-img">
                                            <div class="avatar-upload">
                                                <div class="avatar-edit">
                                                    <input type="file" id="imageUpload" name="main_image"
                                                        class="ec-image-upload" accept=".png, .jpg, .jpeg" />
                                                    <label for="imageUpload">
                                                        <img src="assets/img/icons/edit.svg" class="svg_img header_svg" alt="edit" />
                                                    </label>
                                                </div>
                                                <div class="avatar-preview ec-preview">
                                                    <div class="imagePreview ec-div-preview">
                                                        <img class="ec-image-preview"
                                                            src="<?= !empty($productData['image_main']) ? '../view/assets/images/product/main/' . $productData['image_main'] : '../view/assets/images/product/main/default.png' ?>"
                                                            alt="preview" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Thumbnails -->
                                            <div class="thumb-upload-set colo-md-12">
                                                <?php
                                                // Decode JSON array from DB (if available)
                                                $thumbs = !empty($productData['image_gallery'])
                                                    ? json_decode($productData['image_gallery'], true)
                                                    : [];

                                                for ($i = 0; $i < 4; $i++):
                                                    $thumbImg = isset($thumbs[$i]) && !empty($thumbs[$i])
                                                        ? '../view/assets/images/product/thumbs/' . $thumbs[$i]
                                                        : '../view/assets/images/product/thumbs/thumb.jpg';
                                                ?>
                                                    <div class="thumb-upload">
                                                        <div class="thumb-edit">
                                                            <input type="file" id="thumbUpload<?= $i + 1 ?>" name="thumbs[]"
                                                                class="ec-image-upload" accept=".png, .jpg, .jpeg" />
                                                            <label for="thumbUpload<?= $i + 1 ?>">
                                                                <img src="assets/img/icons/edit.svg" class="svg_img header_svg" alt="edit" />
                                                            </label>
                                                        </div>
                                                        <div class="thumb-preview ec-preview">
                                                            <div class="image-thumb-preview">
                                                                <img class="image-thumb-preview ec-image-preview"
                                                                    src="<?= htmlspecialchars($thumbImg) ?>" alt="preview" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <!-- Product Details -->
                                <div class="col-lg-8">
                                    <div class="ec-vendor-upload-detail">
                                        <div class="row g-3">

                                            <!-- Product Name -->
                                            <div class="col-md-6">
                                                <label for="product_name" class="form-label">Product name</label>
                                                <input type="text" class="form-control slug-title" id="product_name"
                                                    name="product_name"
                                                    value="<?= htmlspecialchars($productData['product_name']) ?>"
                                                    tabindex="1" required>
                                            </div>

                                            <!-- Category -->
                                            <div class="col-md-6">
                                                <label class="form-label">Select Category</label>
                                                <select name="category_id" id="Categories" class="form-select" tabindex="2" required>
                                                    <?php
                                                    $categories = $model->getRows("categories", ["where" => ["category_status" => "Active"]]);
                                                    foreach ($categories as $cat):
                                                        $selected = ($cat['categoryTbl_id'] == $productData['category_id']) ? 'selected' : '';
                                                    ?>
                                                        <option value="<?= $cat['categoryTbl_id'] ?>" <?= $selected ?>>
                                                            <?= strtoupper($cat['category_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Slug -->
                                            <div class="col-md-4">
                                                <label for="slug" class="col-form-label">Slug</label>
                                                <input id="slug" name="slug" class="form-control here set-slug"
                                                    type="text" tabindex="3"
                                                    value="<?= htmlspecialchars($productData['product_slug']) ?>" required>
                                            </div>

                                            <!-- Brand -->
                                            <div class="col-md-4">
                                                <label for="brand" class="col-form-label">Brand</label>
                                                <input id="brand" name="brand" class="form-control"
                                                    type="text" tabindex="4"
                                                    value="<?= htmlspecialchars($productData['brand']) ?>" required>
                                            </div>

                                            <!-- Model -->
                                            <div class="col-md-4">
                                                <label for="model" class="col-form-label">Model</label>
                                                <input id="model" name="model" class="form-control"
                                                    type="text" tabindex="5"
                                                    value="<?= htmlspecialchars($productData['model']) ?>" required>
                                            </div>

                                            <!-- Short Description -->
                                            <div class="col-md-12">
                                                <label class="form-label">Short Description</label>
                                                <textarea name="short_description" class="form-control" rows="2" tabindex="6"><?= htmlspecialchars($productData['short_description']) ?></textarea>
                                            </div>

                                            <!-- Colors -->
                                            <div class="col-md-4 mb-25">
                                                <label class="form-label">Colors</label>
                                                <input type="text" class="form-control"
                                                    name="colors" tabindex="7"
                                                    value="<?= htmlspecialchars($productData['color_options']) ?>"
                                                    data-role="tagsinput"
                                                    placeholder="Comma separated e.g. #ff6191,#33317d">
                                            </div>

                                            <!-- Sizes -->
                                            <div class="col-md-8 mb-25">
                                                <label class="form-label">Sizes</label>
                                                <div class="form-checkbox-box">
                                                    <?php
                                                    $savedSizes = explode(",", $productData['size_options'] ?? '');
                                                    foreach (["S", "M", "L", "XL", "XXL"] as $size):
                                                        $checked = in_array($size, $savedSizes) ? 'checked' : '';
                                                    ?>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" name="sizes[]" value="<?= $size ?>" <?= $checked ?> tabindex="8">
                                                            <label><?= $size ?></label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <!-- Price & Quantity -->
                                            <div class="col-md-6">
                                                <label class="form-label">Price (£)</label>
                                                <input type="number" class="form-control"
                                                    name="price" tabindex="9"
                                                    value="<?= htmlspecialchars($productData['price']) ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" class="form-control"
                                                    name="quantity" tabindex="10"
                                                    value="<?= htmlspecialchars($productData['stock_quantity']) ?>" required>
                                            </div>

                                            <!-- Full Detail -->
                                            <div class="col-md-12">
                                                <label class="form-label">Full Detail</label>
                                                <textarea name="full_description" class="form-control" tabindex="11" rows="4"><?= htmlspecialchars($productData['description']) ?></textarea>
                                            </div>

                                            <!-- Tags -->
                                            <div class="col-md-12">
                                                <label class="form-label">Product Tags</label>
                                                <input type="text" class="form-control" id="group_tag"
                                                    name="group_tag"
                                                    value="<?= htmlspecialchars($productData['tags']) ?>"
                                                    tabindex="12" data-role="tagsinput" />
                                            </div>

                                            <!-- Hidden Inputs -->
                                            <input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_updates_product') ?>">
                                            <input type="hidden" name="product_id" value="<?= $productData['product_id'] ?>">
                                            <!-- Submit -->
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-info">Update Product Information</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>

    </div> <!-- End Content -->
</div> <!-- End Content Wrapper -->

<?php
include './inc/footer.php';
?>