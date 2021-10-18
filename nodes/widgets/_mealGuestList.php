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
  
  if ($guestBookingObject->dessert == "1" || $guest->guest_dessert == "on") {
    $icon = " <svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#cookie\"></use></svg>";
  } else {
   $icon = ""; 
  }
  
  if ($totalGuests > 0) {
    $output .= "<li>" . $memberObject->public_displayName() . " (" . $totalGuests . autoPluralise(" guest", " guests", $totalGuests) . ")" . $icon . "</li>";
  } else {
    $output .= "<li>" . $memberObject->public_displayName() . $icon . "</li>";
  }

  if ($totalGuests == $totalGuests) {
    $output .= "<ul>";

    foreach ($guestsArray AS $guest) {
      $guest = json_decode($guest);
      
      if ($guest->guest_dessert == "on") {
        $icon = " <svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#cookie\"></use></svg>";
      } else {
       $icon = ""; 
      }
      
      $output .= "<li>" . $guest->guest_name . $icon . "</li>";
    }
    $output .= "</ul>";
  }

  echo $output;
}
?>
</ul>
