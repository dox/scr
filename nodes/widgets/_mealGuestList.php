<?php
include_once("../../inc/autoload.php");

if (!isset($mealObject)) {
  $mealObject = new meal($_GET['mealUID']);
}

?>

<h4 class="mb-3">Guest List</h4>

<ul>
<?php
foreach ($mealObject->bookings_this_meal() AS $booking) {
  $output = "";
  $guestsArray = json_decode($booking['guests_array']);
  $totalGuests = count($guestsArray);

  $memberObject = new member($booking['member_ldap']);
  $output .= "<li>" . $memberObject->public_displayName() . " (" . $totalGuests . autoPluralise(" guest", " guests", $totalGuests) . ")</li>";

  if ($totalGuests == $totalGuests) {
    $output .= "<ul>";

    foreach ($guestsArray AS $guest) {
      $output .= "<li>" . $guest->guest_name . "</li>";
    }
    $output .= "</ul>";
  }

  echo $output;
}
?>
</ul>
