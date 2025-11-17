<?php
include_once('../inc/autoload.php');

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}

$meals = new Meals();
$date = $_GET['date'] ?? '';
$validDate = null;

if ($date) {
	$dt = DateTime::createFromFormat('Y-m-d', $date);
	if ($dt && $dt->format('Y-m-d') === $date) {
		$validDate = $date; // Safe, valid YYYY-MM-DD
	} else {
		// Invalid date format
		$validDate = null;
	}
}

$start = $terms->firstDayOfWeek($validDate);
$end   = $terms->lastDayOfWeek($validDate);

$mealsBetweenDates = $meals->betweenDates($start, $end);

// Group meals by date
$mealsByDate = [];
foreach ($mealsBetweenDates as $meal) {
	$mealDate = date('Y-m-d', strtotime($meal->date_meal));
	$mealsByDate[$mealDate][] = $meal;
}

// Loop through each day of the week
$current = new DateTime($start);
$endDate  = new DateTime($end);



while ($current <= $endDate) {
	$dateStr = $current->format('Y-m-d');
	
	$output  = "<h3 class=\"text-center\">" . $current->format('l') . " <span class=\"text-muted\">" . $current->format('F jS') . "</span></h3>"; // Day title
	$output .= "<div class=\"row row-cols-1 row-cols-md-3 justify-content-center mb-3\">";
	
	if (!empty($mealsByDate[$dateStr])) {
		foreach ($mealsByDate[$dateStr] as $meal) {
			$output .= $meal->card();
		}
	} else {
		$output .= "<p class=\"text-center mb-3\">No meals</p>";
	}
	
	$output .= "</div>";
	
	echo $output;

	$current->modify('+1 day');
}

?>