<?php
/**
 * Handler that saves event in DB
 **/
$app = include("../config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "includes/functions.php";

if ($_SESSION['token'] == $_POST['token']) {
    $removeEvent = deleteDb($db, 'events', [ 'id' => $_POST['id'] ]);
    echo $removeEvent ? 1 : 0;
} else {
    echo 0;
}
$db = null;