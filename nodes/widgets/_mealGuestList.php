<?php
include_once("../../inc/autoload.php");

if (!isset($bookingsClass)) {
  $bookingsClass = new bookings();
}

$thisMealsBookings = $bookingsClass->bookingsByMealUID($_GET['mealUID']);

?>

<h4 class="mb-3">Guest List</h4>

<ul>
<?php
foreach ($thisMealsBookings AS $booking) {
  $output = "";

  $bookingObject = new booking($booking['uid']);
  $memberObject = new member($bookingObject->member_ldap);

  $guestsArray = $bookingObject->guestsArray();
  $totalGuests = count($guestsArray);

  $output .= "<li>" . $memberObject->public_displayName() . " (" . $totalGuests . autoPluralise(" guest", " guests", $totalGuests) . ")</li>";

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
