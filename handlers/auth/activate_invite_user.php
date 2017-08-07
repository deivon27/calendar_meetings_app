<?php
/**
 * Handler that activate account of invited user
 **/
$app = include("../../config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "includes/functions.php";

$email = $_GET['email'];
$token = $_GET['token'];

$stm = $db->prepare("SELECT `email`, `token`  
                      FROM `invites` 
                      WHERE `email` = ? AND `token` = ?");
$stm->bindParam(1, $email, PDO::PARAM_STR);
$stm->bindParam(2, $token, PDO::PARAM_STR);
if ($stm->execute()) {
    $rowCount = $stm->rowCount();
    if ($rowCount == 0) {
        die('0');
    }
    $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

    $nameValues = [
        'email' => $email,
        'token' => $token
    ];

    $addNewUser = insertDb($db, 'users', array('email' => $email));
    
    header("Location: ../../views/auth/finish_register.php?token=" . $token);
    exit();
} else {
    echo 0;
}

$db = null;