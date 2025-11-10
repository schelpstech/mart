<?php
include "../query.php"; // load session + db + model

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $utility->inputDecode($_POST['action'] ?? '');

    // ✅ ADD NEW SERVICE
    try {
        if ($action === 'this_form_adds_a_new_service') {
            $category_id     = intval($_POST['category_id'] ?? 0);
            $name            = trim($_POST['service_name'] ?? '');
            $slug            = trim($_POST['slug'] ?? '');
            $price           = floatval($_POST['service_price'] ?? 0);
            $duration        = trim($_POST['service_duration'] ?? '');
            $description     = trim($_POST['service_description'] ?? '');
            $iconFile        = $_FILES['service_icon'] ?? null;

            // ✅ Basic validation
            if ($category_id <= 0 || empty($name) || $price <= 0) {
                $utility->setFlash("danger", "Category, Service Name, and Price are required.");
                header("Location: ../../console/servicemgr.php");
                exit;
            }

            // ✅ Auto-generate slug if empty
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
            }

            // ✅ Check for uniqueness of service slug
            $exists = $model->exists("services", ["slug" => $slug]);
            if ($exists) {
                $utility->setFlash("warning", "A service with this slug already exists.");
                header("Location: ../../console/servicemgr.php");
                exit;
            }

            // ✅ Handle image upload
            $iconPath = "/services/default.png"; // fallback
            if (!empty($iconFile['name'])) {
                $uploadDir = "../../view/assets/images/services/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . "_" . preg_replace('/\s+/', '_', basename($iconFile['name']));
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($iconFile['tmp_name'], $targetPath)) {
                    $iconPath = "/services/" . $fileName;
                } else {
                    $utility->setFlash("danger", "Error uploading service image.");
                    header("Location: ../../console/servicemgr.php");
                    exit;
                }
            }

            // ✅ Insert service record
            $insert = $model->insert("services", [
                "services_categoryID"   => $category_id,
                "name"         => $name,
                "slug"         => $slug,
                "base_price"   => $price,
                "duration"     => $duration,
                "description"  => $description,
                "image"        => $iconPath,
                "status"       => "active",
                "created_at"   => date("Y-m-d H:i:s")
            ]);

            // ✅ Feedback message
            if ($insert) {
                $utility->setFlash("success", "Service added successfully!");
            } else {
                $utility->setFlash("danger", "Error adding service. Please try again.");
            }

            header("Location: ../../console/servicemgr.php");
            exit;
        } elseif ($action === "update_service") {
            $serviceId = $_POST['service_id'];
            $categoryId = $_POST['category_id'];
            $serviceName = trim($_POST['service_name']);
            $slug = trim($_POST['slug']);
            $price = trim($_POST['service_price']);
            $duration = trim($_POST['service_duration']);
            $description = trim($_POST['service_description']);
            $iconFile = $_FILES['service_icon'] ?? null;

            // Handle image (keep old one if no new upload)
            $imagePath = $_SESSION['image']; // old image stored from editservice.php
            if (!empty($iconFile['name'])) {
                $uploadDir = "../../view/assets/images/services/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = time() . "_" . basename($iconFile['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($iconFile['tmp_name'], $targetPath)) {
                    $imagePath = "/services/". $fileName;
                }
            }

            // ✅ Update DB
            $model->update("services", [
                "services_categoryID" => $categoryId,
                "name" => $serviceName,
                "slug" => $slug,
                "base_price" => $price,
                "duration" => $duration,
                "description" => $description,
                "image" => $imagePath
            ], ["id" => $serviceId]);

            $utility->setFlash("success", "Service updated successfully.");
            header("Location: ../../console/servicemgr.php");
            exit;
        } elseif ($action === 'this_form_delete_this_category') {
            $id = intval($_POST['category_id'] ?? 0);

            if ($id > 0) {
                // Check if category has been deactivated first
                $categoryStatus = $model->getRows("categories", ["categoryTbl_id" => $id]);

                // Check if category has products
                $productCount = $model->getRows("products", [
                    "where" => ["category_id" => $id],
                    "return_type" => "count"
                ]);

                if ($productCount > 0 && $categoryStatus["category_status"] !== 'Inactive') {
                    $utility->setFlash("warning", "Cannot delete category. It still has $productCount product(s).");
                } else {
                    $deleted = $model->update(
                        "categories",
                        [
                            "category_status" => "Deleted"
                        ],
                        ["categoryTbl_id" => $id]
                    );

                    if ($deleted) {
                        $utility->setFlash("success", "Category deleted successfully.");
                    } else {
                        $utility->setFlash("danger", "Error deleting category.");
                    }
                }
            }

            header("Location: ../../console/category_mgr.php");
            exit;
        } elseif ($action === 'this_form_toggle_category_status') {
            $id = intval($_POST['category_id'] ?? 0);

            if ($id > 0) {
                // Get current status
                $category = $model->getById("categories", ["categoryTbl_id" => $id]);

                if ($category) {
                    $newStatus = ($category['category_status'] === 'Active') ? 'Inactive' : 'Active';

                    $updated = $model->update(
                        "categories",
                        ["category_status" => $newStatus],
                        ["categoryTbl_id" => $id]
                    );

                    if ($updated) {
                        $utility->setFlash("success", "Category status updated to $newStatus successfully.");
                    } else {
                        $utility->setFlash("danger", "Failed to update category status.");
                    }
                } else {
                    $utility->setFlash("danger", "Category not found.");
                }
            } else {
                $utility->setFlash("danger", "Invalid category ID.");
            }

            header("Location: ../../console/category_mgr.php");
            exit;
        }
    } catch (Exception $e) {
        $utility->setFlash("danger", "Error: " . $e->getMessage());
    }

    header("Location: ../../console/category_mgr.php");
    exit;
}
