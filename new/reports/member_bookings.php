<?php
$uid = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$member = Member::fromUID($uid);

if(!$member->uid) {
	die("Invalid member UID.");
}
	
// CSV header row (columns)
$rowHeaders = [
	'uid',
	'date',
	'type',
	'member_ldap',
	'guests_count',
	'guests_array',
	'charge_to',
	'domus_reason',
	'wine_choice',
	'dessert'
];

fputcsv($output, $rowHeaders);

foreach ($member->recentBookings() as $booking) {
	$row = [];
	
	$row['uid'] = $booking->uid;
	$row['date'] = $booking->date;
	$row['type'] = $booking->type;
	$row['member_ldap'] = $booking->member_ldap;
	$row['guests_count'] = count($booking->guests());
	$row['guests_array'] = $booking->guests_array;
	$row['charge_to'] = $booking->charge_to;
	$row['domus_reason'] = $booking->domus_reason;
	$row['wine_choice'] = $booking->wine_choice;
	$row['dessert'] = $booking->dessert;
	
	fputcsv($output, $row);
}