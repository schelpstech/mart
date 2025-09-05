<?php
header("Content-Type: application/json");
require_once '../query.php'; // adjust path

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['user_id'];

    if (!$userId) {
        echo json_encode(["success" => false, "message" => "User ID missing"]);
        exit;
    }

    try {
        // First, get email + phone from users_mart
        $userOptions = [
            "where"       => ["user_id" => $userId],
            "return_type" => "single"
        ];
        $user = $model->getRows("users_mart", $userOptions);

        if (!$user) {
            echo json_encode(["success" => false, "message" => "User not found"]);
            exit;
        }

        // Next, check if profile exists
        $profileOptions = [
            "where"       => ["user_id" => $userId],
            "return_type" => "single"
        ];
        $profile = $model->getRows("user_profiles", $profileOptions);

        if ($profile) {
            // Merge user + profile data
            $data = array_merge($user, $profile);
        } else {
            // Only user data available
            $data = $user;
        }

        echo json_encode(["success" => true, "data" => $data]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error fetching profile: " . $e->getMessage()
        ]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}
