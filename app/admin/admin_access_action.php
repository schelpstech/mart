<?php
require_once "../query.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $utility->inputDecode($_POST['action']);

    switch ($action) {

        case 'admin_login':
            $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            try {
                $admin = $model->getRows("admins", [
                    "where" => ["admin_email" => $email],
                    "return_type" => "single"
                ]);

                if ($admin && password_verify($password, $admin['admin_password'])) {
                    $_SESSION['admin_id']   = $admin['admin_id'];
                    $_SESSION['admin_name'] = $admin['admin_name'];
                    $_SESSION['admin_role'] = $admin['role'];

                    $utility->setFlash("success", "Welcome back, {$admin['admin_name']}!");
                    header("Location: ../../console/dashboard.php");
                    exit;
                } else {
                    $utility->setFlash("danger", "Invalid email or password");
                    header("Location: ../../console/index.php");
                    exit;
                }
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                $utility->setFlash("danger", "Something went wrong. Please try again.");
                header("Location: ../../console/index.php");
                exit;
            }

        case 'logout':
            $logout = $user->logout();
            if ($logout) {
                $utility->setFlash("success", "Logged out successfully.");
                header("Location: ../../console/index.php");
                exit;
            } else {
                $utility->setFlash("warning", "Logout failed. Please try again.");
                header("Location: ../../console/dashboard.php");
                exit;
            }

        default:
            $utility->setFlash("warning", "Unknown action requested.");
            header("Location: ../../console/index.php");
            exit;
    }
} else {
    $utility->setFlash("warning", "Error! Restricted Action.");
    header("Location: ../../console/index.php");
    exit;
}
