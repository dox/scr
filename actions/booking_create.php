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

?>
