<?php
$pageTitle = "Product Manager"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';
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
														class="ec-image-upload" accept=".png, .jpg, .jpeg" required />
													<label for="imageUpload">
														<img src="assets/img/icons/edit.svg" class="svg_img header_svg" alt="edit" />
													</label>
												</div>
												<div class="avatar-preview ec-preview">
													<div class="imagePreview ec-div-preview">
														<img class="ec-image-preview"
															src="assets/img/products/vender-upload-preview.jpg" alt="preview" />
													</div>
												</div>
											</div>

											<!-- Thumbnails -->
											<div class="thumb-upload-set colo-md-12">
												<?php for ($i = 1; $i <= 4; $i++): ?>
													<div class="thumb-upload">
														<div class="thumb-edit">
															<input type="file" id="thumbUpload<?= $i ?>" name="thumbs[]"
																class="ec-image-upload" accept=".png, .jpg, .jpeg" />
															<label for="thumbUpload<?= $i ?>">
																<img src="assets/img/icons/edit.svg" class="svg_img header_svg" alt="edit" />
															</label>
														</div>
														<div class="thumb-preview ec-preview">
															<div class="image-thumb-preview">
																<img class="image-thumb-preview ec-image-preview"
																	src="assets/img/products/vender-upload-thumb-preview.jpg" alt="preview" />
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
												<input type="text" class="form-control slug-title" id="product_name" name="product_name" tabindex="1" required>
											</div>

											<!-- Category -->
											<div class="col-md-6">
												<label class="form-label">Select Category</label>
												<select name="category_id" id="Categories" class="form-select" tabindex="2" required>
													<?php
													$categories = $model->getRows("categories", ["where" => ["category_status" => "Active"]]);
													foreach ($categories as $cat): ?>
														<option value="<?= $cat['categoryTbl_id'] ?>">
															<?= strtoupper($cat['category_name']); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>

											<!-- Slug -->
											<div class="col-md-4">
												<label for="slug" class="col-form-label">Slug</label>
												<input id="slug" name="slug" class="form-control here set-slug" tabindex="3" type="text" required>
											</div>

											<!-- Brand -->
											<div class="col-md-4">
												<label for="brand" class="col-form-label">Brand</label>
												<input id="brand" name="brand" class="form-control" type="text" tabindex="4" required>
											</div>
											<!-- Model -->
											<div class="col-md-4">
												<label for="model" class="col-form-label">Model</label>
												<input id="model" name="model" class="form-control" type="text" tabindex="5" required>
											</div>

											<!-- Short Description -->
											<div class="col-md-12">
												<label class="form-label">Short Description</label>
												<textarea name="short_description" class="form-control" rows="2" tabindex="6"></textarea>
											</div>

											<!-- Colors -->
											<div class="col-md-4 mb-25">
												<label class="form-label">Colors</label>
												<input type="text" class="form-control" name="colors" data-role="tagsinput" tabindex="7" placeholder="Comma separated e.g. #ff6191,#33317d">
											</div>

											<!-- Sizes -->
											<div class="col-md-8 mb-25">
												<label class="form-label">Sizes</label>
												<div class="form-checkbox-box">
													<?php foreach (["S", "M", "L", "XL", "XXL"] as $size): ?>
														<div class="form-check form-check-inline">
															<input type="checkbox" name="sizes[]" value="<?= $size ?>" tabindex="8">
															<label><?= $size ?></label>
														</div>
													<?php endforeach; ?>
												</div>
											</div>

											<!-- Price & Quantity -->
											<div class="col-md-6">
												<label class="form-label">Price (£)</label>
												<input type="number" class="form-control" name="price" tabindex="9" required>
											</div>
											<div class="col-md-6">
												<label class="form-label">Quantity</label>
												<input type="number" class="form-control" name="quantity" tabindex="10" required>
											</div>

											<!-- Full Detail -->
											<div class="col-md-12">
												<label class="form-label">Full Detail</label>
												<textarea name="full_description" class="form-control" tabindex="11" rows="4"></textarea>
											</div>

											<!-- Tags -->
											<div class="col-md-12">
												<label class="form-label">Product Tags <span>( Type and
														make comma to separate tags )</span></label>
												<input type="text" class="form-control" id="group_tag"
													name="group_tag" value="" placeholder="" tabindex="12"
													data-role="tagsinput" />
											</div>
											<input hidden name="action" value="<?= $utility->inputEncode('this_form_adds_a_new_product') ?>" type="text">
											<!-- Submit -->
											<div class="col-md-12">
												<button type="submit" class="btn btn-primary">Submit</button>
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