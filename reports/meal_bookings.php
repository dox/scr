<?php
$uid = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$meal = new Meal($uid);

if(!$meal->uid) {
	die("Invalid meal UID.");
}
	
// CSV header row (columns)
$rowHeaders = [
	'booking_uid',
	'booking_date',
	'type',
	'member_ldap',
	'member_type',
	'member_category',
	'name',
	'guests_count',
	'charge_to',
	'domus_reason',
	'wine_choice',
	'dessert',
	'meal_uid',
	'meal_name',
	'meal_date',
	'meal_menu'
];

fputcsv($output, $rowHeaders);

foreach ($meal->bookings() as $booking) {
	$meal = new Meal($booking->meal_uid);
	$member = Member::fromLDAP($booking->member_ldap);
	
	$row = [];
	
	$row['booking_uid'] = $booking->uid;
	$row['booking_date'] = $booking->date;
	$row['booking_type'] = $booking->type;
	$row['member_ldap'] = $booking->member_ldap;
	$row['member_type'] = $member->type;
	$row['member_category'] = $member->category;
	$row['name'] = $member->name();
	$row['guests_count'] = count($booking->guests());
	$row['charge_to'] = $booking->charge_to;
	$row['domus_reason'] = $booking->domus_reason;
	$row['wine_choice'] = $booking->wine_choice;
	$row['dessert'] = $booking->dessert;
	
	$row['meal_uid'] = $meal->uid;
	$row['meal_name'] = $meal->name();
	$row['meal_date'] = $meal->date_meal;
	$row['meal_menu'] = $meal->cleanMenu();
	
	fputcsv($output, $row);
	
	foreach ($booking->guests() as $guest) {
		$row = [];
	
		$row['booking_uid']   = $booking->uid          ?? '';
		$row['booking_date']  = $booking->date         ?? '';
		$row['booking_type']  = $booking->type         ?? '';
		$row['member_ldap']   = $booking->member_ldap;
		$row['member_type']   = $member->type . ' GUEST';
		$row['member_category'] = $member->category . ' GUEST';
	
		$row['name']          = $guest['guest_name']           ?? '';
		$row['guests_count']  = '';
		$row['charge_to']     = $guest['guest_charge_to']      ?? '';
		$row['domus_reason']  = $guest['guest_domus_reason']   ?? '';
		$row['wine_choice']   = $guest['guest_wine_choice']    ?? '';
		$row['dessert']       = $booking->dessert              ?? '';
	
		$row['meal_uid']      = $meal->uid          ?? '';
		$row['meal_name']     = $meal->name();
		$row['meal_date']     = $meal->date_meal    ?? '';
		$row['meal_menu']     = $meal->cleanMenu()         ?? '';
	
		fputcsv($output, $row);
	}
}