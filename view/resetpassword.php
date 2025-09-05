<?php
include './inc/head.php';

// Get token from query string
$token = $_GET['token'] ?? '';
$isValid = false;
$userEmail = '';

// Check token in DB
if ($token) {
    $row = $model->getRows('password_resets', [
        "where" => ["token" => $token],
        "return_type" => "single",
        'left_join' => [
            'users_mart' => ' on users_mart.user_id = password_resets.user_id'
        ]
    ]);

    if ($row && strtotime($row['expires_at']) > time()) {
        $isValid = true;
        $userEmail = $row['email'];
    }
}
?>

<body class="register_page">
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

    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">Reset Password</h2>
                        <h2 class="ec-title">Reset Password</h2>
                        <p class="sub-title mb-3">Set a new password for your account</p>
                    </div>
                </div>

                <div class="ec-login-wrapper">
                    <div class="ec-login-container">
                        <div class="ec-login-form">
                            <?php $utility->displayFlash(); ?>

                            <?php if ($isValid): ?>
                                <form action="../app/user_access_action.php" method="POST" autocomplete="off">
                                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                                    <span class="ec-login-wrap">
                                        <label>Email*</label>
                                        <input type="email" name="email" value="<?= htmlspecialchars($userEmail) ?>" readonly />
                                    </span>

                                    <span class="ec-login-wrap">
                                        <label>New Password*</label>
                                        <input type="password" id="password" name="password" placeholder="Enter new password" required />
                                        <small id="passwordHelp" class="text-danger"></small>
                                    </span>

                                    <span class="ec-login-wrap">
                                        <label>Confirm Password*</label>
                                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required />
                                        <small id="confirmHelp" class="text-danger"></small>
                                    </span>

                                    <span class="ec-login-wrap">
                                        <input type="checkbox" id="showPassword">
                                        <label>Show Password</label>
                                    </span>

                                    <span class="ec-login-wrap ec-login-btn">
                                    <button class="btn btn-primary" name="action" value="<?php echo $utility->inputEncode('reset'); ?>" type="submit">Reset Password</button>
                                    <a href="login.php" class="btn btn-secondary">Login</a>
                                </span>
                                </form>

                            <?php else: ?>
                                <p class="text-danger">Invalid or expired reset link. Please request a new one.</p>
                            <?php endif; ?>
                        </div>
                        <div id="resetMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include './inc/footer.php'; ?>
</body>

</html>