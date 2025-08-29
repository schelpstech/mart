<?php
require_once "./query.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $utility->inputDecode($_POST['action']);

    switch ($action) {

        case 'register':
            $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone    = htmlspecialchars(trim($_POST['phone']));
            $password = $_POST['password'];
            $confirm  = $_POST['confirm_password'];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $utility->setFlash("danger", "Invalid email address.");
                header("Location: ../view/register.php");
                exit;
            }

            if ($password !== $confirm) {
                $utility->setFlash("danger", "Passwords do not match.");
                header("Location: ../view/register.php");
                exit;
            }

            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
                $utility->setFlash("danger", "Password must be at least 8 characters and contain uppercase, lowercase, number, and symbol.");
                header("Location: ../view/register.php");
                exit;
            }

            try {
                if ($user->emailExists($email)) {
                    $utility->setFlash("danger", "Email already registered.");
                    header("Location: ../view/register.php");
                    exit;
                }

                $userId = $user->register($email, $phone, $password);

                if ($userId) {
                    $sent = $user->sendVerificationEmail($email);
                    $msg  = $sent
                        ? "Registration successful! Please check your email to verify your account."
                        : 'Account created, but failed to send verification email.<a href="./resendverification.php"><b>Click to resend link</b></a>';
                    $utility->setFlash($sent ? "success" : "warning", $msg);

                    header("Location: ../view/login.php");
                    exit;
                } else {
                    $utility->setFlash("danger", "Registration failed. Please try again.");
                    header("Location: ../view/register.php");
                    exit;
                }
            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                $utility->setFlash("danger", "Something went wrong. Debug: " . $e->getMessage());
                header("Location: ../view/register.php");
                exit;
            }


        case 'login':
            $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            try {
                $userData = $user->login($email, $password);

                if ($userData['status']) {

                    $utility->setFlash("success", $userData['message']);
                    header("Location: ../view/checkout.php");
                    exit;
                } else {
                    // Login failed
                    $utility->setFlash("danger", $userData['message']);
                    header("Location: ../view/login.php");
                    exit;
                }
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                $utility->setFlash("danger", "Something went wrong. Debug: " . $e->getMessage());
                header("Location: ../view/login.php");
                exit;
            }
        case 'resend_verification':
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $utility->setFlash("danger", "Invalid email format.");
                header("Location: ../view/resendverification.php");
                exit;
            }

            try {
                $userData = $user->getByEmail($email);

                if (!$userData) {
                    $utility->setFlash("danger", "No account found with this email.");
                    header("Location: ../view/resendverification.php");
                    exit;
                }

                if ($userData['verified']) {
                    $utility->setFlash("info", "This email is already verified.");
                    header("Location: ../view/login.php");
                    exit;
                }

                $sent = $user->sendVerificationEmail($email);

                if ($sent) {
                    $utility->setFlash("success", "A new verification email has been sent.");
                } else {
                    $utility->setFlash("danger", "Failed to send verification email.");
                }

                header("Location: ../view/resend_verification.php");
                exit;
            } catch (Exception $e) {
                error_log("Resend verification error: " . $e->getMessage());
                $utility->setFlash("danger", "Something went wrong. Debug: " . $e->getMessage());
                header("Location: ../view/resend_verification.php");
                exit;
            }

        case 'logout':
            $logout = $user->logout();
            if ($logout) {
                $utility->setFlash("success", "Logged out successfully.");
                header("Location: ../view/login.php");
                exit;
            } else {
                $utility->setFlash("warning", "Logout failed. Please try again.");
                header("Location: ../view/checkout.php");
                exit;
            }
        default:
            $utility->setFlash("warning", "Unknown action requested.");
            header("Location: ../view/login.php");
            exit;
    }
} else {
    $utility->setFlash("warning", "Error! Restricted Action.");
    header("Location: ../view/register.php");
    exit;
}
