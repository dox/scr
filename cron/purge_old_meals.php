<?php
require_once '../inc/autoload.php';

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit("This script can only be run from the command line.\n");
}

// Get the retention period from settings
$days = (int) ($settings->get('bookings_retention') ?? 0); // default to 0

// Delete old bookings first
$query = "DELETE FROM bookings WHERE `date` < NOW() - INTERVAL ? DAY";
$stmt = $db->query($query, [$days]);
$deleted = $stmt->rowCount();

// then delete meals
$meals = new Meals();
$mealsToDelete = $meals->betweenDates('1970-01-01', date('Y-m-d', strtotime("-{$days} days")));

foreach ($mealsToDelete as $meal) {
	if (count($meal->bookings()) <= 0) {
		$meal->delete();
	}
}

// write the log
$log->add($deleted . ' bookings and ' . count($mealsToDelete) . ' meals older than ' . $days . ' days purged', 'System', Log::WARNING);
?>