<?php
// Get all members
$membersClass = new Members();
$members = $membersClass->all();

// CSV header row
$rowHeaders = [
	'uid', 'enabled', 'type', 'ldap', 'permissions', 'title',
	'firstname', 'lastname', 'category', 'precedence', 'email',
	'dietary', 'opt_in', 'email_reminders', 'default_wine_choice',
	'default_dessert', 'date_lastlogon', 'total_bookings'
];
fputcsv($output, $rowHeaders);

// Iterate members
if (!empty($members) && is_iterable($members)) {
	foreach ($members as $member) {
		$row = [
			'uid'                => $member->uid,
			'enabled'            => $member->enabled ? 'Yes' : 'No',
			'type'               => $member->type,
			'ldap'               => $member->ldap,
			'permissions'        => $member->permissions,
			'title'              => $member->title,
			'firstname'          => $member->firstname,
			'lastname'           => $member->lastname,
			'category'           => $member->category,
			'precedence'         => $member->precedence,
			'email'              => $member->email,
			'dietary'            => $member->dietary,
			'opt_in'             => $member->opt_in ? 'Yes' : 'No',
			'email_reminders'    => $member->email_reminders ? 'Yes' : 'No',
			'default_wine_choice'=> $member->default_wine_choice,
			'default_dessert'    => $member->default_dessert,
			'date_lastlogon'     => $member->date_lastlogon,
			'total_bookings'     => $member->bookingsCount(),
		];

		fputcsv($output, $row);
	}
}
