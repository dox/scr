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

try {
	$data['meal_uid'] = $meal_uid;
	$data['member_ldap'] = $user->getUsername();
	$data['type'] = "SCR";
	
	$bookings = new Bookings();
	$bookingSuccessUID = $bookings->create($data); // or false if something fails
	
	if ($bookingSuccessUID) {
		echo json_encode(['success' => true, 'booking_uid' => $bookingSuccessUID]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Could not book this meal.']);
	}
} catch (Exception $e) {
	echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}