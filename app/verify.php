<?php
require_once "./query.php";
if (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']);

    try {
        $verified = $user->verifyAccount($token);

        if ($verified) {
            $_SESSION['success'] = "Your email has been successfully verified! You can now log in.";
            header("Location: ../view/login.php");
            exit;
        } else {
            $_SESSION['error'] = "Invalid or expired verification link.";
            header("Location: ../view/resend_verification.php");
            exit;
        }

    } catch (Exception $e) {
        error_log("Verification error: " . $e->getMessage());
        $_SESSION['error'] = "Something went wrong during verification.";
        header("Location: ../view/login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: login.php");
    exit;
}
