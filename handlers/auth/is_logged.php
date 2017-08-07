<?php
if (!$_SESSION['accessFlag']) {
    session_destroy();
    header("location: ../../views/auth/login.php");
    exit();
}