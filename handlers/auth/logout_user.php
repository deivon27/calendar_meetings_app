<?php
$app = include("../../config/app_params.php");
require_once $app['root'] . "includes/functions.php";

sessionDestroy();
header("Location: ../../views/auth/login.php");
exit();