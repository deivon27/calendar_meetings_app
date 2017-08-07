<?php
/**
 * Handler that returns a list of events
 **/
$app = include("../config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "includes/functions.php";

$whereValues = null;
if (isset($_POST['typeSelect']) && isset($_POST['id'])) {
    $whereValues = array('id' => $_POST['id']);
}

$selEvents = selectDb(
    $db,
    'events',
    ['id', 'name', 'startdate', 'enddate', 'starttime', 'endtime', 'description', 'status', 'id_user', 'color', 'url'],
    $whereValues
);
$events = $selEvents->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($events); $i++) {
    $events[$i]['loggedUserId'] = $_SESSION['idUser'];
    $events[$i]['role'] = $_SESSION['role'];
}
$data = json_encode(array('monthly' => $events));
echo $data ? $data : 0;

$db = null;