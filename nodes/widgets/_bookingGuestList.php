<?php
include_once("../../inc/autoload.php");

if (!isset($bookingObject)) {
  $bookingsClass = new bookings();
  $mealObject = new meal($_GET['mealUID']);

  $bookingByMember = $bookingsClass->bookingForMealByMember($mealObject->uid, $_SESSION['username']);
  $bookingObject = new booking($bookingByMember['uid']);
}

?>

<h4 class="d-flex justify-content-between align-items-center mb-3">
  <span>Your Guests</span>
  <span class="badge bg-secondary rounded-pill"><?php echo count($bookingObject->guestsArray()); ?></span>
</h4>
<ul class="list-group mb-3" id="guests_list">
  <?php
  foreach ($bookingObject->guestsArray() AS $guest) {
    $guestObject = json_decode($guest);

    $editIcon  = "<a href=\"#\" class=\"float-start\" id=\"bookingUID-" . $bookingObject->uid . "\" data-guestUID=\"" . $guestObject->guest_uid . "\" data-bs-toggle=\"modal\" data-bs-target=\"#modalGuestAdd\" onclick=\"editGuestModal(this)\">";
    $editIcon .= "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#sliders\"/></svg>";
    $editIcon .= "</a>";

    $icons = array();

    if ($guestObject->guest_domus == "on") {
      $icons[] = "<svg width=\"1em\" height=\"1em\" class=\"text-muted\"><use xlink:href=\"img/icons.svg#graduation-cap\"/></svg>";
    }
    if ($guestObject->guest_wine == "on") {
      $icons[] = "<svg width=\"1em\" height=\"1em\" class=\"text-muted\"><use xlink:href=\"img/icons.svg#wine-glass\"/></svg>";
    }
    if ($guestObject->guest_dessert == "on") {
      $icons[] = "<svg width=\"1em\" height=\"1em\" class=\"text-muted\"><use xlink:href=\"img/icons.svg#cookie\"/></svg>";
    }
    $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
    $output .= "<div>";
    $output .= "<h6 class=\"my-0\">" . htmlspecialchars_decode($guestObject->guest_name) . " " . $badge . "</h6>";
    $output .= "<p>" . implode(" ", $icons) . "</p>";
    if (!empty($guestObject->guest_dietary)) {
      $output .= "<small class=\"text-muted\">" . implode(", ", $guestObject->guest_dietary) . "</small>";
    }

    $output .= "</div>";
    $output .= $editIcon;
    $output .= "</li>";

    echo $output;
  }
  ?>
</ul>
