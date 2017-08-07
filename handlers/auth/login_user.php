<?php
$app = include("../../config/app_params.php");
require_once $app['root'] . "includes/db_connect.php";
require_once $app['root'] . "includes/functions.php";

$_SESSION['accessFlag'] = 0;

$selUser = selectDb($db, 'users', '*', ['email' => $_POST['email']]);
$rowCount = $selUser->rowCount();

// If an user not found
if ($rowCount == 0) {
    echo 0;
} else {
    $fetch = $selUser->fetch();
    // Correct Password
    if (validate_pw($_POST['password'], $fetch['password']) == true) {
        $idRole = $fetch['idRole'];
        $selRole = selectDb($db, 'roles', ['nameRole'], ['idRole' => $idRole]);

        $_SESSION['role'] = $selRole->fetchColumn(0);
        $_SESSION['accessFlag'] = 1;
        $_SESSION['idUser'] = $fetch['idUser'];
        $_SESSION['email'] = $fetch['email'];

        $_SESSION['token'] = generate_hash(md5(uniqid(rand(), true)), 11);
        echo 1;
    } else {
        // Incorrect Password
        echo 3;
    }
}
$db = null;
