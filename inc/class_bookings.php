<?php
class bookings extends booking {
  public function all() {

  }

  public function totalBookingsByMealUID($mealUID = null) {
    global $db;

    $bookings = $this->bookingsByMealUID($mealUID);

    foreach ($bookings AS $booking) {
      $guestsArray[] = count(json_decode($booking['guests_array']));
    }

    $totalGuests = count($bookings) + array_sum($guestsArray);

    return $totalGuests;
  }

  public function bookingsByMealUID($mealUID = null) {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE meal_uid = '" . $mealUID . "'";
    $sql .= " ORDER BY uid ASC";

    $bookings = $db->query($sql)->fetchAll();

    return $bookings;
  }

  public function bookingForMealByMember($mealUID = null, $username = null) {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE meal_uid = '" . $mealUID . "'";
    $sql .= " AND member_ldap = '" . $username . "'";
    $sql .= " ORDER BY uid ASC";

    $bookingThisMeal = $db->query($sql)->fetchAll();

    return $bookingThisMeal[0];
  }

  public function bookingExistCheck($mealUID = null, $username = null) {
    $bookingsThisMeal = $this->bookingForMealByMember($mealUID, $username);

    if (isset($bookingsThisMeal['uid'])) {
      return true;
    } else {
      return false;
    }
  }
}
?>
