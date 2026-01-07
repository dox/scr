<?php
class bookings extends booking {
  public function all() {

  }

  public function bookingsUIDsByMealUID($mealUID = null) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE meal_uid = '" . $mealUID . "'";
    $sql .= " ORDER BY uid ASC";

    $sql  = "SELECT bookings.uid AS uid FROM " . self::$table_name;
    $sql .= " LEFT JOIN members ON bookings.member_ldap = members.ldap";
    $sql .= " WHERE meal_uid = '" . $mealUID . "'";
    $sql .= " ORDER BY members.type DESC, -members.precedence DESC, members.lastname ASC";

    $bookings = $db->query($sql)->fetchAll();

    return $bookings;
  }

  public function bookingForMealByMember($mealUID = null, $username = null) {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE meal_uid = '" . $mealUID . "'";
    $sql .= " AND member_ldap = '" . $username . "'";
    $sql .= " LIMIT 1";

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

  public function booking_uids_future_by_member($memberldap = null) {
    global $db;

    $sql  = "SELECT bookings.uid AS uid FROM " . self::$table_name;
    $sql .= " LEFT JOIN meals ON bookings.meal_uid = meals.uid";
    $sql .= " WHERE meals.date_meal > NOW()";
    $sql .= " AND member_ldap = '" . $memberldap . "'";
    $sql .= " ORDER BY meals.date_meal ASC";

    $bookings = $db->query($sql)->fetchAll();

    return $bookings;
  }

  public function booking_uids_past_by_member($memberldap = null) {
    global $db;

    $sql  = "SELECT bookings.uid AS uid FROM " . self::$table_name;
    $sql .= " LEFT JOIN meals ON bookings.meal_uid = meals.uid";
    $sql .= " WHERE meals.date_meal < NOW()";
    $sql .= " AND member_ldap = '" . $memberldap . "'";
    $sql .= " ORDER BY meals.date_meal ASC";

    $bookings = $db->query($sql)->fetchAll();

    return $bookings;
  }
}
?>
