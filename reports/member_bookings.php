<?php
$uid = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$member = Member::fromUID($uid);

if(!$member->uid) {
	die("Invalid member UID.");
}

if (($member->ldap != $user->getUsername()) && !$user->hasPermission("reports"))  {
	die("User does not have permission to run reports or is not logged in.");
}
	
// CSV header row (columns)
$rowHeaders = [
	'booking_uid',
	'booking_date',
	'type',
	'member_ldap',
	'name',
	'guests_count',
	'charge_to',
	'domus_reason',
	'wine_choice',
	'dessert',
	'meal_uid',
	'meal_name',
	'meal_type',
	'meal_date',
	'meal_menu'
];

fputcsv($output, $rowHeaders);

$recentBookings = $member->bookingsBetweenDates(date('Y-m-d', strtotime('-10 years')), date('Y-m-d', strtotime('+1 10 years')));
//krsort($recentBookings);

foreach ($recentBookings as $booking) {
	$meal = new Meal($booking->meal_uid);
	
	$row = [];
	
	$row['booking_uid'] = $booking->uid;
	$row['booking_date'] = $booking->date;
	$row['booking_type'] = $booking->type;
	$row['member_ldap'] = $booking->member_ldap;
	$row['name'] = $member->name();
	$row['guests_count'] = count($booking->guests());
	$row['charge_to'] = $booking->charge_to;
	$row['domus_reason'] = $booking->domus_reason;
	$row['wine_choice'] = $booking->wine_choice;
	$row['dessert'] = $booking->dessert;
	
	$row['meal_uid'] = $meal->uid;
	$row['meal_name'] = $meal->name();
	$row['meal_type'] = $meal->type;
	$row['meal_date'] = $meal->date_meal;
	$row['meal_menu'] = $meal->cleanMenu();
	
	fputcsv($output, $row);
	
	foreach ($booking->guests() as $guest) {
		$row = [];
	
		$row['booking_uid']   = $booking->uid          ?? '';
		$row['booking_date']  = $booking->date         ?? '';
		$row['booking_type']  = $booking->type         ?? '';
		$row['member_ldap']   = 'GUEST';
	
		$row['name']          = $guest['guest_name']           ?? '';
		$row['guests_count']  = '';
		$row['charge_to']     = $guest['guest_charge_to']      ?? '';
		$row['domus_reason']  = $guest['guest_domus_reason']   ?? '';
		$row['wine_choice']   = $guest['guest_wine_choice']    ?? '';
		$row['dessert']       = $booking->dessert              ?? '';
	
		$row['meal_uid']      = $meal->uid          ?? '';
		$row['meal_name']     = $meal->name();
		$row['meal_type']     = $meal->type;
		$row['meal_date']     = $meal->date_meal    ?? '';
		$row['meal_menu']     = $meal->cleanMenu()         ?? '';
	
		fputcsv($output, $row);
	}
}
