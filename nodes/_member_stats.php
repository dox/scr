<?php
$memberObject = new member($memberUID);
$mealsClass = new meals();

foreach ($mealsClass->mealTypes() AS $type) {
  $totalBookings[$type] = $memberObject->count_allBookings($type);
}

arsort($totalBookings);
$totalBookings = array_slice($totalBookings, 0, 4, true);
?>
<div class="row row-deck row-cards mb-3">
  <?php
  foreach ($totalBookings AS $typeName => $total) {
    $output  = "<div class=\"col-sm-6 col-lg-3\">";
    $output .= "<div class=\"card\">";
    $output .= "<div class=\"card-body\">";
    $output .= "<div class=\"subheader\">" . $typeName . " bookings</div>";
    $output .= "<div class=\"h1 mb-3\">" . $total . "</div>";

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
