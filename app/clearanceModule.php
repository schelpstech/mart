<?php
include './query.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recordCandidates']) && $utility->inputDecode($_POST['recordCandidates']) === 'record_candidates') {
    // Sanitize and validate input
    $recordSchoolCode = htmlspecialchars($_POST['schoolName'], ENT_QUOTES, 'UTF-8');
    $numberCaptured = filter_var($_POST['numCandidatesCaptured'], FILTER_VALIDATE_INT);

    // Check if inputs are valid
    if (empty($recordSchoolCode)) {
        $utility->redirectWithNotification('danger', 'Please select a valid school.', 'capturingRecord');
        exit;
    }

    if ($numberCaptured === false || $numberCaptured <= 0) {
        $utility->redirectWithNotification('danger', 'Please enter a valid number of candidates captured.', 'capturingRecord');
        exit;
    }
    // Database table names
    $tblRemittance = 'tbl_remittance';
    // Check if the record for the same school and exam year already exists
    $conditions = [
        'where' => [
            'recordSchoolCode' => $recordSchoolCode,
            'examYearRef' => $examYear['id']
        ],

        'return_type' => 'count'
    ];

    $recordExists = $model->getRows($tblRemittance, $conditions);

    if ($recordExists >= 1) {
        $utility->redirectWithNotification('warning', 'Record already exists for this school and exam year. Please update the record instead.', 'capturingRecord');
    }
    // Generate a unique reference
    $uniqueReference = uniqid('CAP', true);

    // Query to fetch school type based on school code
    $tblName = 'tbl_schoollist';
    $conditions = [
        'where' => [
            'centreNumber' => $recordSchoolCode,
        ],
        'return_type' => 'single',
    ];
    $selectedSchoolType = $model->getRows($tblName, $conditions);
    if (!empty($selectedSchoolType)) {
        $ratePerCandidate = ($selectedSchoolType['schType'] == 1) ? 280 : (($selectedSchoolType['schType'] == 2) ? 130 : '');
        // Calculate the remittance amount
        $amountDue = $numberCaptured * $ratePerCandidate;
    } else {
        $utility->redirectWithNotification('danger', 'School Type not Specified.', 'capturingRecord');
    }
    // Database table names
    $tblRemittance = 'tbl_remittance';

    $remittanceData = [
        'Rem_uniquereference' => $uniqueReference,
        'recordSchoolCode' => $recordSchoolCode,
        'numberCaptured' => $utility->inputEncode($numberCaptured),
        'amountdue' => $utility->inputEncode($amountDue),
        'examYearRef' => $examYear['id'],
        'submittedby' => $_SESSION['activeID']
    ];

    try {

        // Insert into tbl_remittance
        $insertRemittance = $model->insert_data($tblRemittance, $remittanceData);
        if (!$insertRemittance) {
            throw new Exception('Failed to record remittance.');
        }

        // Record the log
        $user->recordLog(
            $_SESSION['active'],
            'Captured Record Created',
            sprintf('User ID: %d recorded number of captured candidate for Centre Number : %d.', $_SESSION['active'], $recordSchoolCode)
        );

        // Redirect with success notification
        $utility->redirectWithNotification('success', 'Record successfully added!', 'capturingRecord');
    } catch (Exception $e) {
        // Log the error
        error_log($e->getMessage());

        // Redirect with error notification
        $utility->redirectWithNotification('danger', 'An error occurred. Please try again.', 'capturingRecord');
    }
}



//Modify

elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCandidates']) && $utility->inputDecode($_POST['updateCandidates']) === 'update_candidates') {
    // Sanitize and validate input
    $recordSchoolCode = $utility->inputDecode($_SESSION['reference']);
    $numberCaptured = filter_var($_POST['numCandidatesCaptured'], FILTER_VALIDATE_INT);

    // Check if inputs are valid
    if (empty($recordSchoolCode)) {
        $utility->redirectWithNotification('danger', 'Please select a valid school.', 'capturingRecord');
        exit;
    }

    if ($numberCaptured === false || $numberCaptured <= 0) {
        $utility->redirectWithNotification('danger', 'Please enter a valid number of candidates captured.', 'capturingRecord');
        exit;
    }
    // Database table name
    $tblRemittance = 'tbl_remittance';
    // Check if the record for the same school and exam year already exists
    $conditions = [
        'where' => [
            'recordSchoolCode' => $recordSchoolCode,
            'examYearRef' => $examYear['id']
        ],

        'return_type' => 'count'
    ];

    $recordExists = $model->getRows($tblRemittance, $conditions);

    if ($recordExists != 1) {
        $utility->redirectWithNotification('warning', 'No Record exists for this school in the exam year. Please submit the record instead.', 'capturingRecord');
    }

    // Calculate the remittance amount
    // Query to fetch school type based on school code
    $tblName = 'tbl_schoollist';
    $conditions = [
        'where' => [
            'centreNumber' => $recordSchoolCode,
        ],
        'return_type' => 'single',
    ];
    $selectedSchoolType = $model->getRows($tblName, $conditions);
    if (!empty($selectedSchoolType)) {
        $ratePerCandidate = ($selectedSchoolType['schType'] == 1) ? 280 : (($selectedSchoolType['schType'] == 2) ? 130 : '');
        // Calculate the remittance amount
        $amountDue = $numberCaptured * $ratePerCandidate;
    } else {
        $utility->redirectWithNotification('danger', 'School Type not Specified.', 'capturingRecord');
    }
    // Data to be inserted
    $remittanceData = [
        'amountdue' => $utility->inputEncode($amountDue),
        'numberCaptured' => $utility->inputEncode($numberCaptured),
        'submittedby' => $_SESSION['activeID']
    ];
    // Update the company profile
    $condition = [
        'recordSchoolCode' => $recordSchoolCode,
        'examYearRef' => $examYear['id']
    ];

    try {
        // Update tbl_remittance
        $updateRemittance = $model->upDate($tblRemittance, $remittanceData, $condition);
        if (!$updateRemittance) {
            throw new Exception('Failed to update remittance.');
        }

        // Record the log
        $user->recordLog(
            $_SESSION['active'],
            'Captured Record Edited',
            sprintf('User ID: %d edited number of captured candidate for Centre Number : %d.', $_SESSION['active'], $recordSchoolCode)
        );

        // Redirect with success notification
        $utility->redirectWithNotification('success', 'Number of captured candidates in Centre : ' . $recordSchoolCode . '  have been updated. to ' . $numberCaptured, 'capturingRecord');
    } catch (Exception $e) {
        // Log the error for debugging purposes
        error_log($e->getMessage());

        // Redirect with error notification
        $utility->redirectWithNotification('danger', 'An error occurred while updating the number of captured candidates.', 'capturingRecord');
    }
} if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_submitted_clearance']) && $utility->inputDecode($_POST['verify_submitted_clearance']) === 'checkClearanceValidity') {
    $clearanceCode = isset($_POST['clearanceCode']) ? htmlspecialchars($_POST['clearanceCode'], ENT_QUOTES, 'UTF-8') : '';

    if (empty($clearanceCode)) {
        $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Invalid! Clearance ID cannot be empty.');
        $utility->redirect('../view/verifyClearance.php');
        exit;
    }

    $tblName = 'tbl_remittance';
    $conditions = ['where' => ['Rem_uniquereference' => $clearanceCode], 'return_type' => 'count'];
    $referedSchoolCount = $model->getRows($tblName, $conditions);

    if ($referedSchoolCount === 1) {
        $conditions = ['where' => ['Rem_uniquereference' => $clearanceCode], 'return_type' => 'single'];
        $referedSchool = $model->getRows($tblName, $conditions);

        if (!empty($referedSchool) && isset($referedSchool['clearanceStatus']) && $referedSchool['clearanceStatus'] == 200) {
            $_SESSION['referencedSchoolForVerification'] = $referedSchool['recordSchoolCode'];
            $utility->redirect('../view/verificationpage.php');
        } else {
            $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Invalid! Payment pending on this Clearance ID.');
            $utility->redirect('../view/verifyClearance.php');
        }
    } else {
        $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Invalid! Clearance ID does not exist in our record.');
        $utility->redirect('../view/verifyClearance.php');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['verify_submitted_clearance_ID'])) {
    $clearanceCode = isset($_GET['verify_submitted_clearance_ID']) ? $utility->inputDecode($_GET['verify_submitted_clearance_ID']) : '';

    if (empty($clearanceCode)) {
        $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Invalid! Clearance ID cannot be empty.');
        $utility->redirect('../view/verifyClearance.php');
        exit;
    }

    $tblName = 'tbl_remittance';
    $conditions = ['where' => ['Rem_uniquereference' => $clearanceCode], 'return_type' => 'count'];
    $referedSchoolCount = $model->getRows($tblName, $conditions);

    if ($referedSchoolCount === 1) {
        $conditions = ['where' => ['Rem_uniquereference' => $clearanceCode], 'return_type' => 'single'];
        $referedSchool = $model->getRows($tblName, $conditions);

        if (!empty($referedSchool) && isset($referedSchool['clearanceStatus']) && $referedSchool['clearanceStatus'] == 200) {
            $_SESSION['referencedSchoolForVerification'] = $referedSchool['recordSchoolCode'];
            $utility->redirect('../view/verificationpage.php');
        } else {
            $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Invalid! Payment pending on this Clearance ID.');
            $utility->redirect('../view/verifyClearance.php');
        }
    } else {
        $utility->setNotification('alert-danger', 'icon fas fa-ban', 'Invalid! Clearance ID does not exist in our record.');
        $utility->redirect('../view/verifyClearance.php');
    }
}
 else {
    $utility->redirectWithNotification('danger', 'Unknown Request.', 'capturingRecord');
}
