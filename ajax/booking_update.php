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
	echo json_encode(['success' => false, 'message' => 'Invalid booking UID provided.']);
	exit;
}

$booking = Booking::fromUID($booking_uid);
if (!$booking->exists()) {
	echo json_encode(['success' => false, 'message' => 'No booking reference recorded.']);
	exit;
}

$meal = new Meal($booking->meal_uid);
if (!$meal->isCutoffValid(true)) {
	echo json_encode(['success' => false, 'message' => 'Update failed.  Meal cut-off has passed.']);
	exit;
}

try {
	// meal cutoff reached?
	// allowed wine?
	// allowed dessert? (GUESTS!?)
	
	if ($_POST['action'] == 'guest_add') {
		// Safely pull the raw string
		$guestDietaryArray = $_POST['guest_dietary'] ?? [];
		$guestDietaryArray = array_map('trim', $guestDietaryArray);
		
		$data = array(
		  'guest_name'			=> $_POST['guest_name']     ?? null,
		  'guest_charge_to'		=> $_POST['charge_to']     ?? null,
		  'guest_domus_reason'	=> $_POST['domus_reason']  ?? null,
		  'guest_wine_choice'	=> $_POST['wine_choice']   ?? null,
		  'guest_dietary'		=> $guestDietaryArray
		);
		
		$booking->addGuest($data);
		
		echo json_encode(['success' => true, 'message' => 'Guest added successfully']);
		exit;
	} elseif ($_POST['action'] == 'guest_update') {
		// Safely pull the raw guest dietary array
		$guestDietaryArray = $_POST['guest_dietary'] ?? [];
		$guestDietaryArray = is_array($guestDietaryArray)
			? array_map('trim', $guestDietaryArray)
			: [];
		
		// Handle checkboxes / arrays (dietary)
		$maxChoices = (int) $settings->get('meal_dietary_allowed');
		
		$fields['dietary'] = (
			!empty($postData['dietary']) && is_array($postData['dietary'])
		)
			? implode(',', array_slice(array_filter($postData['dietary']), 0, $maxChoices))
			: '';
		
		// Apply the same limit to guest dietary
		$guestDietary = !empty($guestDietaryArray)
			? implode(',', array_slice(array_filter($guestDietaryArray), 0, $maxChoices))
			: '';
		
		$data = [
			'guest_uid'          => $_POST['guest_uid']        ?? null,
			'guest_name'         => $_POST['guest_name']       ?? null,
			'guest_charge_to'    => $_POST['charge_to']        ?? null,
			'guest_domus_reason' => $_POST['domus_reason']     ?? null,
			'guest_wine_choice'  => $_POST['wine_choice']      ?? null,
			'guest_dietary'      => $guestDietary,
		];
		
		$booking->editGuest($data);
		
		echo json_encode([
			'success' => true,
			'message' => 'Guest updated successfully.',
		]);
		exit;
	} elseif ($_POST['action'] == 'guest_delete') {
		$booking->deleteGuest($_POST['guest_uid']);
		
		echo json_encode(['success' => true, 'message' => 'Guest deleted successfully']);
		exit;
	} elseif ($_POST['action'] == 'booking_update') {
		$data = array(
		  'uid'          => $_POST['booking_uid']   ?? null,
		  'charge_to'    => $_POST['charge_to']     ?? null,
		  'domus_reason' => $_POST['domus_reason']  ?? null,
		  'wine_choice'  => $_POST['wine_choice']   ?? null,
		  'dessert'      => $_POST['dessert']       ?? 0
		);
		
		$bookingUpdate = $booking->update($data);
		
		echo json_encode(['success' => true, 'message' => 'Booking updated successfully.']);
	} else {
		echo json_encode(['success' => false, 'message' => 'Unknown action requested.']);
		exit;
	}
} catch (Exception $e) {
	echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
