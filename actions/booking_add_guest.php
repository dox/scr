<?php
include_once("../inc/autoload.php");

$bookingUID = $_POST['bookingUID'];

$guestName = htmlspecialchars($_POST['guest_name'], ENT_QUOTES);
$guestDietary = explode(",", $_POST['guest_dietary']);
$guestDomusDescription = $_POST['guest_domus_description'];

if ($_POST['guest_wine'] == "true") {
	$guestWine = "on";
} else {
	$guestWine = null;
}

if ($_POST['guest_dessert'] == "true") {
	$guestDessert = "on";
} else {
	$guestDessert = null;
}

$bookingObject = new booking($bookingUID);

$newGuest = array(
	'guest_name' => $guestName,
	'guest_dietary' => $guestDietary,
	'guest_charge_to' => $_POST['guest_charge_to'],
	'guest_domus_reason' => $_POST['guest_domus_reason'],
	'guest_wine' => $guestWine,
	'guest_dessert' => $guestDessert
);

$bookingObject->addGuest($newGuest);

//$logsClass->create("booking", $guestName . " added as guest to booking UID " . $bookingUID);
?>
