<?php
include_once('inc/autoload.php');
$user->logout();
header("Location: login.php");
exit;