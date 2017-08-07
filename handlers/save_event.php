<?php
/**
 * Handler that saves event in DB
 **/
$app = include("../config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "includes/functions.php";

if ($_SESSION['token'] == $_POST['token']) {

    $arrayKey = array();
    $arrayVal = array();
    $mainData = array();
    $restData = array();

    foreach ($_POST as $key => $value) {
        // Skip some unuseful values
        if ($key == 'formAction' || $key == 'token') {
            continue;
        } else {
            // Explode date and time
            if ($key == 'startdate' || $key == 'enddate') {
                if ($key == 'startdate') {
                    $exStartDate = explode(' ', $value);
                    $arrayKey[] = "startdate";
                    $arrayVal[] = $exStartDate[0];

                    $arrayKey[] = "starttime";
                    $arrayVal[] = $exStartDate[1] . ":00";
                }
                if ($key == 'enddate') {
                    $exEndDate = explode(' ', $value);
                    $arrayKey[] = "enddate";
                    $arrayVal[] = $exEndDate[0];

                    $arrayKey[] = "endtime";
                    $arrayVal[] = $exEndDate[1] . ":00";
                }
            } else {
                $restData[$key] = $value;
            }
        }
    }
    $mainData = array_merge($restData, array_combine($arrayKey, $arrayVal));

    if (count($mainData) > 0) {
        $action = intval($_POST['formAction']);

        if ($action == 1) {

            /* Insert event */
            $exec = insertDb($db, 'events', $mainData);
            echo $exec ? 1 : 0;
        } else {

            /* Update event */
            $exec = updateDb($db, 'events', $mainData, [ 'id' => $_POST['id'] ]);
            $selInsertedEvent = selectDb($db, 'events', '*', [ 'id' => $_POST['id'] ]);
            echo json_encode($selInsertedEvent->fetchAll(PDO::FETCH_ASSOC));
        }
    }
} else {
    echo 3;
}
$db = null;