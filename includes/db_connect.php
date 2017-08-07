<?php
/**
 * Setting up DB config array
 **/
$db = include($app['root'] . "config/db_params.php");

/**
 * Connecting with DB using PDO
 **/
$dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
$db = new PDO($dsn, $db['user'], $db['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* Opening session */
if (!isset($_SESSION)) session_start();

