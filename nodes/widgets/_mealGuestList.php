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
  
  $bookingObject = new booking($booking['uid']);
  $guestBookingObject = new booking($booking['uid']);
  $memberObject = new member($guestBookingObject->member_ldap);
  
  $guestsArray = $guestBookingObject->guestsArray();
  $totalGuests = count($guestsArray);
  
  if ($bookingObject->wineChoice() != "None") {
    $wineIcon = " <svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#wine-glass\"></use></svg>";
  } else {
   $wineIcon = ""; 
  }
  
  if ($bookingObject->dessert == "1") {
    $icon = " <svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#cookie\"></use></svg>";
  } else {
   $icon = ""; 
  }
  
  if ($totalGuests > 0) {
    $output .= "<li>" . $memberObject->public_displayName() . " (" . $totalGuests . autoPluralise(" guest", " guests", $totalGuests) . ") " . $wineIcon . $icon . "</li>";
  } else {
    $output .= "<li>" . $memberObject->public_displayName() . $wineIcon . $icon . "</li>";
  }

  if ($totalGuests == $totalGuests) {
    $output .= "<ul>";

    foreach ($guestsArray AS $guest) {
      $guest = json_decode($guest);
      
      if ($guest->guest_wine_choice != "None" && $guest->guest_wine_choice != "") {
        $wineIcon = " <svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#wine-glass\"></use></svg>";
      } else {
       $wineIcon = ""; 
      }
      
      if ($bookingObject->dessert == "1") {
        $icon = " <svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#cookie\"></use></svg>";
      } else {
       $icon = ""; 
      }
      
      if ($memberObject->opt_in == 1 || checkpoint_charlie("members,meals")) {
        $guestName = $guest->guest_name;
      } else {
        if ($memberObject->ldap == $_SESSION['username']) {
          $guestName = $guest->guest_name;
        } else {
          $guestName = $memberObject->public_displayName();
        }
      }
      $output .= "<li>" . htmlspecialchars_decode($guestName) . " " . $wineIcon . $icon . "</li>";
    }
    $output .= "</ul>";
  }

  echo $output;
}
?>
</ul>
