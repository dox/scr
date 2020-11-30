<?php
class bookings extends booking {
  public function all() {

  }

  public function totalBookingsByMealUID($mealUID = null) {
    global $db;

    $bookings = $this->bookings_this_meal($mealUID);

    foreach ($bookings AS $booking) {
      $guestsArray[] = count($booking['guests_array']);
    }

    $totalGuests = count($bookings) + array_sum($guestsArray);

    return $totalGuests;
  }
}
?>
