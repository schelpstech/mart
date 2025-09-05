<?php
include './inc/head.php';
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch orders for this user
$orders = $model->getRows("orders_mart", ["user_id" => $userId]);
?>

<body class="shop_page">
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <?php
    include './inc/header.php';
    include './inc/cart.php';
    include './inc/category.php';
    ?>

    <!-- Ec breadcrumb start -->

    <!-- Ec breadcrumb end -->
    <!-- User profile section -->
    <section class="ec-page-content ec-vendor-uploads ec-user-account section-space-p">
        <div class="container">
            <div class="row">
                <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="row ec_breadcrumb_inner">
                                    <div class="col-md-6 col-sm-12">
                                        <h2 class="ec-breadcrumb-title">User Profile</h2>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <!-- ec-breadcrumb-list start -->
                                        <ul class="ec-breadcrumb-list">
                                            <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                            <li class="ec-breadcrumb-item active">Profile</li>
                                        </ul>
                                        <!-- ec-breadcrumb-list end -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar Area Start -->
                <div class="ec-shop-leftside ec-vendor-sidebar col-lg-3 col-md-12">
                    <div class="ec-sidebar-wrap ec-border-box">
                        <!-- Sidebar Category Block -->
                        <div class="ec-sidebar-block">
                            <div class="ec-vendor-block">
                                <!-- <div class="ec-vendor-block-bg"></div>
                                <div class="ec-vendor-block-detail">
                                    <img class="v-img" src="assets/images/user/1.jpg" alt="vendor image">
                                    <h5>Mariana Johns</h5>
                                </div> -->
                                <div class="ec-vendor-block-items">
                                    <ul>
                                        <li><a href="profile.php" class="active">User Profile</a></li>
                                        <li><a href="order.php">Order History</a></li>
                                        <li><a href="viewcart.php">My Cart</a></li>
                                        <li><a href="checkout.php">Checkout</a></li>
                                        <li><a href="#">Track Order</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $profile = $user->getUserProfile($userId);

                // fallback values if no profile row yet
                $firstname   = $profile['firstname']   ?? '';
                $lastname    = $profile['lastname']    ?? '';
                $email       = $profile['email']       ?? '';
                $phone       = $profile['phone']       ?? '';
                $phone2      = $profile['phone2']      ?? '';
                $address1    = $profile['address1']    ?? '';
                $address2    = $profile['address2']    ?? '';
                $city        = $profile['city']        ?? '';
                $county     = $profile['county']     ?? '';
                ?>
                <div class="ec-shop-rightside col-lg-9 col-md-12">
                    <div class="ec-vendor-dashboard-card ec-vendor-setting-card">
                        <div class="ec-vendor-card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="ec-vendor-block-profile">
                                        <div class="ec-vendor-block-img space-bottom-30">
                                            <div class="ec-vendor-block-bg">
                                                <a href="javascript:void(0);"
                                                    onclick="loadUserProfile(<?= $_SESSION['user_id']; ?>)"
                                                    class="btn btn-lg btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#edit_modal">Edit Detail</a>

                                            </div>
                                            <div class="ec-vendor-block-detail">
                                                <img class="v-img" src="assets/images/user/icon.png" alt="Profile picture">
                                                <h5 class="name"><?= htmlspecialchars($firstname . ' ' . $lastname) ?></h5>
                                                <p>( <?= !empty($profile['occupation']) ? htmlspecialchars($profile['occupation']) : 'Customer' ?> )</p>
                                            </div>
                                            <p>Hello <span><?= htmlspecialchars($firstname) ?></span>!</p>
                                            <p>From your account you can easily view and track orders. You can manage and change your account information like address, contact information and history of orders.</p>
                                        </div>

                                        <h5>Account Information</h5>
                                        <div class="row">
                                            <!-- Email -->
                                            <div class="col-md-6 col-sm-12">
                                                <div class="ec-vendor-detail-block ec-vendor-block-email space-bottom-30">
                                                    <h6>E-mail address
                                                        
                                                    </h6>
                                                    <ul>
                                                        <li><strong>Email : </strong><?= htmlspecialchars($email) ?></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Phone -->
                                            <div class="col-md-6 col-sm-12">
                                                <div class="ec-vendor-detail-block ec-vendor-block-contact space-bottom-30">
                                                    <h6>Contact number
                                                        
                                                    </h6>
                                                    <ul>
                                                        <li><strong>Phone Number 1 : </strong><?= htmlspecialchars($phone) ?></li>
                                                        <li><strong>Phone Number 2 : </strong><?= htmlspecialchars($phone2) ?></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Address -->
                                            <div class="col-md-6 col-sm-12">
                                                <div class="ec-vendor-detail-block ec-vendor-block-address mar-b-30">
                                                    <h6>Address
                                                        
                                                    </h6>
                                                    <ul>
                                                        <li><strong>Home : </strong><?= htmlspecialchars($address1 . ', ' . $address2) ?></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Shipping -->
                                            <div class="col-md-6 col-sm-12">
                                                <div class="ec-vendor-detail-block ec-vendor-block-address">
                                                    <h6>Shipping Address
                                                        
                                                    </h6>
                                                    <ul>
                                                        <li><strong>City : </strong><?= htmlspecialchars($city) ?>, <?= htmlspecialchars($county) ?></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div> <!-- row -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- End User profile section -->
    <!-- Modal -->
    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="ec-vendor-block-img space-bottom-30">
                            <div class="ec-vendor-block-bg cover-upload">
                                <div class="thumb-upload">
                                    <div class="thumb-edit">
                                    </div>
                                    <div class="thumb-preview ec-preview">
                                        <div class="image-thumb-preview">
                                            <img class="v-img" src="assets/images/user/banner.png" alt="Profile picture">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ec-vendor-block-detail">
                                <div class="thumb-upload">
                                    <div class="thumb-edit">
                                        
                                    </div>
                                    <div class="thumb-preview ec-preview">
                                        <div class="image-thumb-preview">
                                            <img class="v-img" src="assets/images/user/icon.png" alt="Profile picture">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ec-vendor-upload-detail">
                                <form id="editProfileForm" class="row g-3">
                                    <input type="hidden" id="user_id" name="user_id">

                                    <div class="col-md-6 space-t-15">
                                        <label class="form-label">First name</label>
                                        <input type="text" id="first_name" name="first_name" class="form-control">
                                    </div>
                                    <div class="col-md-6 space-t-15">
                                        <label class="form-label">Last name</label>
                                        <input type="text" id="last_name" name="last_name" class="form-control">
                                    </div>

                                    <div class="col-md-12 space-t-15">
                                        <label class="form-label">Address 1</label>
                                        <input type="text" id="address1" name="address1" class="form-control">
                                    </div>
                                    <div class="col-md-12 space-t-15">
                                        <label class="form-label">Address 2</label>
                                        <input type="text" id="address2" name="address2" class="form-control">
                                    </div>
                                    <div class="col-md-12 space-t-15">
                                        <label class="form-label">City</label>
                                        <input type="text" id="city" name="city" class="form-control">
                                    </div>
                                    <div class="col-md-12 space-t-15">
                                        <label class="form-label">County</label>
                                        <input type="text" id="county" name="county" class="form-control">
                                    </div>
                                    <div class="col-md-12 space-t-15">
                                        <label class="form-label">Post Code</label>
                                        <input type="text" id="postcode" name="postcode" class="form-control">
                                    </div>

                                    <!-- Email from users_mart (NOT editable) -->
                                    <div class="col-md-12 space-t-15">
                                        <label class="form-label">Email Address</label>
                                        <input type="text" id="email" name="email" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-6 space-t-15">
                                        <label class="form-label">Phone number 1</label>
                                        <input type="text" id="phone1" name="phone1" class="form-control">
                                    </div>
                                    <div class="col-md-6 space-t-15">
                                        <label class="form-label">Phone number 2</label>
                                        <input type="text" id="phone2" name="phone2" class="form-control">
                                    </div>

                                    <div class="col-md-12 space-t-15">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <a href="#" class="btn btn-lg btn-secondary qty_close" data-bs-dismiss="modal" aria-label="Close">Close</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->

    <?php include "./inc/footer.php"; ?>