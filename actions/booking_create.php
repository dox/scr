<?php
include_once("../inc/autoload.php");

$bookingObject = new booking();

$mealObject = new meal($_POST['meal_uid']);

if (isset($_POST['member_ldap'])) {
	$memberObject = new member($_POST['member_ldap']);
} else {
	$memberObject = new member($_SESSION['username']);
}

$wineValue = "0";
$dessertValue = "0";

if ($mealObject->allowed_wine == 1 && $memberObject->default_wine == 1) {
	$wineValue = "1";
}

if ($mealObject->allowed_dessert == 1 && $memberObject->default_dessert == 1) {
	// check if dessert capacity is reached
	if ($mealObject->total_dessert_bookings_this_meal() < $mealObject->scr_dessert_capacity) {
		$dessertValue = "1";
	}
}

$bookingArray = array(
	'meal_uid' => $_POST['meal_uid'],
	'type' => $memberObject->type,
	'member_ldap' => strtoupper($memberObject->ldap),
	'charge_to' => $mealObject->charge_to,
	'domus_reason' => $domusReason,
	'wine' => $wineValue,
	'dessert' => $dessertValue
);

$mealBookableCheck = $mealObject->check_meal_bookable();

if ($mealBookableCheck == true) {
	$booking = $bookingObject->create($bookingArray);
} else {
	if ($_SESSION['admin'] == true) {
		$booking = $bookingObject->create($bookingArray);
	} else {
		echo "Error: An error occured when making this booking.  Please refresh the page and try again.  If the problem persists, please contact the Bursary.";
	}
}

?>
