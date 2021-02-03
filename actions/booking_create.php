<?php
include_once("../inc/autoload.php");

$bookingObject = new booking();
$mealObject = new meal($_POST['meal_uid']);
$memberObject = new member($_SESSION['username']);

$domusValue = "0";
$domusReason = null;
$wineValue = "0";
$dessertValue = "0";
if ($mealObject->domus == 1) {
	$domusValue = "1";
	$domusReason = "Meal auto-marked as Domus";
} else {
	if ($memberObject->default_domus == 1) {
		$domusValue = "1";
		$domusReason = "Meal auto-marked as Domus";
	}
}

if ($mealObject->allowed_wine == 1 && $memberObject->default_wine == 1) {
	$wineValue = "1";
}

if ($mealObject->allowed_dessert == 1 && $memberObject->default_dessert == 1) {
	$wineValue = "1";
}

$bookingArray = array(
	'meal_uid' => $_POST['meal_uid'],
	'type' => $memberObject->type,
	'member_ldap' => strtoupper($_SESSION['username']),
	'domus' => $domusValue,
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
