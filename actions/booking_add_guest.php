<?php
include_once("../inc/autoload.php");

$bookingUID = $_POST['bookingUID'];

$guestName = $_POST['guest_name'];
$guestDietary = $_POST['guest_dietary'];
$guestDomus = $_POST['guest_domus'];
$guestDomusDescription = $_POST['guest_domus_description'];
$guestWine = $_POST['guest_wine'];
$guestDessert = $_POST['guest_dessert'];

$bookingObject = new booking($bookingUID);

$newGuest = array(
	'guest_name' => $guestName,
	'guest_dietary' => $guestDietary,
	'guest_domus' => $guestDomus,
	'guest_domus_description' => $guestDomusDescription,
	'guest_wine' => $guestWine,
	'guest_dessert' => $guestDessert
);

$bookingObject->addGuest($newGuest);

//$logsClass->create("booking", $guestName . " added as guest to booking UID " . $bookingUID);
?>
