<?php
require_once "./query.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email.";
        header("Location: resend_verification.php");
        exit;
    }

    try {
        // Check if user exists
        $exists = $user->emailExists($email);
        if (!$exists) {
            $_SESSION['error'] = "No account found with this email.";
            header("Location: resend_verification.php");
            exit;
        }

        // Get user record
        $userData = $user->getByEmail($email);

        if ($userData['verified']) {
            $_SESSION['info'] = "This email is already verified. Please log in.";
            header("Location: login.php");
            exit;
        }

        // Send new verification email
        $sent = $user->sendVerificationEmail($email);

        if ($sent) {
            $_SESSION['success'] = "A new verification email has been sent to your inbox.";
        } else {
            $_SESSION['error'] = "Failed to send verification email. Please try again later.";
        }
        header("Location: resend_verification.php");
        exit;

    } catch (Exception $e) {
        error_log("Resend verification error: " . $e->getMessage());
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: resend_verification.php");
        exit;
    }
}
?>

<!-- Simple form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resend Verification Email</title>
</head>
<body>
    <h2>Resend Verification Email</h2>

    <?php
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<p style='color:green'>" . $_SESSION['success'] . "</p>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['info'])) {
        echo "<p style='color:blue'>" . $_SESSION['info'] . "</p>";
        unset($_SESSION['info']);
    }
    ?>

    <form action="" method="post">
        <label for="email">Enter your registered email:</label><br>
        <input type="email" name="email" required>
        <button type="submit">Resend Verification</button>
    </form>
</body>
</html>
