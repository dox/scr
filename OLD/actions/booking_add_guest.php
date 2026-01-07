<?php
include_once("../inc/autoload.php");

$bookingUID = filter_var($_POST['bookingUID'], FILTER_SANITIZE_NUMBER_INT);

$guestName = htmlspecialchars($_POST['guest_name'], ENT_QUOTES);
$guestDietary = explode(",", $_POST['guest_dietary']);
$guestDomusDescription = $_POST['guest_domus_description'];

if ($_POST['guest_dessert'] == "true") {
	$guestDessert = "on";
} else {
	$guestDessert = null;
}

$bookingObject = new booking($bookingUID);
$mealObject = new meal($bookingObject->meal_uid);

$newGuest = array(
	'guest_name' => $guestName,
	'guest_dietary' => $guestDietary,
	'guest_charge_to' => $_POST['guest_charge_to'],
	'guest_domus_reason' => $_POST['guest_domus_reason'],
	'guest_wine_choice' => $_POST['guest_wine_choice'],
	'guest_dessert' => $guestDessert
);

// check that we're not adding a guest above the maximum number
if (count($bookingObject->guestsArray()) < $mealObject->getTotalGuestsAllowed() || checkpoint_charlie("bookings")) {
	$bookingObject->addGuest($newGuest);
} else {
	$logArray['category'] = "booking";
	$logArray['result'] = "danger";
	$logArray['description'] = "Error attempting to add too many guests to [bookingUID:" . $bookingObject->uid . "]";
	$logsClass->create($logArray);
}
?>
