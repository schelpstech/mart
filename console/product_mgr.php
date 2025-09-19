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
							<h1>Product Manager</h1>
							<?php $utility->displayFlash(); ?>
							<p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
								<span><i class="mdi mdi-chevron-right"></i></span>Product
							</p>
						</div>
						<div>
							<a href="add-product.php" class="btn btn-primary"> Add Porduct</a>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="card card-default">
								<div class="card-body">
									<div class="table-responsive">
										<table id="responsive-data-table" class="table"
											style="width:100%">
											<thead>
												<tr>
													<th>Product</th>
													<th>Name</th>
													<th>Price</th>
													<th>Category</th>
													<th>Stock</th>
													<th>Purchased</th>
													<th>Status</th>
													<th>Date</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$count = 1;
												// Fetch categories (Active & Inactive)
												$product_item = $model->getRows("products", [
													"where" => ["status" => ["Active", "Inactive"]],
													"order_by" => "product_tbl_record_time  DESC"
												]);
												?>
												<?php if (!empty($product_item)): ?>
													<?php foreach ($product_item as $item): ?>
														<?php
														// Get parent section
														$cartgory = $model->getRows("categories", [
															"where" => ["categoryTbl_id" => $item['category_id']],
															"return_type" => "single"
														]);
														// Get parent section
														$countsold = $model->getRows("order_items_mart", [
															"where" => ["product_id" => $item['product_id']],
															"return_type" => "count"
														]);
														?>
														<tr>
															<td><img class="tbl-thumb" src="../view/assets/images/product/main/<?= !empty($item['image_main']) ?  $item['image_main'] : "default.png" ?>" alt="Product Image" /></td>

															<td><?= !empty($item['product_name']) ?  $item['product_name'] : "N/A" ?></td>
															<td><?= !empty($item['price']) ?  $item['price'] : "N/A" ?></td>
															<td><?= !empty($cartgory['category_name']) ?  $cartgory['category_name'] : "N/A" ?></td>
															<td><?= !empty($item['stock_quantity']) ?  $item['stock_quantity'] : "N/A" ?></td>
															<td><?= !empty($$countsold) ?  $$countsold : "Not Sold Yet" ?></td>
															<td>
																<?= $item['status'] === "Active"
																	? "<span class=\"badge badge-success\">Active</span>"
																	: "<span class=\"badge badge-danger\">Inactive</span>" ?>
															</td>
															<td><?= !empty($item['date_added']) ?  $item['date_added'] : "N/A" ?></td>
															<td>
																<div class="btn-group mb-1">
																	<button type="button"
																		class="btn btn-outline-success">Action</button>
																	<button type="button"
																		class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
																		data-bs-toggle="dropdown" aria-haspopup="true"
																		aria-expanded="false" data-display="static">
																		<span class="sr-only">Action</span>
																	</button>

																	<div class="dropdown-menu">

																		<!-- View -->
																		<a class="dropdown-item" href="viewproduct.php?productSlug=<?= $utility->inputEncode($item['product_slug']) ?>">
																			<i class="mdi mdi-eye"></i> View
																		</a>

																		<!-- Edit -->
																		<a class="dropdown-item" href="editproduct.php?productSlug=<?= $utility->inputEncode($item['product_slug']) ?>">
																			<i class="mdi mdi-pencil"></i> Edit
																		</a>

																		<!-- Toggle Active/Inactive -->
																		<form method="POST" action="../app/admin/productHandler.php"
																			onsubmit="return confirm('Are you sure you want to <?= $item['status'] === 'Active' ? 'deactivate' : 'activate' ?>  Product - <?= $item['product_name'] ?> ?');">
																			<input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_toggle_product_status') ?>">
																			<input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
																			<button type="submit" class="dropdown-item">
																				<i class="mdi <?= $item['status'] === 'Active' ? 'mdi-close-circle' : 'mdi-check-circle' ?>"></i>
																				<?= $item['status'] === 'Active' ? 'Deactivate' : 'Activate' ?>
																			</button>
																		</form>

																		<!-- Delete -->
																		<form method="POST" action="../app/admin/productHandler.php"
																			onsubmit="return confirm('Are you sure you want to delete  Product - <?= $item['product_name'] ?> ?');">
																			<input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_delete_this_product') ?>">
																			<input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
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
														<td colspan="7" class="text-center">You have no products yet.</td>
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