<?php
include_once("../../inc/autoload.php");

if (!isset($bookingsClass)) {
  $bookingsClass = new bookings();
}

$thisMealsBookingsUIDs = $bookingsClass->bookingsUIDsByMealUID($_GET['mealUID']);
?>

<h4 class="mb-3">Guest List</h4>

<ul>
<?php
foreach ($thisMealsBookingsUIDs AS $booking) {
  $output = "";

  $guestBookingObject = new booking($booking['uid']);
  $memberObject = new member($guestBookingObject->member_ldap);

  $guestsArray = $guestBookingObject->guestsArray();
  $totalGuests = count($guestsArray);

  if ($totalGuests > 0) {
    $output .= "<li>" . $memberObject->public_displayName() . " (" . $totalGuests . autoPluralise(" guest", " guests", $totalGuests) . ")</li>";
  } else {
    $output .= "<li>" . $memberObject->public_displayName() . "</li>";
  }

  if ($totalGuests == $totalGuests) {
    $output .= "<ul>";

    foreach ($guestsArray AS $guest) {
      $guest = json_decode($guest);
      $output .= "<li>" . $guest->guest_name . "</li>";
    }
    $output .= "</ul>";
  }

  echo $output;
}
?>
</ul>
