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

try {
	$bookings = new Bookings();
	$member = Member::fromLDAP($user->getUsername());
	
	$data['meal_uid'] = $meal->uid;
	$data['member_ldap'] = $member->ldap;
	$data['type'] = $member->type;
	$data['charge_to'] = $meal->charge_to;
	$data['wine_choice'] = ($meal->allowed_wine) ? $member->default_wine_choice : '0';
	$data['dessert'] = ($meal->hasDessertCapacity()) ? $member->default_dessert : '0';
	
	$bookingSuccessUID = $bookings->create($data); // or false if something fails
	
	if ($bookingSuccessUID) {
		echo json_encode(['success' => true, 'booking_uid' => $bookingSuccessUID]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Could not book this meal.']);
	}
} catch (Exception $e) {
	echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}