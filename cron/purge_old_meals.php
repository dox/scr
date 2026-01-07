<?php
require_once '../inc/autoload.php';

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit("This script can only be run from the command line.\n");
}

$meals = new Meals();

// Get the retention period from settings
$days = (int) $settings->get('bookings_retention'); // default to 0
$days = 2100;

// Delete old bookings first
$query = "DELETE FROM bookings WHERE `date` < NOW() - INTERVAL ? DAY";
$stmt = $db->query($query, [$days]);
$deleted = $stmt->rowCount();
echo "$deleted bookings purged\n\n";


// then delete meals
$mealsToDelete = $meals->betweenDates('1970-01-01', date('Y-m-d', strtotime("-{$days} days")));

foreach ($mealsToDelete as $meal) {
	if (count($meal->bookings()) <= 0) {
		echo "Deleting meal UID: " . $meal->uid . "\n";
		$meal->delete();
	}
}

echo "\nMeals (and bookings) purged older than " . $days . " days.\n";
?>