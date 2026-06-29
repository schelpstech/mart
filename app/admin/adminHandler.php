<?php
include "../query.php";
include_once "../../controller/admin_helpers.php";

function admin_redirect()
{
    header("Location: ../../console/admins.php");
    exit;
}

function admin_post($key, $default = '')
{
    return trim((string)($_POST[$key] ?? $default));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $utility->setFlash("warning", "Restricted action.");
    admin_redirect();
}

if (!qs_admin_table_exists($model, 'admins')) {
    $utility->setFlash("danger", "Admins table is not installed.");
    admin_redirect();
}

$columns = qs_admin_table_columns($model, 'admins');
$hasStatus = in_array('admin_status', $columns, true);
$hasCreatedAt = in_array('created_at', $columns, true);
$hasUpdatedAt = in_array('updated_at', $columns, true);
$action = $utility->inputDecode($_POST['action'] ?? '');

try {
    if ($action === 'add_admin') {
        $name = admin_post('admin_name');
        $email = filter_var(admin_post('admin_email'), FILTER_VALIDATE_EMAIL);
        $role = strtolower(admin_post('role', 'admin'));
        $password = (string)($_POST['password'] ?? '');

        if ($name === '' || !$email || strlen($password) < 8) {
            $utility->setFlash("danger", "Name, valid email, and password of at least 8 characters are required.");
            admin_redirect();
        }

        $role = in_array($role, ['admin', 'manager', 'staff'], true) ? $role : 'admin';
        if ($model->exists('admins', ['admin_email' => $email])) {
            $utility->setFlash("warning", "An admin with this email already exists.");
            admin_redirect();
        }

        $fields = [
            'admin_name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'admin_email' => $email,
            'admin_password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role
        ];
        if ($hasStatus) $fields['admin_status'] = 'active';
        if ($hasCreatedAt) $fields['created_at'] = date("Y-m-d H:i:s");
        if ($hasUpdatedAt) $fields['updated_at'] = date("Y-m-d H:i:s");

        $model->insert('admins', $fields);
        $utility->setFlash("success", "Admin created successfully.");
        admin_redirect();
    }

    if ($action === 'update_admin') {
        $adminId = (int)($_POST['admin_id'] ?? 0);
        $name = admin_post('admin_name');
        $email = filter_var(admin_post('admin_email'), FILTER_VALIDATE_EMAIL);
        $role = strtolower(admin_post('role', 'admin'));
        $password = (string)($_POST['password'] ?? '');

        if ($adminId <= 0 || $name === '' || !$email) {
            $utility->setFlash("danger", "Invalid admin update.");
            admin_redirect();
        }

        $existing = $model->getRows('admins', [
            'where' => ['admin_email' => $email],
            'return_type' => 'single'
        ]);
        if ($existing && (int)$existing['admin_id'] !== $adminId) {
            $utility->setFlash("warning", "Another admin already uses this email.");
            admin_redirect();
        }

        $role = in_array($role, ['admin', 'manager', 'staff'], true) ? $role : 'admin';
        $fields = [
            'admin_name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'admin_email' => $email,
            'role' => $role
        ];

        if ($password !== '') {
            if (strlen($password) < 8) {
                $utility->setFlash("danger", "New password must be at least 8 characters.");
                admin_redirect();
            }
            $fields['admin_password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($hasStatus && (int)($_SESSION['admin_id'] ?? 0) !== $adminId) {
            $status = strtolower(admin_post('admin_status', 'active'));
            $fields['admin_status'] = in_array($status, ['active', 'inactive'], true) ? $status : 'active';
        }
        if ($hasUpdatedAt) $fields['updated_at'] = date("Y-m-d H:i:s");

        $model->update('admins', $fields, ['admin_id' => $adminId]);
        $utility->setFlash("success", "Admin updated successfully.");
        admin_redirect();
    }

    if ($action === 'deactivate_admin') {
        $adminId = (int)($_POST['admin_id'] ?? 0);
        if ($adminId <= 0 || (int)($_SESSION['admin_id'] ?? 0) === $adminId) {
            $utility->setFlash("danger", "You cannot deactivate this admin account.");
            admin_redirect();
        }

        $activeCount = $hasStatus
            ? $model->getRows('admins', ['where' => ['admin_status' => 'active'], 'return_type' => 'count'])
            : $model->getRows('admins', ['return_type' => 'count']);

        if ((int)$activeCount <= 1) {
            $utility->setFlash("warning", "At least one active admin must remain.");
            admin_redirect();
        }

        if ($hasStatus) {
            $fields = ['admin_status' => 'inactive'];
            if ($hasUpdatedAt) $fields['updated_at'] = date("Y-m-d H:i:s");
            $model->update('admins', $fields, ['admin_id' => $adminId]);
        } else {
            $model->delete('admins', ['admin_id' => $adminId]);
        }

        $utility->setFlash("success", "Admin account deactivated.");
        admin_redirect();
    }

    $utility->setFlash("warning", "Unknown admin action.");
} catch (Exception $e) {
    $utility->setFlash("danger", "Admin action failed: " . $e->getMessage());
}

admin_redirect();
