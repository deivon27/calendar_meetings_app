<?php
$app = include("../../config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "includes/functions.php";

$token = $_POST['token'];
$password = generate_hash($_POST['password'], 11);

if ($_POST['password'] != $_POST['repeatPassword']) {
    die('2');
}

$selectEmail = selectDb($db, 'invites', ['email'], ['token' => $token]);

$email = $selectEmail->fetchColumn(0);

$registerNewUser = updateDb($db, 'users', ['password' => $password], ['email' => $email]);
$removeInvite = deleteDb($db, 'invites', ['email' => $email, 'token' => $token]);

echo $registerNewUser ? 1 : 0;

$db = null;
