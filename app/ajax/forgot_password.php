<?php
require_once '../query.php'; // adjust path

header("Content-Type: application/json");

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email is required"]);
    exit;
}

// Check if email exists
$user = $model->getRows("users_mart", [
    "where" => ["email" => $email],
    "return_type" => "single"
]);

if (!$user) {
    echo json_encode(["success" => false, "message" => "No account found with this email."]);
    exit;
}

$userId = $user['user_id'];
$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Store token
$model->insert("password_resets", [
    "user_id" => $userId,
    "token" => $token,
    "expires_at" => $expires
]);

// Reset link
$resetLink = "http://localhost/mart/view/resetpassword.php?token=$token";

// TODO: Replace with PHPMailer or your mail utility
require_once "../mail_config.php";

// Build email content
$subject = "Password Reset Request - Queeny Store";
$htmlContent = $utility->resetPasswordEmailTemplate($resetLink);

// Send email
if (sendEmail($email, $subject, $htmlContent)) {
    echo json_encode(["success" => true, "message" => "A reset link has been sent to your email."]);
    exit;
} else {
    echo json_encode(["success" => false, "message" => "Failed to send reset email."]);
    exit;
}
