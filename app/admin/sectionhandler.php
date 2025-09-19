<?php
include "../query.php"; // load session + db + model

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = $utility->inputDecode($_POST['action']);
    $section_id  = intval($_POST['section_id'] ?? 0);
    $name        = trim($_POST['section_name'] ?? '');
    $slug        = trim($_POST['slug'] ?? '');
    $description = trim($_POST['fulldescription'] ?? '');

    // Basic validation


    try {
        if ($action === 'this_form_adds_a_new_section') {

            if (empty($name)) {
                $utility->setFlash("danger", "Section name is required.");
                header("Location: ../../console/section_mgr.php");
                exit;
            }

            // Auto-generate slug if empty
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
            }
            // Check uniqueness
            $exists = $model->exists("sections", [
                "section_name" => $name,
                "section_slug" => $slug
            ]);

            if ($exists) {
                $utility->setFlash("warning", "Section Name or Slug already exists.");
                header("Location: ../../console/section_mgr.php");
                exit;
            }

            // Insert
            $insert = $model->insert("sections", [
                "section_name"   => $name,
                "section_slug"   => $slug,
                "description"    => $description,
                "section_status" => "Active"
            ]);

            if ($insert) {
                $utility->setFlash("success", "Section added successfully!");
            } else {
                $utility->setFlash("danger", "Error adding section. Try again.");
            }
        } elseif ($action === 'this_form_edits_a_section' && $section_id > 0) {
            if (empty($name)) {
                $utility->setFlash("danger", "Section name is required.");
                header("Location: ../../console/section_mgr.php");
                exit;
            }

            // Auto-generate slug if empty
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
            }
            // Update
            $update = $model->update("sections", [
                "section_name" => $name,
                "section_slug" => $slug,
                "description"  => $description
            ], ["id" => $section_id]);

            if ($update) {
                $utility->setFlash("success", "Section updated successfully!");
            } else {
                $utility->setFlash("warning", "No changes made or update failed.");
            }
        } elseif ($action === 'this_form_delete_this_section') {
            $id = intval($_POST['section_id'] ?? 0);

            if ($id > 0) {
                // Check if section has been deactivated first
                $sectionStatus = $model->getById("sections", $id);

                // Check if section has categories
                $catCount = $model->getRows("categories", [["section_id" => $id], "return_type" => "count"]);

                if ($catCount > 0 && $sectionStatus["section_status"] !== 'Inactive') {
                    $utility->setFlash("warning", "Cannot delete section. It still has $catCount category(s).");
                } else {
                    $deleted = $model->update(
                        "sections",
                        [
                            "section_status"  => "Deleted"
                        ],
                        ["id" => $id]
                    );
                    if ($deleted) {
                        $utility->setFlash("success", "Section deleted successfully.");
                    } else {
                        $utility->setFlash("danger", "Error deleting section.");
                    }
                }
            }
            header("Location: ../../console/section_mgr.php");
            exit;
        } elseif ($action === 'this_form_toggle_section_status') {
            $id = intval($_POST['section_id'] ?? 0);

            if ($id > 0) {
                // Get current status
                $section = $model->getById("sections", $id);

                if ($section) {
                    $newStatus = ($section['section_status'] === 'Active') ? 'Inactive' : 'Active';

                    $updated = $model->update(
                        "sections",
                        ["section_status" => $newStatus],
                        ["id" => $id]
                    );

                    if ($updated) {
                        $utility->setFlash("success", "Section status updated to $newStatus successfully.");
                    } else {
                        $utility->setFlash("danger", "Failed to update section status.");
                    }
                } else {
                    $utility->setFlash("danger", "Section not found.");
                }
            } else {
                $utility->setFlash("danger", "Invalid section ID.");
            }

            header("Location: ../../console/section_mgr.php");
            exit;
        } else {
            $utility->setFlash("danger", "Invalid action or Section ID.");
        }
    } catch (Exception $e) {
        $utility->setFlash("danger", "Error: " . $e->getMessage());
    }

    header("Location: ../../console/section_mgr.php");
    exit;
}
