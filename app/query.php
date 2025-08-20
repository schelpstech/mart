<?php
if (file_exists('../../controller/start.inc.php')) {
    include '../../controller/start.inc.php';
} elseif (file_exists('../controller/start.inc.php')) {
    include '../controller/start.inc.php';
} else {
    include './controller/start.inc.php';
};


// Get products where section_id = 1
$products_in_section_1 = $model->getRows("products", [
    'where' => ["section_id" => 1],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'joinl' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);

// Get products in category = Nails Only
$products_in_salon_nail = $model->getRows("products", [
    'where' => ["category_id" => 1],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'joinl' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);
// Get products in category = Facials Only
$products_in_salon_facial = $model->getRows("products", [
    'where' => ["category_id" => 2],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'joinl' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);
// Get products in category = Hair Only
$products_in_salon_hair = $model->getRows("products", [
    'where' => ["category_id" => 3],
    "order_by" => "product_tbl_record_time DESC", // optional ordering
    'joinl' => [
        'categories' => ' on products.category_id = categories.categoryTbl_id'
    ]
]);


// Get the latest 10 Testimonials
$latest_testimonials = $model->getRows("testimonials", [
    "order_by" => "testimonial_created_at DESC", // optional ordering
    "limit" => "5", // optional limit
]);