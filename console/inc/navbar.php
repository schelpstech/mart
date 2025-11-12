	<!--  WRAPPER  -->
	<div class="wrapper">

		<!-- LEFT MAIN SIDEBAR -->
		<div class="ec-left-sidebar ec-bg-sidebar">
			<div id="sidebar" class="sidebar ec-sidebar-footer">

				<div class="ec-brand">
					<a href="dashboard.php" title="<?php echo APP_NAME; ?>">
						<img class="ec-brand-icon" src="assets/img/logo/nlogo.png" alt="logo" />
						<span class="ec-brand-name text-truncate"><?php echo APP_NAME; ?></span>
					</a>
				</div>

				<!-- Sidebar navigation -->
				<div class="ec-navigation" data-simplebar>
					<ul class="nav sidebar-inner" id="sidebar-menu">

						<!-- Dashboard -->
						<li class="active">
							<a class="sidenav-item-link" href="dashboard.php">
								<i class="mdi mdi-view-dashboard-outline"></i>
								<span class="nav-text">Dashboard</span>
							</a>
							<hr>
						</li>

						<!-- Users -->
						<li>
							<a class="sidenav-item-link" href="users.php">
								<i class="mdi mdi-account-group"></i>
								<span class="nav-text">Users</span>
							</a>
						</li>

						<!-- Sections -->
						<li>
							<a class="sidenav-item-link" href="section_mgr.php">
								<i class="mdi mdi-tag-faces"></i>
								<span class="nav-text">Sections</span>
							</a>
						</li>

						<!-- Categories -->
						<li>
							<a class="sidenav-item-link" href="category_mgr.php">
								<i class="mdi mdi-dns-outline"></i>
								<span class="nav-text">Categories</span>
							</a>
						</li>
						<!-- Products -->
						<li class="has-sub">
							<a class="sidenav-item-link" href="javascript:void(0)">
								<i class="mdi mdi-palette-advanced"></i>
								<span class="nav-text">Products</span> <b class="caret"></b>
							</a>
							<div class="collapse">
								<ul class="sub-menu" data-parent="#sidebar-menu">
									<li><a class="sidenav-item-link" href="product_mgr.php">Product Manager</a></li>
									<li><a class="sidenav-item-link" href="add-product.php">Add Product</a></li>
									<li><a class="sidenav-item-link" href="servicemgr.php">Manage Services</a></li>
								</ul>
							</div>
						</li>

						<!-- Orders -->
						<li>
							<a class="sidenav-item-link" href="orders.php">
								<i class="mdi mdi-cart"></i>
								<span class="nav-text">Orders</span>
							</a>
						</li>

						<!-- Reviews -->
						<li>
							<a class="sidenav-item-link" href="reviews.php">
								<i class="mdi mdi-star-half"></i>
								<span class="nav-text">Reviews</span>
							</a>
						</li>


						<!-- Admins -->
						<li>
							<a class="sidenav-item-link" href="admins.php">
								<i class="mdi mdi-shield-account"></i>
								<span class="nav-text">Admins</span>
							</a>
						</li>

						<!-- Settings -->
						<li>
							<a class="sidenav-item-link" href="settings.php">
								<i class="mdi mdi-cog-outline"></i>
								<span class="nav-text">Settings</span>
							</a>
						</li>

						<!-- Logout -->
						<li>
							<a class="sidenav-item-link" href="../app/admin/admin_access_action.php?action=<?php echo $utility->inputEncode('logout'); ?>">
								<i class="mdi mdi-logout"></i>
								<span class="nav-text">Logout</span>
							</a>
						</li>

					</ul>
				</div>
			</div>
		</div>