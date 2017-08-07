<?php
$app = include("../../config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "includes/functions.php";

$email = $_POST['email'];
$password = generate_hash($_POST['password'], 11);

$registerAdmin = insertDb($db, 'users', ['email' => $email, 'password' => $password, 'idRole' => 1]);
echo $registerAdmin ? 1 : 0;

$db = null;
