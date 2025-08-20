<?php
header('Content-Type: application/json');
include 'query.php';

$response = ['success' => false, 'message' => 'Invalid request'];


try {
    // Get the input data
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['schoolCode']) && !empty($input['schoolCode'])) {
        $schoolCode = htmlspecialchars($input['schoolCode'], ENT_QUOTES);
        // Query to fetch school type based on school code
        $tblName = 'tbl_schoollist';
        $conditions = [
            'where' => [
                'centreNumber' => $input['schoolCode'],
            ],
            'return_type' => 'single',
        ];
        $selectedSchoolType = $model->getRows($tblName, $conditions);
        if (!empty($selectedSchoolType)) {
            $schtypeValue = ($selectedSchoolType['schType'] == 1) ? 'Public' : (($selectedSchoolType['schType'] == 2) ? 'Private' : '');

            $response = ['success' => true, 'schoolType' =>  $schtypeValue];
        } else {
            $response['message'] = "School type not found for the selected school.";
        }
    } else {
        $response['message'] = "School code is required.";
    }
} catch (Exception $e) {
    $response['message'] = "An error occurred: " . $e->getMessage();
}

// Return JSON response
echo json_encode($response);
