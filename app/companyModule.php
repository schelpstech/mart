<?php
include './query.php';

// Validate the session and module
if (!isset($_SESSION['module']) || $_SESSION['module'] !== 'Dashboard') {
    $utility->redirectWithNotification('dark', 'Sorry, we cannot understand your request.', 'consultantDashboard');
    exit;
}

// Check if the form submission is valid
if (isset($_POST['edit_company_details']) && $utility->inputDecode($_POST['edit_company_details']) === "company_profile_editor_form") {
    // Ensure the user is authenticated
    if (!isset($_SESSION['activeID']) || empty($_SESSION['activeID'])) {
        $utility->redirectWithNotification('danger', 'Unauthorized access. Please log in.', 'login');
        exit;
    }

    $tblName = 'tbl_consultantdetails';

    // Check if the company record exists
    $conditions = [
        'where' => ['userId' => $_SESSION['activeID']],
        'return_type' => 'count',
    ];
    $ifExist = $model->getRows($tblName, $conditions);

    if ($ifExist === 0) {
        $utility->redirectWithNotification('danger', 'No company record exists within this request.', 'consultantDashboard');
        exit;
    }

    // Sanitize and validate input data
    $companyData = [
        'companyName' =>  preg_replace('/[^a-zA-Z0-9\s&,\.\-\'()]/', '', $_POST['companyName']),
        'companyAddress' => preg_replace('/[^a-zA-Z0-9\s&,\.\-\'()]/', '', $_POST['companyAddress']),
        'contactPhone' => filter_var($_POST['contactPhone'], FILTER_SANITIZE_NUMBER_INT),
        'contactEmail' => filter_var($_POST['contactEmail'], FILTER_VALIDATE_EMAIL),
    ];

    // Validate email format
    if (!$companyData['contactEmail']) {
        $utility->redirectWithNotification('danger', 'Invalid email address provided.', 'consultantDashboard');
        exit;
    }

    // Validate phone number length (11 digits)
    if (!preg_match('/^\d{11}$/', $companyData['contactPhone'])) {
        $utility->redirectWithNotification('danger', 'Phone number must be exactly 11 digits.', 'consultantDashboard');
        exit;
    }

    // Update the company profile
    $condition = ['userId' => $_SESSION['activeID']];

    try {
        if (!$model->upDate($tblName, $companyData, $condition)) {
            throw new Exception('Failed to update the company profile.');
        }

        // Record the log
        $user->recordLog(
            $_SESSION['active'],
            'Consultant Details Edited',
            sprintf('User ID: %d edited consultant details for company ID: %d.', $_SESSION['active'], $_SESSION['activeID'])
        );

        // Redirect with success notification
        $utility->redirectWithNotification('success', 'Consultant company details have been updated.', 'consultantDashboard');
    } catch (Exception $e) {
        // Log the error for debugging purposes
        error_log($e->getMessage());

        // Redirect with error notification
        $utility->redirectWithNotification('danger', 'An error occurred while updating the consultant details.', 'consultantDashboard');
    }
} elseif (isset($_POST['new_user_password_credential']) && $utility->inputDecode($_POST['new_user_password_credential']) === "user_password_editor_form") {
    // Ensure the user is authenticated
    if (!isset($_SESSION['activeID']) || empty($_SESSION['activeID'])) {
        $utility->redirectWithNotification('danger', 'Unauthorized access. Please log in.', 'login');
        exit;
    }
    $tblName = 'book_of_life';
    $logTable = 'log';

    $oldPassword = !empty($_POST["oldPassword"]) ? htmlspecialchars($_POST["oldPassword"]) : null;
    $newPassword = !empty($_POST["newPassword"]) ? htmlspecialchars($_POST["newPassword"]) : null;
    $confirmPassword = !empty($_POST["confirmPassword"]) ? htmlspecialchars($_POST["confirmPassword"]) : null;

    // Validate passwords
    if ($newPassword !== $confirmPassword) {
        $utility->redirectWithNotification('danger', 'Passwords do not match.', 'consultantpwdMgr');
        exit;
    }

    if (!$utility->isPasswordStrong($newPassword)) {
        $utility->redirectWithNotification('danger', 'Password must meet strength requirements.', 'consultantpwdMgr');
        exit;
    }

    // Check if the user exists
    $conditions = [
        'return_type' => 'count',
        'where' => ['user_name' => htmlspecialchars($_SESSION['active'])]
    ];
    $userExists = $model->getRows($tblName, $conditions);

    if ($userExists === 1) {
        // Fetch user details
        $conditions = [
            'return_type' => 'single',
            'where' => ['user_name' => htmlspecialchars($_SESSION['active'])]
        ];
        $loginDetails = $model->getRows($tblName, $conditions);
        $passwordHash = $loginDetails['user_password'] ?? '';

        // Verify old password
        $oldPasswordEncoded = $utility->inputEncode($oldPassword);
        if ($utility->verifyPassword($oldPasswordEncoded, $passwordHash)) {
            $newpwd = $utility->encodePassword($newPassword);
            // Update password
            $condition = ['user_name' => $_SESSION['active']];
            $loginData = [
                'user_password' => $newpwd,
                'activeStatus' => 1,
            ];

            try {
                if (!$model->upDate($tblName, $loginData, $condition)) {
                    throw new Exception('Failed to update login credential.');
                }

                // Regenerate session ID for security
                session_regenerate_id(true);

                // Record the log
                $user->recordLog(
                    $_SESSION['active'],
                    'Consultant Password Modified',
                    $_SERVER['REMOTE_ADDR'] . ' modified login password'
                );

                // Redirect with success notification
                $utility->redirectWithNotification('success', 'Login Credential has been updated.', 'consultantpwdMgr');
            } catch (Exception $e) {
                error_log('Error updating password for user ' . $_SESSION['active'] . ': ' . $e->getMessage());
                $utility->redirectWithNotification('danger', 'An error occurred while updating your login credential.', 'consultantpwdMgr');
            }
        } else {
            $utility->redirectWithNotification('danger', 'Current Password entered is incorrect.', 'consultantpwdMgr');
        }
    } else {
        // Log out the user and redirect
        $model->log_out_user();
        $utility->setNotification('alert-success', 'icon fas fa-check', 'Logged out successfully.');
        $utility->redirect('../view/index.php');
    }
}
