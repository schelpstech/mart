<?php
$pageTitle = "Users Manager"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';
?>

<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-contacts">
            <div>
                <h1>User List</h1>
                <p class="breadcrumbs"><span><a href="index.html">Home</a></span>
                    <span><i class="mdi mdi-chevron-right"></i></span>User
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addUser"> Add User
                </button>
            </div>
        </div>
        <?php
        // Fetch users
        $users = $model->getRows("users_mart", [
            'left_join' => [
        'user_profiles' => ' on users_mart.user_id = user_profiles.user_id'
            ],
            "order_by" => "users_mart.created_at DESC"
        ]);

        // Loop users and calculate total buy from orders
        ?>
        <div class="row">
            <div class="col-12">
                <div class="ec-vendor-list card card-default">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="responsive-data-table" class="table">
                                <thead>
                                    <tr>
                                        <th>Profile</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Total Buy (£)</th>
                                        <th>Status</th>
                                        <th>Join On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $user): ?>
                                            <?php
                                            // profile picture
                                            $profile = !empty($user['profile_pic'])
                                                ? "../view/assets/images/users/" . $user['profile_pic']
                                                : "../view/assets/images/user/icon.png";
                                            // status
                                            $status = $user['verified'] == 1 ? "Verified" : "Unverified";

                                            // join date
                                            $joinDate = !empty($user['created_at'])
                                                ? date("Y-m-d", strtotime($user['created_at']))
                                                : "-";

                                            // ✅ calculate total buy from orders
                                            $orders = $model->getRows("orders_mart", [
                                                "select" => "SUM(total_amount) as total_spent",
                                                "where"  => ["user_id" => $user['user_id']]
                                            ]);

                                            $totalBuy = !empty($orders[0]['total_spent']) ? number_format($orders[0]['total_spent'], 2) : "0.00";
                                            ?>
                                            <tr>
                                                <td><img class="vendor-thumb" src="<?= $profile ?>" alt="user profile" /></td>
                                                <td><?= htmlspecialchars($user['lastname'] ." ".$user['lastname']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                                <td><?= $totalBuy ?></td>
                                                <td><?= $status ?></td>
                                                <td><?= $joinDate ?></td>
                                                <td>
                                                    <div class="btn-group mb-1">
                                                        <button type="button" class="btn btn-outline-success">Info</button>
                                                        <button type="button"
                                                            class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false" data-display="static">
                                                            <span class="sr-only">Info</span>
                                                        </button>

                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="edit-user.php?id=<?= $user['user_id'] ?>">Edit</a>
                                                            <a class="dropdown-item" href="delete-user.php?id=<?= $user['user_id'] ?>"
                                                                onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No users found.</td>
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