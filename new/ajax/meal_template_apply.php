<?php
require_once '../inc/autoload.php';
$user->pageCheck('meals');

try {
	// --- 1. Gather input ---
	$templateMealUID  = filter_input(INPUT_POST, 'template_meal_uid', FILTER_VALIDATE_INT);
	$weekStartRaw     = filter_input(INPUT_POST, 'template_week_start', FILTER_DEFAULT);
	$weekCount        = filter_input(INPUT_POST, 'week_count', FILTER_VALIDATE_INT);
	$templateDays     = $_POST['template_days'] ?? [];
	
	if (!$templateMealUID) {
		throw new RuntimeException('No template meal specified.');
	}
	if (!$weekStartRaw) {
		throw new RuntimeException('No week start date specified.');
	}
	if (empty($templateDays)) {
		throw new RuntimeException('No days selected.');
	}
	
	$meals = new Meals();
	$templateMeal = new Meal($templateMealUID);

	$weekStart = new DateTime($weekStartRaw);

	// --- 2. Map day names to weekday numbers (Sunday = 0) ---
	$dayMap = [
		'sunday'    => 0,
		'monday'    => 1,
		'tuesday'   => 2,
		'wednesday' => 3,
		'thursday'  => 4,
		'friday'    => 5,
		'saturday'  => 6,
	];

	// --- 3. Iterate through weeks ---
	$datesCreated = [];

	foreach (range(0, $weekCount - 1) as $weekOffset) {
		$currentWeekStart = clone $weekStart;
		$currentWeekStart->modify("+{$weekOffset} weeks");
	
		foreach ($templateDays as $dayName) {
			if (!isset($dayMap[$dayName])) continue;
	
			$dayOffset = $dayMap[$dayName];
	
			// --- New meal date with template time ---
			$templateMealDate = new DateTime($templateMeal->date_meal);
			$newMealDate = clone $templateMealDate;
			$newMealDate->setDate(
				(int)$currentWeekStart->format('Y'),
				(int)$currentWeekStart->format('m'),
				(int)$currentWeekStart->format('d')
			);
			$newMealDate->modify("+{$dayOffset} days");
	
			// --- Cutoff: same interval as template ---
			$templateMealTimestamp   = strtotime($templateMeal->date_meal);
			$templateCutoffTimestamp = strtotime($templateMeal->date_cutoff);
			$offsetSeconds = $templateMealTimestamp - $templateCutoffTimestamp;
	
			$newCutoffTimestamp = $newMealDate->getTimestamp() - $offsetSeconds;
			$newCutoffDate = (new DateTime())->setTimestamp($newCutoffTimestamp);
	
			$mealData = [
				'type'                  => $templateMeal->type,
				'name'                  => $templateMeal->name,
				'location'              => $templateMeal->location,
				'date_meal'             => $newMealDate->format('Y-m-d H:i:s'),
				'date_cutoff'           => $newCutoffDate->format('Y-m-d H:i:s'),
				'scr_capacity'          => $templateMeal->scr_capacity,
				'scr_dessert_capacity'  => $templateMeal->scr_dessert_capacity,
				'scr_guests'            => $templateMeal->scr_guests,
				'charge_to'             => $templateMeal->charge_to,
				'allowed_wine'          => $templateMeal->allowed_wine,
				'allowed_dessert'       => $templateMeal->allowed_dessert,
				'allowed'               => $templateMeal->allowed,
				//'menu'                  => $templateMeal->menu,
				'notes'                 => $templateMeal->notes,
				'photo'                 => $templateMeal->photo,
			];
	
			$meals->create($mealData);
			$datesCreated[] = $newMealDate->format('Y-m-d H:i');
		}
	}
	
	// --- 5. Output success ---
	if (!empty($datesCreated)) {
		echo "<div class=\"alert alert-success\">Meal template applied successfully for the following dates:</div>";
		echo implode('<br>', $datesCreated);
	} else {
		echo "No meals were created.";
	}

} catch (Exception $e) {
	http_response_code(400);
	echo "Error: " . $e->getMessage();
}

?>
