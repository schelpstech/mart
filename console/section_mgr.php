<?php
$pageTitle = "Section Manager"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';
?>

<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
	<div class="content">
		<div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
			<h1>Store Section</h1>
			<?php $utility->displayFlash(); ?>
			<p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
				<span><i class="mdi mdi-chevron-right"></i></span>Store Section
			</p>

		</div>
		<div class="row">
			<div class="col-xl-4 col-lg-12">
				<div class="ec-cat-list card card-default mb-24px">
					<div class="card-body">
						<div class="ec-cat-form">
							<h4>Add New Store Section</h4>
							<form method="POST" action="../app/admin/sectionhandler.php" autocapitalize="true" autocomplete="off" id="section_add_form">
								<div class="form-group row">
									<label for="text" class="col-12 col-form-label">Section Name</label>
									<div class="col-12">
										<input id="section_name" name="section_name" class="form-control here slug-title" type="text">
									</div>
								</div>

								<div class="form-group row">
									<label for="slug" class="col-12 col-form-label">Section Slug</label>
									<div class="col-12">
										<input id="slug" name="slug" class="form-control here set-slug" type="text">
										<small>The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-12 col-form-label">Section Description</label>
									<div class="col-12">
										<textarea id="fulldescription" name="fulldescription" cols="40" rows="4" class="form-control"></textarea>
									</div>
								</div>
								<input hidden name="action" value="<?= $utility->inputEncode('this_form_adds_a_new_section') ?>" type="text">
								<div class="row">
									<div class="col-12">
										<button name="submit" type="submit" class="btn btn-danger">Create new Section</button>
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
										<th>Name</th>
										<th>Slug</th>
										<th>Description</th>
										<th>Categories</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>

								<tbody>
									<?php
									$count = 1;
									// Fetch last 10 orders
									$sections = $model->getRows("sections", ["where" =>  ["section_status" => ["Active", "Inactive"]]]);
									?>
									<?php if (!empty($sections)): ?>
										<?php foreach ($sections as $section): ?>
											<tr>
												<td><?= $count++ ?></td>
												<td><?= strtoupper($section['section_name'] ?? "N/A"); ?></td>
												<td><?= ($section['section_slug'] ?? "N/A"); ?></td>
												<td><?= ($section['description'] ?? "N/A"); ?></td>

												<td>
													<?php
													$countItem = $model->getRows("categories", [
														"where" => ["section_id" => $section['id']],
														"return_type" => "count"
													]);
													?>
													<span class="ec-sub-cat-list">
														<span class="ec-sub-cat-count" title="Total Sub Categories"><?= $countItem ?></span>
													</span>
												</td>
												<td>
													<?= $section['section_status'] == "Active" ? "<span class=\"badge badge-success\">Active</span>" : "<span class=\"badge badge-danger\">Inactive</span>" ?></td>
												<td>
													<div class="btn-group">
														<button type="button"
															class="btn btn-outline-success">Action</button>
														<button type="button"
															class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
															data-bs-toggle="dropdown" aria-haspopup="true"
															aria-expanded="false" data-display="static">
															<span class="sr-only">Action</span>
														</button>

														<div class="dropdown-menu">
															<a class="dropdown-item" href="./editsection.php?sectionID=<?= $utility->inputEncode($section['section_slug']) ?>">Edit Section</a>
															<!-- Deactivate Section -->
															<form method="POST" action="../app/admin/sectionhandler.php"
																style="display:inline;"
																onsubmit="return confirm('Are you sure you want to <?= $section['section_status'] === 'Active' ? 'deactivate' : 'activate' ?> this section?');">
																<input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_toggle_section_status') ?>">
																<input type="hidden" name="section_id" value="<?= $section['id'] ?>">
																<button type="submit" class="btn btn-sm <?= $section['section_status'] === 'Active' ? 'btn-warning' : 'btn-success' ?>">
																	<i class="mdi <?= $section['section_status'] === 'Active' ? 'mdi-close-circle' : 'mdi-check-circle' ?>"></i>
																	<?= $section['section_status'] === 'Active' ? 'Deactivate Section' : 'Activate Section' ?>
																</button>
															</form>


															<!-- Delete -->
															<?php if ($countItem > 0): ?>
																<button class="btn btn-sm btn-secondary" disabled
																	title="This section has <?= $countItem ?> categories. Delete disabled.">
																	<i class="mdi mdi-delete"></i> Delete Section
																</button>
															<?php else: ?>
																<form method="POST" action="../app/admin/sectionhandler.php"
																	style="display:inline;"
																	onsubmit="return confirm('Are you sure you want to delete this section?');">
																	<input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_delete_this_section') ?>">
																	<input type="hidden" name="section_id" value="<?= $section['id'] ?>">
																	<button type="submit" class="btn btn-sm btn-danger">
																		<i class="mdi mdi-delete"></i> Delete
																	</button>
																</form>
															<?php endif; ?>
														</div>
													</div>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="7" class="text-center">You have no sections yet.</td>
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