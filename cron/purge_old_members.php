<?php
require_once '../inc/autoload.php';

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit("This script can only be run from the command line.\n");
}

// Get the retention period from settings
$days = (int) ($settings->get('bookings_retention') ?? 0); // default to 0

// Get a list of all members
$membersClass = new Members();
$members = $membersClass->all();

foreach ($members as $member) {
	$deleted = 0;
	
	// skip members who are currently enabled
	if ($member->enabled == true) {
		continue;
	}
	
	// skip members who do not have 0 bookings
	$totalBookings = $member->bookingsCount();
	
	if ($totalBookings > 0) {
		continue;
	} else {
		echo "Delete member " . $member->name() . " as they have " . $totalBookings . " bookings.\n";
		$member->delete();
		$deleted ++;
	}
}

// write the log
$log->add($deleted . ' disabled members with 0 bookings purged', 'System', Log::WARNING);
?>