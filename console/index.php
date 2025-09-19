<?php
require_once("../app/query.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Admin Login - <?php echo APP_NAME; ?>">

    <title>Admin Login | <?php echo APP_NAME; ?></title>

    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet" />

    <!-- Ekka CSS -->
    <link id="ekka-css" rel="stylesheet" href="assets/css/ekka.css" />

    <!-- FAVICON -->
    <link href="assets/img/favicon.png" rel="shortcut icon" />
</head>

<body class="sign-inup" id="body">
    <div class="container d-flex align-items-center justify-content-center form-height-login pt-24px pb-24px">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10">
                <div class="card">
                    <div class="card-header bg-default">
                        <div class="ec-brand" >
                            <a href="index.php" title="Admin Panel">
                                <img class="ec-brand-icon" src="assets/img/logo/nlogo.png" alt="logo" />
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-5">
                        <h4 class="text-dark mb-4">Admin Sign In</h4>

                        <!-- Flash messages -->
                        <?php $utility->displayFlash(); ?>

                        <form action="../app/admin/admin_access_action.php" method="post" autocomplete="off">
                            <div class="row">
                                <!-- Email -->
                                <div class="form-group col-md-12 mb-4">
                                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                                </div>

                                <!-- Password with toggle -->
                                <div class="form-group col-md-12 mb-3 position-relative">
                                    <input type="password" class="form-control" name="password" id="passwordField" placeholder="Password" required>
                                    <span id="togglePassword"
                                        style="position: absolute; right: 12px; top: 10px; cursor: pointer; font-size: 16px; color: #555;">
                                        👁
                                    </span>
                                </div>

                                <!-- Remember + Forgot -->
                                <div class="col-md-12">
                                    <div class="d-flex my-2 justify-content-between">
                                        <div class="d-inline-block mr-3">
                                            <div class="control control-checkbox">Remember me
                                                <input type="checkbox" name="remember" />
                                                <div class="control-indicator"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit -->
                                    <button type="submit"
                                        name="action"
                                        value="<?php echo $utility->inputEncode('admin_login'); ?>"
                                        class="btn btn-primary btn-block mb-4">
                                        Sign In
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Show/Hide Password -->
                        <script>
                            const passwordField = document.getElementById("passwordField");
                            const togglePassword = document.getElementById("togglePassword");

                            togglePassword.addEventListener("click", function() {
                                const type = passwordField.type === "password" ? "text" : "password";
                                passwordField.type = type;
                                this.textContent = type === "password" ? "👁" : "🙈";
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Javascript -->
    <script src="assets/plugins/jquery/jquery-3.5.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/jquery-zoom/jquery.zoom.min.js"></script>
    <script src="assets/plugins/slick/slick.min.js"></script>

    <!-- Ekka Custom -->
    <script src="assets/js/ekka.js"></script>
</body>

</html>