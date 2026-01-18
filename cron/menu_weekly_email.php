<?php
require_once '../inc/autoload.php';

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit("This script can only be run from the command line.\n");
}

$meals      = new Meals();
$weekStart  = $terms->firstDayOfWeek();
$subject    = "SEH Menu for week commencing " . formatDate($weekStart);
$recipients = array_filter(
	array_map('trim', explode(',', $settings->get('menu_recipients')))
);

$totalMeals = 0;
$output     = "<h1>{$subject}</h1>";

// Build menu for the week
$weekStartTimestamp = strtotime($weekStart);

for ($day = 0; $day < 7; $day++) {

	$timestamp = strtotime("+{$day} day", $weekStartTimestamp);
	$dateYmd   = date('Y-m-d', $timestamp);
	$mealsOnDay = $meals->betweenDates($dateYmd, $dateYmd);
	
	$output .= sprintf(
		'<h2 class="text-center mt-3">%s <span class="text-muted">%s</span></h2>',
		date('l', $timestamp),
		date('F jS', $timestamp)
	);

	foreach ($mealsOnDay as $meal) {

		if (empty($meal->menu)) {
			continue;
		}

		$totalMeals++;

		$output .= sprintf(
			'<p><strong>%s</strong> %s</p>',
			htmlspecialchars($meal->type, ENT_QUOTES, 'UTF-8'),
			$meal->menu
		);
	}
}

// Send email (or not)
if ($totalMeals === 0) {
	echo 'No menu data to send\n';
	return;
}

sendEmail($recipients, $subject, $output);
echo "Email sent for {$totalMeals} meals\n";

?>