<?php
header('Content-Type: application/json');

require_once '../inc/autoload.php';

// Basic check: only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
	exit;
}

// Get and sanitize input
$booking_uid = filter_input(INPUT_POST, 'booking_uid', FILTER_VALIDATE_INT);
if (!$booking_uid) {
	echo json_encode(['success' => false, 'message' => 'Invalid booking UID.']);
	exit;
}

$booking = Booking::fromUID($booking_uid);
$meal = new Meal($booking->meal_uid);

if (!$meal->isCutoffValid() && !$user->hasPermission("bookings")) {
	echo json_encode(['success' => false, 'message' => 'Meal cut-off date has passed.']);
	exit;
}

try {
	if ($booking->delete()) {
		toast('Booking Deleted', 'Booking sucesfully deleted', 'text-success');
		echo json_encode(['success' => true, 'message' => 'Booking deleted.']);
	} else {
		echo json_encode(['success' => false, 'message' => 'Booking deletion failed.']);
	}
} catch (Exception $e) {
	echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}