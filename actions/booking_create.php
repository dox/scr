<?php
include_once("../inc/autoload.php");

$bookingObject = new booking();

$bookingArray = array(
	'meal_uid' => $_POST['meal_uid'],
	'type' => 'SCR',
  'member_ldap' => strtoupper($_SESSION['username'])
);

$booking = $bookingObject->create($bookingArray);

//quit();
echo "Booking made";

$logsClass->create("booking", "Booking made for " . $_SESSION['username'] . " for meal " . $_POST['meal_uid']);
?>
