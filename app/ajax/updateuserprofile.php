<?php
require_once '../query.php'; // adjust path

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['user_id'];

    if (!$userId) {
        echo json_encode(["success" => false, "message" => "User ID missing"]);
        exit;
    }

    // Collect sanitized input
    $firstName = trim($_POST["first_name"] ?? "");
    $lastName  = trim($_POST["last_name"] ?? "");
    $address1  = trim($_POST["address1"] ?? "");
    $address2  = trim($_POST["address2"] ?? "");
    $city      = trim($_POST["city"] ?? "");
    $county    = trim($_POST["county"] ?? "");
    $postcode  = trim($_POST["postcode"] ?? "");
    $phone2    = trim($_POST["phone2"] ?? "");

    try {
        // Check if profile exists
        $options = [
            "where"       => ["user_id" => $userId],
            "return_type" => "single"
        ];
        $existing = $model->getRows("user_profiles", $options);

        $data = [
            "firstname" => $firstName,
            "lastname"  => $lastName,
            "address1"  => $address1,
            "address2"  => $address2,
            "city"      => $city,
            "county"    => $county,
            "postcode"   => $postcode,
            "phone2"     => $phone2,
        ];

        if ($existing) {
            // Update existing profile
            $where = ["user_id" => $userId];
            $updated = $model->update("user_profiles", $data, $where);

            echo json_encode([
                "success" => $updated,
                "message" => $updated ? "Profile updated successfully" : "No changes made"
            ]);
        } else {
            // Insert new profile
            $data["user_id"] = $userId;
            $inserted = $model->insert("user_profiles", $data);

            echo json_encode([
                "success" => $inserted ? true : false,
                "message" => $inserted ? "Profile created successfully" : "Failed to create profile"
            ]);
        }
        exit;
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error saving profile: " . $e->getMessage()
        ]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}
