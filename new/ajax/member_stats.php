<?php
include_once('../inc/autoload.php');

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}

printArray($_GET);
?>


<div class="row row-deck row-cards mb-3">
  <?php
  $totalBookings = array("test" => 1,2,3);
  
  foreach ($totalBookings AS $typeName => $total) {
	$output  = "<div class=\"col\">";
	$output .= "<div class=\"card\">";
	$output .= "<div class=\"card-body\">";
	$output .= "<div class=\"subheader\">" . $typeName . " bookings</div>";
	$output .= "<div class=\"h1 mb-3\" id=\"test\">" . $total . "</div>";

	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";

	echo $output;

  }
  ?>
</div>