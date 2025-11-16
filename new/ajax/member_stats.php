<?php
include_once('../inc/autoload.php');

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}

printArray($_GET);
?>