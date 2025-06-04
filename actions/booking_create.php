<?php
include_once("../inc/autoload.php");

$uid = filter_var($_POST['meal_uid'], FILTER_SANITIZE_NUMBER_INT);

$bookingObject = new booking();

$mealObject = new meal($uid);

if (isset($_POST['member_ldap'])) {
	$memberObject = new member($_POST['member_ldap']);
} else {
	$memberObject = new member($_SESSION['username']);
}

$wineChoice = "";
$dessertValue = "0";

if ($mealObject->allowed_wine == 1) {
	$wineChoice = $memberObject->defaultWineChoice();
}

if ($mealObject->allowed_dessert == 1 && $memberObject->default_dessert == 1) {
	// check if dessert capacity is reached
	if ($mealObject->total_dessert_bookings_this_meal() < $mealObject->scr_dessert_capacity) {
		$dessertValue = "1";
	}
}

$bookingArray = array(
	'meal_uid' => $mealObject->uid,
	'type' => $memberObject->type,
	'member_ldap' => strtoupper($memberObject->ldap),
	'charge_to' => $mealObject->charge_to,
	'domus_reason' => $domusReason,
	'wine_choice' => $wineChoice,
	'dessert' => $dessertValue
);

$mealBookableCheck = $mealObject->check_meal_bookable();

if ($mealBookableCheck == true || checkpoint_charlie("bookings")) {
	$booking = $bookingObject->create($bookingArray);
	
	if ($memberObject->email_reminders == "1") {
		$body  = "<h1>You have booked onto the following meal:</h1>";
		$body .= "<p>Name: " . $mealObject->name . "</p>";
		$body .= "<p>Date/Time: " . dateDisplay($mealObject->date_meal, true) . " at " . timeDisplay($mealObject->date_meal) . "</p>";
		$body .= "<p>Location: " . $mealObject->location . "</p>";
		$body .= "<br /><p><i>Changes to this booking can be made no later than: " . dateDisplay($mealObject->date_cutoff, true) . " at " . timeDisplay($mealObject->date_meal) . "</i></p>";
		
		if (!empty($mealObject->menu)) {
			$body .= "<hr><h2>Menu</h2><p>" . nl2br($mealObject->menu) . "</p>";
		}
		
		//$url = "http://scr2.seh.ox.ac.uk/calendar.php?hash=" . $memberObject->calendar_hash;
		//$body .= "<p>You can add all your SCR meal bookings to your calendar, by <a href=\"" . $url . "\">subscribing to your ICS file here</a></p>";
		
		
		sendMail("SCR Meal Booking Confirmation", array($memberObject->email), $body);
	}
} else {
	echo "Error: An error occured when making this booking.  Please refresh the page and try again.  If the problem persists, please contact the Bursary.";
}

?>
