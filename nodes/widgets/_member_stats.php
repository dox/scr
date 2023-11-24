<?php
include_once("../../inc/autoload.php");

$memberObject = new member($_GET['memberUID']);
$mealsClass = new meals();
$termsClass = new term();
$currentTerm = $termsClass->currentTerm();

if ($_GET['scope'] == "all") {
	$totalBookingsByType = $memberObject->countBookingsByType();
} elseif ($_GET['scope'] == "year") {
	$totalBookingsByType = $memberObject->countBookingsByType(365);
} elseif ($_GET['scope'] == "term") {
	// current days since term start
	$termStartDate = new DateTime($currentTerm['date_start']);
	$now = new DateTime;
	$daysAgo = $termStartDate->diff($now)->days;
	
	$totalBookingsByType = $memberObject->countBookingsByType($daysAgo);
} else {
	$totalBookingsByType = $memberObject->countBookingsByType(365);
}

arsort($totalBookingsByType);
$totalBookingsByType = array_slice($totalBookingsByType, 0, 4, true);
?>

<div class="row">
  <?php
  if (count($totalBookingsByType) == 4) {
		$colClass = "col-6 col-sm-6 col-lg-3";
  } elseif (count($totalBookingsByType) == 3) {
	  $colClass = "col-4";
  } elseif (count($totalBookingsByType) == 2) {
	  $colClass = "col-6";
  } else {
	  $colClass = "col-12";
  }
  foreach ($totalBookingsByType AS $totalBookings) {
	$output  = "<div class=\"" . $colClass . "\">";
	$output .= "<div class=\"card mb-3\">";
	$output .= "<div class=\"card-body\">";
	$output .= "<div class=\"subheader text-nowrap\">" . $totalBookings['type'] . "</div>";
	$output .= "<div class=\"h1\">" . $totalBookings['total'] . "</div>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";

	echo $output;
  }
  ?>
</div>