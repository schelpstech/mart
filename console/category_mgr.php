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
			<h1>Store Categories</h1>
			<?php $utility->displayFlash(); ?>
			<p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
				<span><i class="mdi mdi-chevron-right"></i></span>Store Categories
			</p>

		</div>
		<div class="row">
			<div class="col-xl-4 col-lg-12">
				<div class="ec-cat-list card card-default mb-24px">
					<div class="card-body">
						<div class="ec-cat-form">
							<h4>Add New Categories to Section</h4>
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
													<option value="<?= $section['id'] ?>"><?= strtoupper($section['section_name']) ?></option>
											<?php endforeach;
											endif; ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="text" class="col-12 col-form-label">Category Name</label>
									<div class="col-12">
										<input id="category_name" name="category_name" class="form-control here slug-title" type="text">
									</div>
								</div>

								<div class="form-group row">
									<label for="slug" class="col-12 col-form-label">Category Slug</label>
									<div class="col-12">
										<input id="slug" name="slug" class="form-control here set-slug" type="text">
										<small>The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small>
									</div>
								</div>

								<div class="form-group row">
									<label for="text" class="col-12 col-form-label">Category Icon</label>
									<div class="col-12">
										<input id="category_icon" name="category_icon" class="form-control" type="file">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-12 col-form-label">Category Description</label>
									<div class="col-12">
										<textarea id="fulldescription" name="fulldescription" cols="40" rows="4" class="form-control"></textarea>
									</div>
								</div>
								<input hidden name="action" value="<?= $utility->inputEncode('this_form_adds_a_new_category') ?>" type="text">
								<div class="row">
									<div class="col-12">
										<button name="submit" type="submit" class="btn btn-danger">Create new Category</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-8 col-lg-12">
				<div class="ec-cat-list card card-default">
					<div class="card-body">
						<div class="table-responsive">
							<table id="responsive-data-table" class="table">
								<thead>
									<tr>
										<th>S/N</th>
										<th>Icon</th> <!-- 👈 New -->
										<th>Name</th>
										<th>Slug</th>
										<th>Products</th>
										<th>Section</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									// Fetch categories (Active & Inactive)
									$categories = $model->getRows("categories", [
										"where" => ["category_status" => ["Active", "Inactive"]],
										"order_by" => "categoryTbl_id  DESC"
									]);
									?>
									<?php if (!empty($categories)): ?>
										<?php foreach ($categories as $category): ?>
											<?php
											// Get parent section
											$section = $model->getRows("sections", [
												"where" => ["id" => $category['section_id']],
												"return_type" => "single"
											]);
											?>
											<tr>
												<td><?= $count++ ?></td>
												<td>
													<?php
													$iconPath = !empty($category['icon'])
														? "../view/assets/images/" . $category['icon']
														: "../view/assets/images/category_icons/default_icon.png";
													?>
													<img src="<?= $iconPath ?>" alt="Category Icon"
														style="width:40px; height:40px; object-fit:contain; border:1px solid #ddd; border-radius:6px;">
												</td>
												<td><?= strtoupper($category['category_name'] ?? "N/A"); ?></td>
												<td><?= ($category['category_slug'] ?? "N/A"); ?></td>
												<td>
													<?php
													$countItem = $model->getRows("products", [
														"where" => ["category_id" => $category['categoryTbl_id']],
														"return_type" => "count"
													]);
													?>
													<span class="ec-sub-cat-list">
														<span class="ec-sub-cat-count" title="Total Sub Categories"><?= $countItem ?></span>
													</span>
												</td>
												<td><?= strtoupper($section['section_name'] ?? "N/A"); ?></td>
												<td>
													<?= $category['category_status'] === "Active"
														? "<span class=\"badge badge-success\">Active</span>"
														: "<span class=\"badge badge-danger\">Inactive</span>" ?>
												</td>
												<td>
													<div class="btn-group">
														<button type="button" class="btn btn-outline-success">Action</button>
														<button type="button"
															class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
															data-bs-toggle="dropdown" aria-haspopup="true"
															aria-expanded="false" data-display="static">
															<span class="sr-only">Action</span>
														</button>

														<div class="dropdown-menu">
															<!-- Edit -->
															<a class="dropdown-item" href="./editcategory.php?categorySlug=<?= $utility->inputEncode($category['category_slug']) ?>">Edit Category</a>

															<!-- Toggle Active/Inactive -->
															<form method="POST" action="../app/admin/categoryHandler.php"
																style="display:inline;"
																onsubmit="return confirm('Are you sure you want to <?= $category['category_status'] === 'Active' ? 'deactivate' : 'activate' ?> this category?');">
																<input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_toggle_category_status') ?>">
																<input type="hidden" name="category_id" value="<?= $category['categoryTbl_id'] ?>">
																<button type="submit" class="btn btn-sm <?= $category['category_status'] === 'Active' ? 'btn-warning' : 'btn-success' ?>">
																	<i class="mdi <?= $category['category_status'] === 'Active' ? 'mdi-close-circle' : 'mdi-check-circle' ?>"></i>
																	<?= $category['category_status'] === 'Active' ? 'Deactivate' : 'Activate' ?>
																</button>
															</form>

															<!-- Delete -->
															<form method="POST" action="../app/admin/categoryHandler.php"
																style="display:inline;"
																onsubmit="return confirm('Are you sure you want to delete this category?');">
																<input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_delete_this_category') ?>">
																<input type="hidden" name="category_id" value="<?= $category['categoryTbl_id'] ?>">
																<button type="submit" class="btn btn-sm btn-danger">
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
											<td colspan="7" class="text-center">You have no categories yet.</td>
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