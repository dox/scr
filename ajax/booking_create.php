<?php
header('Content-Type: application/json');

require_once '../inc/autoload.php';

// Basic check: only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
	exit;
}

// Get and sanitize input
$meal_uid = filter_input(INPUT_POST, 'meal_uid', FILTER_VALIDATE_INT);
if (!$meal_uid) {
	echo json_encode(['success' => false, 'message' => 'Invalid meal UID.']);
	exit;
}

$meal = new Meal($meal_uid);
if (!$meal->canBook()) {
	echo json_encode(['success' => false, 'message' => 'Meal is not bookable.']);
	exit;
}

$booking = Booking::fromMealUID($meal->uid);
if ($booking->exists()) {
	// Don't permit double bookings, instead, return the existing booking UID (without error)
	echo json_encode(['success' => true, 'booking_uid' => $booking->uid]);
	exit;
}

try {
	$bookings = new Bookings();
	$member = Member::fromLDAP($user->getUsername());
	
	$data['meal_uid'] = $meal->uid;
	$data['member_ldap'] = $member->ldap;
	$data['type'] = $member->type;
	$data['charge_to'] = $meal->charge_to;
	$data['domus_reason'] = ($meal->charge_to === 'Domus') ? 'Meal marked as Domus' : null; // fill Domus reason if meal Domus
	$data['wine_choice'] = ($meal->allowed_wine) ? $member->default_wine_choice : 'None';
	$data['dessert'] = ($meal->hasDessertCapacity()) ? $member->default_dessert : '0';
	
	$bookingSuccessUID = $bookings->add($data); // or false if something fails
	
	if ($bookingSuccessUID) {
		// Send email reminder if required
		if ($member->email_reminders == 1 && !empty($member->email)) {
		
			$message  = "<h1>Booking Confirmation</h1>";
			$message .= "<p>You have successfully booked the following meal:</p>";
		
			$message .= "<ul>";
			$message .= "<li><strong>Name:</strong> " . htmlspecialchars($meal->name()) . "</li>";
			$message .= "<li><strong>Date & Time:</strong> " . formatDate($meal->date_meal) . " at " . formatTime($meal->date_meal) . "</li>";
			$message .= "<li><strong>Location:</strong> " . htmlspecialchars($meal->location) . "</li>";
			$message .= "</ul>";
		
			$message .= "<p><em>Please note: Changes to this booking can be made no later than "
					  . formatDate($meal->date_cutoff) . " at " . formatTime($meal->date_meal)
					  . ".</em></p>";
		
			if (!empty($meal->menu)) {
				$message .= "<hr>";
				$message .= "<h2>Menu</h2>";
				$message .= "<p>" . htmlspecialchars($meal->menu) . "</p>";
			}
		
			sendEmail(
				$member->email,
				APP_NAME . ": Booking Confirmation",
				$message
			);
		}
		
		echo json_encode(['success' => true, 'booking_uid' => $bookingSuccessUID]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Could not book this meal.']);
	}
} catch (Exception $e) {
	echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
