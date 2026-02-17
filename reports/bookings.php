<?php
// Get date range from POST
$start = filter_input(INPUT_POST, 'from_date', FILTER_DEFAULT);
$end   = filter_input(INPUT_POST, 'to_date', FILTER_DEFAULT);

if (!$start || !$end) {
	die('Invalid or missing date range.');
}

// Optional: enforce YYYY-MM-DD format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) ||
	!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
	die('Invalid date format.');
}

// Get meals in date range
$mealsObj = new Meals();
$meals = $mealsObj->betweenDates($start, $end);

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
	'meal_date'
];
fputcsv($output, $rowHeaders);

// Iterate meals → bookings → guests
if (!empty($meals) && is_iterable($meals)) {
	foreach ($meals as $meal) {

		$bookings = $meal->bookings('all');
		if (!empty($bookings) && is_iterable($bookings)) {

			foreach ($bookings as $booking) {
				$member = Member::fromLDAP($booking->member_ldap);

				$row = [
					'booking_uid'   => $booking->uid ?? '',
					'booking_date'  => $booking->date ?? '',
					'type'          => $booking->type ?? '',
					'member_ldap'   => $booking->member_ldap ?? '',
					'member_type'   => $member->type ?? '',
					'member_category' => $member->category ?? '',
					'name'          => $member ? $member->name() : '',
					'guests_count'  => count($booking->guests() ?? []),
					'charge_to'     => $booking->charge_to ?? '',
					'domus_reason'  => $booking->domus_reason ?? '',
					'wine_choice'   => $booking->wine_choice ?? '',
					'dessert'       => $booking->dessert ?? '',
					'meal_uid'      => $meal->uid ?? '',
					'meal_name'     => $meal->name() ?? '',
					'meal_date'     => $meal->date_meal ?? ''
				];

				fputcsv($output, $row);

				// Guest rows
				foreach ($booking->guests() ?? [] as $guest) {
					$row = [
						'booking_uid'   => $booking->uid ?? '',
						'booking_date'  => $booking->date ?? '',
						'type'          => $booking->type ?? '',
						'member_ldap'   => 'GUEST',
						'member_ldap'   => $booking->member_ldap ?? '',
						'member_type'   => $member->type . ' GUEST' ?? '',
						'member_category' => $member->category . ' GUEST' ?? '',
						'name'          => $guest['guest_name'] ?? '',
						'guests_count'  => '',
						'charge_to'     => $guest['guest_charge_to'] ?? '',
						'domus_reason'  => $guest['guest_domus_reason'] ?? '',
						'wine_choice'   => $guest['guest_wine_choice'] ?? '',
						'dessert'       => $booking->dessert ?? '',
						'meal_uid'      => $meal->uid ?? '',
						'meal_name'     => $meal->name() ?? '',
						'meal_date'     => $meal->date_meal ?? ''
					];
					fputcsv($output, $row);
				}
			}

		}
	}
}