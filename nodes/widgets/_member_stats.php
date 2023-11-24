<?php
include_once("../../inc/autoload.php");

$memberObject = new member($_GET['memberUID']);
$mealsClass = new meals();
$termsClass = new term();
$currentTerm = $termsClass->currentTerm();

if ($_GET['scope'] == "all") {
	foreach ($mealsClass->mealTypes() AS $type) {
	  $totalBookings[$type] = $memberObject->count_allBookings($type);
	}
} elseif ($_GET['scope'] == "year") {
	foreach ($mealsClass->mealTypes() AS $type) {
	  $totalBookings[$type] = $memberObject->count_allBookings($type, 365);
	}
} elseif ($_GET['scope'] == "term") {
	// current days since term start
	$termStartDate = new DateTime($currentTerm['date_start']);
	$now = new DateTime;
	$daysAgo = $termStartDate->diff($now)->days;
	
	foreach ($mealsClass->mealTypes() AS $type) {
	  $totalBookings[$type] = $memberObject->count_allBookings($type, $daysAgo);
	}
} else {
	foreach ($mealsClass->mealTypes() AS $type) {
	  $totalBookings[$type] = $memberObject->count_allBookings($type, 365);
	}
}

arsort($totalBookings);
$totalBookings = array_slice($totalBookings, 0, 4, true);
?>

<div class="row row-deck row-cards">
  <?php
  foreach ($totalBookings AS $typeName => $total) {
	$output  = "<div class=\"col-6 col-sm-6 col-lg-3 mb-3\">";
	$output .= "<div class=\"card\">";
	$output .= "<div class=\"card-body\">";
	$output .= "<div class=\"subheader\">" . $typeName . " bookings</div>";
	$output .= "<div class=\"h1 mb-3\" id=\"test\">" . $total . "</div>";

	//$output .= "<div class=\"progress progress-sm\">";
	//$output .= "<div class=\"progress-bar bg-blue\" style=\"width: 75%\" role=\"progressbar\" aria-valuenow=\"75\" aria-valuemin=\"0\" aria-valuemax=\"100\">";
	//$output .= "<span class=\"visually-hidden\">75% Complete</span>";
	//$output .= "</div>";
	//$output .= "</div>";

	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";

	echo $output;

  }
  ?>
</div>