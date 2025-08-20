<?php
header('Content-Type: application/json');
require_once '../query.php'; // adjust path

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $product = $model->getRows("products", [
    'where' => ["product_id" => $id],
    "return_type" => "single"
]);

    if ($product) {
        $images = [];

        // Load images from DB
        if (!empty($product['image_gallery'])) {
            $imgList = explode(',', $product['image_gallery']); // comma-separated list
            foreach ($imgList as $img) {
                $img = trim($img);
                // Ensure correct path format
                if ($img !== '') {
                    $images[] = 'assets/images/product/' . ltrim($img, '/');
                }
            }
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'name' => $product['product_name'],
                'description' => $product['description'],
                'new_price' => $product['price'],
                'old_price' => $product['discount_price'],
                'images' => $images
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
