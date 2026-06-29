<?php
include "../query.php";
include_once "../../controller/admin_helpers.php";

function review_redirect()
{
    header("Location: ../../console/reviews.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $utility->setFlash("warning", "Restricted action.");
    review_redirect();
}

$action = $utility->inputDecode($_POST['action'] ?? '');

if (!qs_admin_table_exists($model, 'testimonials')) {
    $utility->setFlash("danger", "Reviews table is not installed. Run the admin menu integration migration.");
    review_redirect();
}

$columns = qs_admin_table_columns($model, 'testimonials');
$idCol = qs_admin_pick_column($columns, ['testimonial_id', 'id']);
$nameCol = qs_admin_pick_column($columns, ['name', 'testimonial_name', 'customer_name', 'client_name']);
$roleCol = qs_admin_pick_column($columns, ['location', 'testimonial_role', 'role', 'designation']);
$messageCol = qs_admin_pick_column($columns, ['message', 'testimonial_message', 'content', 'review', 'testimonial']);
$ratingCol = qs_admin_pick_column($columns, ['testimonial_rating', 'rating']);
$statusCol = qs_admin_pick_column($columns, ['testimonial_status', 'status']);
$createdCol = qs_admin_pick_column($columns, ['testimonial_created_at', 'created_at']);
$updatedCol = qs_admin_pick_column($columns, ['testimonial_updated_at', 'updated_at']);

if (!$idCol || !$nameCol || !$messageCol) {
    $utility->setFlash("danger", "Reviews table is missing required columns.");
    review_redirect();
}

try {
    if ($action === 'add_review') {
        $name = trim(strip_tags($_POST['name'] ?? ''));
        $role = trim(strip_tags($_POST['role'] ?? ''));
        $message = trim(strip_tags($_POST['message'] ?? ''));
        $rating = min(5, max(1, (int)($_POST['rating'] ?? 5)));
        $status = strtolower(trim($_POST['status'] ?? 'active'));
        $status = in_array($status, ['active', 'inactive', 'pending'], true) ? $status : 'active';

        if ($name === '' || $message === '') {
            $utility->setFlash("danger", "Customer name and review are required.");
            review_redirect();
        }

        $fields = [
            $nameCol => $name,
            $messageCol => $message
        ];

        if ($roleCol) $fields[$roleCol] = $role;
        if ($ratingCol) $fields[$ratingCol] = $rating;
        if ($statusCol) $fields[$statusCol] = $status;
        if ($createdCol) $fields[$createdCol] = date("Y-m-d H:i:s");
        if ($updatedCol) $fields[$updatedCol] = date("Y-m-d H:i:s");

        $model->insert('testimonials', $fields);
        $utility->setFlash("success", "Review added successfully.");
        review_redirect();
    }

    if ($action === 'update_review_status') {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        $status = strtolower(trim($_POST['status'] ?? ''));
        if (!$statusCol || $reviewId <= 0 || !in_array($status, ['active', 'inactive', 'pending'], true)) {
            $utility->setFlash("danger", "Invalid review status update.");
            review_redirect();
        }

        $fields = [$statusCol => $status];
        if ($updatedCol) $fields[$updatedCol] = date("Y-m-d H:i:s");
        $model->update('testimonials', $fields, [$idCol => $reviewId]);
        $utility->setFlash("success", "Review status updated.");
        review_redirect();
    }

    if ($action === 'delete_review') {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        if ($reviewId <= 0) {
            $utility->setFlash("danger", "Invalid review.");
            review_redirect();
        }

        $model->delete('testimonials', [$idCol => $reviewId]);
        $utility->setFlash("success", "Review deleted.");
        review_redirect();
    }

    $utility->setFlash("warning", "Unknown review action.");
} catch (Exception $e) {
    $utility->setFlash("danger", "Review action failed: " . $e->getMessage());
}

review_redirect();
