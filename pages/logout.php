<?php
// logout.php - Proses logout
session_start();
require_once '../config/database.php';
require_once '../classes/User.php';

$db = new Database();
$user = new User($db->getConnection());
$user->logout();

header("Location: login.php");
exit();
?>
