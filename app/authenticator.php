<?php

include 'query.php';

$tblName = 'book_of_life';
$logTable = 'log';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Login logic
    if (isset($_POST['login_button']) && $_POST['login_button'] === 'do_login') {
        $userid = !empty($_POST["username"]) ? htmlspecialchars($_POST["username"]) : null;
        $userpwd = !empty($_POST["password"]) ? htmlspecialchars($_POST["password"]) : null;

        if (!$userid || !$userpwd) {
            $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Username and Password must not be empty!');
            $utility->redirect('../view/index.php');
            exit;
        }

        // Check if username exists
        $conditions = [
            'return_type' => 'count',
            'where' => ['user_name' => $userid]
        ];
        $userExists = $model->getRows($tblName, $conditions);

        if ($userExists === 1) {
            // Fetch user details
            $conditions = [
                'return_type' => 'single',
                'where' => ['user_name' => $userid]
            ];
            $loginDetails = $model->getRows($tblName, $conditions);

            $passwordHash = $loginDetails['user_password'] ?? '';
            $accessStatus = $loginDetails['access_status'] ?? 0;

            //encode user inputed passwor
            $userpwd = $utility->inputEncode($userpwd);

            // Verify password
            if ($utility->verifyPassword($userpwd, $passwordHash)) {
                if ($accessStatus == 1) {
                    // Successful login
                    $_SESSION['active'] = $userid;
                    $user->recordLog($userid, 'Login Attempt', 'Successful - Access Granted');
                    $utility->notifier('success', 'You have been successfully logged in');

                    $isActive = isset($loginDetails['activeStatus']) && $loginDetails['activeStatus'] == 1;
                    if (!$isActive) {
                        $utility->redirect('./router.php?pageid=' . $utility->inputEncode('consultantpwdMgr'));
                    } else {
                        $utility->redirect('./router.php?pageid=' . $utility->inputEncode('consultantDashboard'));
                    }
                } else {
                    // Access denied
                    $logData = [
                        'user_name' => $userid,
                        'activity' => 'Login Attempt',
                        'uip' => $_SERVER['REMOTE_ADDR'],
                        'description' => 'Unsuccessful - Access Denied'
                    ];
                    $model->insert_data($logTable, $logData);
                    $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Access Denied! Contact administrator.');
                    $utility->redirect('../view/index.php');
                }
            } else {
                // Incorrect password
                $logData = [
                    'user_name' => $userid,
                    'activity' => 'Login Attempt',
                    'uip' => $_SERVER['REMOTE_ADDR'],
                    'description' => 'Unsuccessful - Wrong Password'
                ];
                $model->insert_data($logTable, $logData);
                $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Invalid Login Credentials!');
                $utility->redirect('../view/index.php');
            }
        } else {
            // Invalid username
            $logData = [
                'user_name' => $userid,
                'activity' => 'Login Attempt',
                'uip' => $_SERVER['REMOTE_ADDR'],
                'description' => 'Unsuccessful - Invalid Username'
            ];
            $model->insert_data($logTable, $logData);
            $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Invalid Login Credentials!');
            $utility->redirect('../view/index.php');
        }
    }

    // Logout logic
    elseif (isset($_POST['log_out_user']) && base64_decode($_POST['log_out_user']) === 'log_out_user_form') {
        $model->log_out_user();
        session_start();
        $utility->setNotification('alert-success', 'icon fas fa-check', 'Logged out successfully.');
        $utility->redirect('../view/index.php');
    } else {
        // Invalid request
        $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Access Denied! You are attempting login from an unsecured page.');
        $utility->redirect('../view/index.php');
    }
}
