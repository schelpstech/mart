<?php
include "../query.php"; // load session + db + model

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $utility->inputDecode($_POST['action'] ?? '');

    try {
        if ($action === 'this_form_adds_a_new_product') {
            // Collect inputs
            $productName       = trim($_POST['product_name'] ?? "");
            $slug              = trim($_POST['slug'] ?? "");
            $categoryId        = intval($_POST['category_id'] ?? 0);
            $shortDescription  = trim($_POST['short_description'] ?? "");
            $fullDescription   = trim($_POST['full_description'] ?? "");
            $brand             = trim($_POST['brand'] ?? "");
            $prod_model        = trim($_POST['model'] ?? "");
            $price             = floatval($_POST['price'] ?? 0);
            $quantity          = intval($_POST['quantity'] ?? 0);
            $tags              = trim($_POST['tags'] ?? "");
            $colors            = trim($_POST['colors'] ?? "");
            $sizes             = isset($_POST['sizes']) ? implode(",", $_POST['sizes']) : "";

            // Validation
            if (empty($productName) || empty($slug) || $categoryId <= 0 || $price <= 0 || $quantity <= 0) {
                $utility->setFlash("danger", "Please fill in all required fields.");
                header("Location: ../../console/add-product.php");
                exit;
            }

            // ✅ Fetch category name for SKU
            $category = $model->getById("categories", ["categoryTbl_id" => $categoryId]);
            $catCode  = strtoupper(substr(preg_replace("/[^A-Za-z0-9]/", "", $category['category_name'] ?? "CAT"), 0, 3));

            // ✅ Brand code for SKU
            $brandCode = !empty($brand) ? strtoupper(substr(preg_replace("/[^A-Za-z0-9]/", "", $brand), 0, 3)) : "GEN";

            // ✅ Sequential number
            $lastProduct = $model->getRows("products", [
                "order_by" => "product_id DESC",
                "limit"    => 1
            ]);
            $nextId = !empty($lastProduct) ? intval($lastProduct[0]['product_id']) + 1 : 1;
            $skuSeq = str_pad($nextId, 5, "0", STR_PAD_LEFT);

            // ✅ Final SKU
            $sku = "{$catCode}-{$brandCode}-{$skuSeq}";



            $mainImagePath = $utility->handleProductImageUploadedFile(
                "main_image",
                ["image/jpeg", "image/jpg", "image/png", "image/webp"],
                5 * 1024 * 1024,
                "../../view/assets/images/product/main"
            );

            if (is_array($mainImagePath) && isset($mainImagePath['error'])) {
                $utility->setFlash("danger", "Image upload failed: " . $mainImagePath['error']);
                header("Location: ../../console/add-product.php");
                exit;
            }

            // ✅ Now $mainImagePath is a string path, safe to store in DB

            // ✅ Handle multiple thumbnail uploads
            $thumbPaths = [];
            $thumbErrors = [];

            if (!empty($_FILES['thumbs']['name'][0])) {
                foreach ($_FILES['thumbs']['name'] as $key => $thumbName) {
                    if (!empty($thumbName)) {
                        // Build a single-file $_FILES entry for each thumb
                        $tmpFile = [
                            'name'     => $_FILES['thumbs']['name'][$key],
                            'type'     => $_FILES['thumbs']['type'][$key],
                            'tmp_name' => $_FILES['thumbs']['tmp_name'][$key],
                            'error'    => $_FILES['thumbs']['error'][$key],
                            'size'     => $_FILES['thumbs']['size'][$key]
                        ];
                        $_FILES['single_thumb'] = $tmpFile;

                        $thumbResult = $utility->handleProductImageUploadedFile(
                            "single_thumb",
                            ["image/jpeg", "image/jpg", "image/png", "image/webp"],
                            5 * 1024 * 1024,
                            "../../view/assets/images/product/thumbs"
                        );

                        if (is_array($thumbResult) && isset($thumbResult['error'])) {
                            // Store error with file name for debugging
                            $thumbErrors[] = "File '{$thumbName}': " . $thumbResult['error'];
                        } else {
                            // Success → save the relative path
                            $thumbPaths[] = $thumbResult;
                        }
                    }
                }
            }

            // Optionally handle errors (flash or log)
            if (!empty($thumbErrors)) {
                $utility->setFlash("danger", "Some thumbnails failed: " . implode("; ", $thumbErrors));
            }



            // ✅ Insert into DB
            $insertData = [
                "sku"                => $sku,
                "product_name"       => $productName,
                "product_slug"       => $slug,
                "category_id"        => $categoryId,
                "short_description"  => $shortDescription,
                "description"        => $fullDescription,
                "brand"              => $brand,
                "model"              => $prod_model,
                "price"              => $price,
                "stock_quantity"     => $quantity,
                "tags"               => $tags,
                "color_options"      => $colors,
                "size_options"       => $sizes,
                "image_main"         => $mainImagePath,
                "image_gallery"      => json_encode($thumbPaths),
                "status"             => "Active",
                "date_added"         => date("Y-m-d H:i:s")
            ];

            $inserted = $model->insert("products", $insertData);

            if ($inserted) {
                $utility->setFlash("success", "Product added successfully. SKU: {$sku}");
            } else {
                $utility->setFlash("danger", "Error saving product.");
            }
        } elseif ($action === 'this_form_updates_product') {

            // --- EDIT PRODUCT ---
            $product_id   = intval($_POST['product_id']);
            $name         = trim($_POST['product_name']);
            $slug         = trim($_POST['slug']);
            $brand        = trim($_POST['brand']);
            $modelName    = trim($_POST['model']);
            $shortDesc    = trim($_POST['short_description']);
            $fullDesc     = trim($_POST['full_description']);
            $price        = floatval($_POST['price']);
            $quantity     = intval($_POST['quantity']);
            $category_id  = intval($_POST['category_id']);
            $colors       = trim($_POST['colors']);
            $sizes        = isset($_POST['sizes']) ? implode(",", $_POST['sizes']) : '';
            $tags         = trim($_POST['group_tag']);

            // Handle Main Image
            if (!empty($_FILES['main_image']['name'])) {
                $mainImagePath = $utility->handleProductImageUploadedFile(
                    "main_image",
                    ["image/jpeg", "image/jpg", "image/png", "image/webp"],
                    5 * 1024 * 1024,
                    "../../view/assets/images/product/main"
                );

                if (is_array($mainImagePath) && isset($mainImagePath['error'])) {
                    $utility->setFlash("danger", "Image upload failed: " . $mainImagePath['error']);
                    header("Location: ../../console/product_mgr.php");
                    exit;
                } else {
                    $main_image = $mainImagePath;
                }
            } else {
                $main_image = $_SESSION['main_icon']; // default keep old
            }

            // ✅ Handle multiple thumbnail uploads
            $thumbsArray = [];
            $thumbErrors = [];

            if (!empty($_FILES['thumbs']['name'][0])) {
                foreach ($_FILES['thumbs']['name'] as $key => $thumbName) {
                    if (!empty($thumbName)) {
                        $tmpFile = [
                            'name'     => $_FILES['thumbs']['name'][$key],
                            'type'     => $_FILES['thumbs']['type'][$key],
                            'tmp_name' => $_FILES['thumbs']['tmp_name'][$key],
                            'error'    => $_FILES['thumbs']['error'][$key],
                            'size'     => $_FILES['thumbs']['size'][$key]
                        ];
                        $_FILES['single_thumb'] = $tmpFile;

                        $thumbResult = $utility->handleProductImageUploadedFile(
                            "single_thumb",
                            ["image/jpeg", "image/jpg", "image/png", "image/webp"],
                            5 * 1024 * 1024,
                            "../../view/assets/images/product/thumbs"
                        );

                        if (is_array($thumbResult) && isset($thumbResult['error'])) {
                            $thumbErrors[] = "File '{$thumbName}': " . $thumbResult['error'];
                        } else {
                            $thumbsArray[] = $thumbResult; // save filename
                        }
                    }
                }
            } else {
                $thumbsArray = json_decode($_SESSION['image_gallery'], true) ?? [];
            }

            // Prepare data array
            $updateData = [
                "product_name"      => $name,
                "product_slug"      => $slug,
                "brand"             => $brand,
                "model"             => $modelName,
                "short_description" => $shortDesc,
                "description"  => $fullDesc,
                "price"             => $price,
                "stock_quantity"    => $quantity,
                "category_id"       => $category_id,
                "color_options"            => $colors,
                "size_options"             => $sizes,
                "tags"         => $tags,
                "image_main"        => $main_image,
                "image_gallery"      => json_encode($thumbsArray) // ✅ stored as JSON
            ];


            $updated = $model->update("products", $updateData, ["product_id" => $product_id]);

            if ($updated) {
                $utility->setFlash("success", "Product updated successfully.");
            } else {
                $utility->setFlash("danger", "Failed to update product.");
            }
            header("Location: ../../console/product_mgr.php");
            exit;


            // You can keep your add-product logic here (this_form_adds_a_new_product)
        } elseif ($action === 'this_form_toggle_product_status') {
            $id = intval($_POST['product_id'] ?? 0);

            if ($id > 0) {
                // Get current status
                $prod = $model->getById("products", ["product_id" => $id]);

                if ($prod) {
                    $newStatus = ($prod['status'] === 'Active') ? 'Inactive' : 'Active';

                    $updated = $model->update(
                        "products",
                        ["status" => $newStatus],
                        ["product_id" => $id]
                    );

                    if ($updated) {
                        $utility->setFlash("success", "Product - ". $prod['product_name']." status updated to $newStatus successfully.");
                    } else {
                        $utility->setFlash("danger", "Failed to update Product status.");
                    }
                } else {
                    $utility->setFlash("danger", "Product not found.");
                }
            } else {
                $utility->setFlash("danger", "Invalid Product ID.");
            }

            header("Location: ../../console/product_mgr.php");
            exit;
        } elseif ($action === 'this_form_delete_this_product') {
            $id = intval($_POST['product_id'] ?? 0);

            if ($id > 0) {
                // Check if section has been deactivated first
                $checkprod = $model->getById("products", ["product_id" => $id]);
                if ($checkprod) {
                    $deleted = $model->update(
                        "products",
                        [
                            "status"  => "Deleted"
                        ],
                        ["product_id" => $id]
                    );
                    if ($deleted) {
                        $utility->setFlash("success", "Product - ". $checkprod['product_name']." deleted successfully.");
                    } else {
                        $utility->setFlash("danger", "Error deleting product.");
                    }
                } else {
                    $utility->setFlash("danger", "Invalid action or Product ID.");
                }
            } else {
                $utility->setFlash("danger", "Invalid action or Product ID.");
            }
            header("Location: ../../console/product_mgr.php");
            exit;
        } else {
            $utility->setFlash("danger", "Invalid action or Product ID.");
        }
    } catch (Exception $e) {
        $utility->setFlash("danger", "Unexpected error: " . $e->getMessage());
    }
}
header("Location: ../../console/add-product.php");
exit;
