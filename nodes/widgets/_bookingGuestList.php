<?php
include_once("../../inc/autoload.php");

if (!isset($bookingObject)) {
  $bookingObject = new booking($_GET['bookingUID']);
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
    $deleteIcon = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-x-circle-fill\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\"><path fill-rule=\"evenodd\" d=\"M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z\"/></svg>";

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
    $output .= "<h6 class=\"my-0\">" . $guestObject->guest_name . " " . $badge . "</h6>";
    $output .= "<p>" . implode(" ", $icons) . "</p>";
    if (!empty($guestObject->guest_dietary)) {
      $output .= "<small class=\"text-muted\">" . implode(", ", $guestObject->guest_dietary) . "</small>";
    }

    $output .= "</div>";
    $output .= "<span class=\"text-muted\" id=\"" . $guestObject->guest_uid . "\" onclick=\"deleteGuest(this.id)\">" . $deleteIcon . "</span>";
    $output .= "</li>";

    echo $output;
  }
  ?>
</ul>
