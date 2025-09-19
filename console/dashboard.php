<?php
$pageTitle = "Dashboard"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';
?>


<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
	<div class="content">
		<div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
			<h1>Queenzy Stores</h1>
			<?php $utility->displayFlash(); ?>
			<p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
				<span><i class="mdi mdi-chevron-right"></i></span>Dashboard
			</p>
		</div>
		<!-- Top Statistics -->

		<?php

		// Get dashboard stats
		$totalUsers = $model->countRows("users_mart");
		$totalOrders = $model->countRows("orders_mart");
		$totalRevenue =  $model->getRows("orders_mart", [
			"where" => ["payment_status" => "paid"],
			"return_type" => "sum"
		]);
		$todaySignups = $model->countRows("users_mart", "DATE(created_at) = CURDATE()");
		?>
		<div class="row">
			<div class="col-xl-3 col-sm-6 p-b-15 lbl-card">
				<div class="card card-mini dash-card card-1">
					<div class="card-body">
						<h2 class="mb-1"><?= number_format($todaySignups) ?></h2>
						<p>Daily Signups</p>
						<span class="mdi mdi-account-arrow-left"></span>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 p-b-15 lbl-card">
				<div class="card card-mini dash-card card-2">
					<div class="card-body">
						<h2 class="mb-1"><?= number_format($totalUsers) ?></h2>
						<p>Total Users</p>
						<span class="mdi mdi-account-clock"></span>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 p-b-15 lbl-card">
				<div class="card card-mini dash-card card-3">
					<div class="card-body">
						<h2 class="mb-1"><?= number_format($totalOrders) ?></h2>
						<p>Total Orders</p>
						<span class="mdi mdi-package-variant"></span>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 p-b-15 lbl-card">
				<div class="card card-mini dash-card card-4">
					<div class="card-body">
						<h2 class="mb-1">£<?= number_format($totalRevenue, 2) ?></h2>
						<p>Total Revenue</p>
						<span class="mdi mdi-currency-gbp"></span>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xl-7">
				<!-- New Customers -->
				<div class="card ec-cust-card card-table-border-none card-default">
					<div class="card-header justify-content-between ">
						<h2>New Customers</h2>
						<div>
							<button class="text-black-50 mr-2 font-size-20">
								<i class="mdi mdi-cached"></i>
							</button>
							<div class="dropdown show d-inline-block widget-dropdown">
								<a class="dropdown-toggle icon-burger-mini" href="#" role="button"
									id="dropdown-customar" data-bs-toggle="dropdown" aria-haspopup="true"
									aria-expanded="false" data-display="static">
								</a>
								<ul class="dropdown-menu dropdown-menu-right">
									<li class="dropdown-item"><a href="#">Action</a></li>
									<li class="dropdown-item"><a href="#">Another action</a></li>
									<li class="dropdown-item"><a href="#">Something else here</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="card-body pt-0 pb-15px">
						<table class="table ">
							<tbody>
								<?php
								// Fetch top customers (by total amount spent or by order count)
								$customers =  $model->getRows("users_mart", [
									"left_join" => [
										"user_profiles" => " on users_mart.user_id = user_profiles.user_id"
									],
									"limit" => "6"
								]);
								?>
								<?php if (!empty($customers)): ?>
									<?php foreach ($customers as $customer): ?>
										<tr>
											<td>
												<div class="media">
													<div class="media-image mr-3 rounded-circle">
														<a href="profile.html"><img
																class="profile-img rounded-circle w-45"
																src="http://localhost/mart/view/assets/images/user/icon.png"
																alt="customer image"></a>
													</div>
													<div class="media-body align-self-center">
														<a href="profile.html">
															<h6 class="mt-0 text-dark font-weight-medium">
																<?=

																strtoupper(($customer['firstname'] ?? '') . " " . ($customer['lastname'] ?? 'N/A')); ?>
															</h6>
														</a>
														<small><?= $customer['email'] ?? ''; ?></small>
													</div>
												</div>
											</td>
											<td class="text-dark d-none d-md-block">
												<?php
												// Fetch top customers (by total amount spent or by order count)
												$num_orders =  $model->getRows("orders_mart", [
													"where" => ["user_id" => $customer['user_id']],
													"return_type" => "count"
												]);
												echo $num_orders . " Order(s)";
												?>
											</td>
											<td>£
												<?php
												// Fetch top customers (by total amount spent or by order count)
												$sum_orders = $model->getRows("orders_mart", [
													"select" => "SUM(total_amount) AS total_spent",
													"where" => [
														"user_id" => $customer['user_id'],
														"payment_status" => "paid"
													],
													"return_type" => "single"
												]);
												echo $sum_orders['total_spent'] ?? 0;

												?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="7" class="text-center">You have no new customer yet.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-xl-5 col-12 p-b-15">
				<!-- Top Sell Table -->
				<div class="card card-default Sold-card-table">
					<div class="card-header justify-content-between">
						<h2>Sold by Items</h2>
						<div class="tools">
							<button class="text-black-50 mr-2 font-size-20"><i
									class="mdi mdi-cached"></i></button>
							<div class="dropdown show d-inline-block widget-dropdown">
								<a class="dropdown-toggle icon-burger-mini" href="#" role="button"
									id="dropdown-units" data-bs-toggle="dropdown" aria-haspopup="true"
									aria-expanded="false" data-display="static"></a>
								<ul class="dropdown-menu dropdown-menu-right">
									<li class="dropdown-item"><a href="#">Action</a></li>
									<li class="dropdown-item"><a href="#">Another action</a></li>
									<li class="dropdown-item"><a href="#">Something else here</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="card-body py-0 compact-units" data-simplebar style="height: 534px;">
						<table class="table ">
							<tbody>
								<?php
								// Get top 10 products with order counts
								$topProducts = $model->getRows("order_items_mart", [
									"select" => "products.product_name, SUM(order_items_mart.quantity) as total_sold",
									"left_join" => [
										"products" => "ON order_items_mart.product_id = products.product_id"
									],
									"group_by" => "order_items_mart.product_id",
									"order_by" => "total_sold DESC",
									"limit" => "10"
								]);

								if (!empty($topProducts)):
									foreach ($topProducts as $product):
										// Calculate growth % (dummy value for now)
										$growth = rand(-50, 300); // Replace with real formula if you track historical sales
										$iconClass = $growth >= 0 ? "mdi mdi-arrow-up-bold text-success" : "mdi mdi-arrow-down-bold text-danger";
								?>
										<tr>
											<td class="text-dark"><?php echo htmlspecialchars($product['product_name']); ?></td>
											<td class="text-center"><?php echo (int)$product['total_sold']; ?></td>
											<td class="text-right">
												<?php echo abs($growth); ?>%
												<i class="<?php echo $iconClass; ?> pl-1 font-size-12"></i>
											</td>
										</tr>
									<?php
									endforeach;
								else:
									?>
									<tr>
										<td colspan="3" class="text-center">No product sales yet.</td>
									</tr>
								<?php endif; ?>
							</tbody>

							
						</table>

					</div>
					<div class="card-footer d-flex flex-wrap bg-white">
						<a href="#" class="text-uppercase py-3">View Report</a>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12 p-b-15">
				<!-- Recent Order Table -->
				<div class="card card-table-border-none card-default recent-orders" id="recent-orders">
					<div class="card-header justify-content-between">
						<h2>Recent Orders</h2>
						<div class="date-range-report">
							<span></span>
						</div>
					</div>
					<div class="card-body pt-0 pb-5">
						<table class="table card-table table-responsive table-responsive-large"
							style="width:100%">
							<thead>
								<tr>
									<th>Order ID</th>
									<th>Product Name</th>
									<th class="d-none d-lg-table-cell">Units</th>
									<th class="d-none d-lg-table-cell">Order Date</th>
									<th class="d-none d-lg-table-cell">Order Cost</th>
									<th>Status</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php
								// Fetch last 10 orders
								$orders = $model->getRows("orders_mart", [
									"limit" => "10"
								]);
								?>
								<?php if (!empty($orders)): ?>
									<?php foreach ($orders as $order): ?>
										<tr>
											<td><?= strtoupper($order['order_reference'] ?? "N/A"); ?></td>
											<td>
												<a class="text-dark" href="#">
													<?= strtoupper(($order['firstname'] ?? '') . " " . ($order['lastname'] ?? 'N/A')); ?>
												</a>
											</td>
											<td class="d-none d-lg-table-cell">
												<?php
												$countItem = $model->getRows("order_items_mart", [
													"where" => ["order_item_id" => $order['order_tbl_id']],
													"return_type" => "count"
												]);
												echo $countItem . " Unit(s)";
												?>
											</td>
											<td class="d-none d-lg-table-cell">
												<?= date("M d, Y", strtotime($order['created_at'] ?? "now")); ?>
											</td>
											<td class="d-none d-lg-table-cell">
												£<?= number_format($order['total_amount'] ?? 0, 2); ?>
											</td>
											<td>
												<?php
												$status = strtolower($order['payment_status'] ?? "pending");
												$badgeClass = match ($status) {
													'paid'    => 'success',
													'failed'  => 'danger',
													'pending' => 'warning',
													default   => 'secondary'
												};
												?>
												<span class="badge badge-<?= $badgeClass ?>">
													<?= strtoupper($status); ?>
												</span>
											</td>
											<td class="text-right">
												<div class="dropdown show d-inline-block widget-dropdown">
													<a class="dropdown-toggle icon-burger-mini" href="#"
														role="button" id="dropdown-recent-order<?= $order['order_tbl_id']; ?>"
														data-bs-toggle="dropdown" aria-haspopup="true"
														aria-expanded="false" data-display="static"></a>
													<ul class="dropdown-menu dropdown-menu-right">
														<li class="dropdown-item">
															<a href="order-detail.php?id=<?= $order['order_tbl_id']; ?>">View</a>
														</li>
														<li class="dropdown-item">
															<a href="order-delete.php?id=<?= $order['order_tbl_id']; ?>"
																onclick="return confirm('Are you sure you want to remove this order?');">Remove</a>
														</li>
													</ul>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="7" class="text-center">You have no orders yet.</td>
									</tr>
								<?php endif; ?>
							</tbody>

						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="row">

		</div>
	</div> <!-- End Content -->
</div>
<!-- End Content Wrapper -->

<?php
include './inc/footer.php';
?>