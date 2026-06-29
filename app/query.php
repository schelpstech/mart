<?php
if (file_exists('../../controller/start.inc.php')) {
    include '../../controller/start.inc.php';
} elseif (file_exists('../controller/start.inc.php')) {
    include '../controller/start.inc.php';
} else {
    include './controller/start.inc.php';
}

include_once __DIR__ . '/../controller/admin_helpers.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/./vendor/autoload.php';;

//Prefill phone number and Email Address
if (!empty($_SESSION['user_email'])) {
    $userData = $user->getByEmail($_SESSION['user_email']);
}
// Get all products 
$allproducts = $model->getRows("products", [
    'where' => [ 'status' => 'Active'],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'left_join' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);
// Get products where section_id = 1
$products_in_section_1 = $model->getRows("products", [
    'where' => ["section_id" => 1, 'status' => 'Active'],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'left_join' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);

// Get products in category = Nails Only
$products_in_salon_nail = $model->getRows("products", [
    'where' => ["category_id" => 1, 'status' => 'Active'],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'left_join' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);
// Get products in category = Facials Only
$products_in_salon_facial = $model->getRows("products", [
    'where' => ["category_id" => 2, 'status' => 'Active'],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'left_join' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);
// Get products in category = Hair Only
$products_in_salon_hair = $model->getRows("products", [
    'where' => ["category_id" => 3, 'status' => 'Active'],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'left_join' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);


// Get the latest testimonials, supporting both legacy and migrated review schemas.
$latest_testimonials = [];
if (isset($model) && qs_admin_table_exists($model, 'testimonials')) {
    $testimonialColumns = qs_admin_table_columns($model, 'testimonials');
    $testimonialNameCandidates = ['name', 'testimonial_name', 'customer_name', 'client_name'];
    $testimonialLocationCandidates = ['location', 'testimonial_role', 'role', 'designation'];
    $testimonialMessageCandidates = ['message', 'testimonial_message', 'content', 'review', 'testimonial'];
    $testimonialNameCol = qs_admin_pick_column($testimonialColumns, $testimonialNameCandidates);
    $testimonialMessageCol = qs_admin_pick_column($testimonialColumns, $testimonialMessageCandidates);
    $testimonialStatusCol = qs_admin_pick_column($testimonialColumns, ['testimonial_status', 'status']);
    $testimonialCreatedCol = qs_admin_pick_column($testimonialColumns, ['testimonial_created_at', 'created_at']);

    if ($testimonialNameCol && $testimonialMessageCol) {
        $testimonialSelect = [
            qs_admin_coalesce_select($testimonialColumns, $testimonialNameCandidates, 'name', "'Customer'"),
            qs_admin_coalesce_select($testimonialColumns, $testimonialLocationCandidates, 'location'),
            qs_admin_coalesce_select($testimonialColumns, $testimonialMessageCandidates, 'message')
        ];

        $testimonialParams = [
            'select' => implode(', ', $testimonialSelect),
            'limit' => 5,
            'return_type' => 'all'
        ];

        if ($testimonialStatusCol && qs_admin_safe_identifier($testimonialStatusCol)) {
            $testimonialParams['where_raw'] = "LOWER(`{$testimonialStatusCol}`) = 'active'";
        }

        if ($testimonialCreatedCol) {
            $testimonialParams['order_by'] = $testimonialCreatedCol . ' DESC';
        }

        try {
            $latest_testimonials = $model->getRows("testimonials", $testimonialParams);
        } catch (\Exception $e) {
            $latest_testimonials = [];
        }
    }
}
