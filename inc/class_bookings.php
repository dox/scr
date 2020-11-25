<?php
class bookings extends booking {
  public function all() {

  }

  public function totalBookingsByMealUID($mealUID = null) {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE mealUID = '" . $mealUID . "'";
    $sql .= " ORDER BY uid ASC";

    $bookings = $db->query($sql)->fetchAll();

    foreach ($bookings AS $booking) {
      $guestsArray[] = count($booking['guests_array']);
    }

    $totalGuests = count($bookings) + array_sum($guestsArray);

    return $totalGuests;
  }
}
?>
