<?php
include "../query.php"; // load session + db + model

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $utility->inputDecode($_POST['action'] ?? '');

    // ✅ ADD NEW CATEGORY
    try {
        if ($action === 'this_form_adds_a_new_category') {
            $section_id   = intval($_POST['section_id'] ?? 0);
            $name         = trim($_POST['category_name'] ?? '');
            $slug         = trim($_POST['slug'] ?? '');
            $description  = trim($_POST['fulldescription'] ?? '');
            $iconFile     = $_FILES['category_icon'] ?? null;

            // Basic validation
            if ($section_id <= 0 || empty($name)) {
                $utility->setFlash("danger", "Section and Category Name are required.");
                header("Location: ../../console/category_mgr.php");
                exit;
            }

            // If slug empty, auto-generate from name
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
            }

            // ✅ Check for uniqueness of category slug within section
            $exists = $model->exists("categories", [
                "category_slug" => $slug,
                "section_id"    => $section_id
            ]);
            if ($exists) {
                $utility->setFlash("warning", "Category already exists in this section.");
                header("Location: ../../console/category_mgr.php");
                exit;
            }

            $iconPath = "/category_icons/default.png"; // fallback
            if (!empty($iconFile['name'])) {
                $uploadDir = "../../view/assets/images/category_icons/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . "_" . basename($iconFile['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($iconFile['tmp_name'], $targetPath)) {
                    $iconPath = "/category_icons/" . $fileName;
                } else {
                    $utility->setFlash("danger", "Error uploading category icon.");
                    header("Location: ../../console/category_mgr.php");
                    exit;
                }
            }


            // ✅ Insert category into DB
            $insert = $model->insert("categories", [
                "section_id"      => $section_id,
                "category_name"   => $name,
                "category_slug"   => $slug,
                "icon"   => $iconPath,
                "description"     => $description,
                "category_status" => "Active"
            ]);

            if ($insert) {
                $utility->setFlash("success", "Category added successfully!");
            } else {
                $utility->setFlash("danger", "Error adding category.");
            }
            header("Location: ../../console/category_mgr.php");
            exit;
        } elseif ($action === "this_form_updates_a_category") {
            $categoryId = $_POST['category_id'];
            $sectionId = $_POST['section_id'];
            $categoryName = trim($_POST['category_name']);
            $slug = trim($_POST['slug']);
            $description = trim($_POST['fulldescription']);
            $iconFile = $_FILES['category_icon'] ?? null;

            // Handle icon (only update if a new one is uploaded)
            $iconPath = $_SESSION['category_icon']; // keep old
            if (!empty($iconFile['name'])) {
                $uploadDir = "../../view/assets/images/category_icons/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . "_" . basename($iconFile['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($iconFile['tmp_name'], $targetPath)) {
                    $iconPath = "/category_icons/" . $fileName;
                }
            }
            // ✅ Update DB
            $model->update("categories", [
                "section_id" => $sectionId,
                "category_name" => $categoryName,
                "category_slug" => $slug,
                "description" => $description,
                "icon" => $iconPath
            ], ["categoryTbl_id" => $categoryId]);

            $utility->setFlash("success", "Category updated successfully.");
            header("Location: ../../console/category_mgr.php");
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
