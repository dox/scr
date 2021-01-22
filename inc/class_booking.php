<?php
class booking {
  protected static $table_name = "bookings";

  public $uid;
  public $type;
  public $date;
  public $meal_uid;
  public $member_ldap;
  public $guests_array; // json encoded array
  public $wine;
  public $dessert;

  function __construct($bookingUID = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $bookingUID . "'";

		$booking = $db->query($sql)->fetchArray();

		foreach ($booking AS $key => $value) {
			$this->$key = $value;
		}
  }

  public function guestsArray() {
    if (!empty($this->guests_array)) {
    	$guestsArray = json_decode($this->guests_array, true);
    } else {
	    $guestsArray = array();
    }
    return $guestsArray;
  }

  public function addGuest($newGuestArray = null) {
	  global $db;

    $guest_uid = "x" . bin2hex(random_bytes(5));
    $guest['guest_uid'] = $guest_uid;
    foreach ($newGuestArray AS $key => $value) {
      $guest[$key] = ($value);
    }

	  $sql  = "UPDATE " . self::$table_name;
	  $sql .= " SET guests_array = JSON_SET(COALESCE(guests_array, '{}'), '$." . $guest_uid . "', '" . json_encode($guest) . "')";
	  $sql .= " WHERE uid = '" . $this->uid . "' LIMIT 1";

    echo $sql;

	  $booking = $db->query($sql);
    $logsClass->create("booking", "Guest added to [bookingUID:" . $this->uid . "] for [mealUID:" . $this->meal_uid . "]");

	  return $this->guests_array;
  }

  public function create($array = null) {
    global $db;
    global $logsClass;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      $sqlColumns[] = $updateItem;
      $sqlValues[] = "'" . $value . "' ";
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);
    $logsClass->create("booking", "[bookingUID:" . $create->lastInsertID() . "] made for " . $_SESSION['username'] . " for [mealUID:" . $array['meal_uid'] . "]");

    return $create;
  }

  public function update($array = null) {
	 global $db;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'bookingUID') {
        $sqlUpdate[] = $updateItem ." = '" . $value . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);
    $logsClass->create("booking", "[bookingUID:" .  $this->uid  . "] updated by " . $_SESSION['username'] . " for [mealUID:" . $array['meal_uid'] . "]");

    return $update;
  }

  public function delete() {
    global $db;
    global $logsClass;

    $bookingUID = $this->uid;
    $mealUID = $this->meal_uid;

    $sql  = "DELETE FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $delete = $db->query($sql);
    $logsClass->create("booking", "[bookingUID:" .  $bookingUID  . "] deleted by " . $_SESSION['username'] . " for [mealUID:" . $mealUID . "]");

    return $delete;
  }

  public function deleteGuest($guest_uid = null) {
    global $db;
    global $logsClass;

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET guests_array = JSON_REMOVE(guests_array, '$." . $guest_uid . "')";
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $delete = $db->query($sql);
    $logsClass->create("booking", "Guest deleted from [bookingUID:" .  $this->uid  . "] for [mealUID:" . $this->meal_uid . "]");

    return $delete;
  }
}
?>
