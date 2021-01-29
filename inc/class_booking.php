<?php
class booking {
  protected static $table_name = "bookings";

  public $uid;
  public $type;
  public $date;
  public $meal_uid;
  public $member_ldap;
  public $guests_array; // json encoded array
  public $domus;
  public $domus_reason;
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
	  global $db, $logsClass;

    $guest_uid = "x" . bin2hex(random_bytes(5));
    $guest['guest_uid'] = $guest_uid;
    foreach ($newGuestArray AS $key => $value) {
      $guest[$key] = ($value);
    }

	  $sql  = "UPDATE " . self::$table_name;
	  $sql .= " SET guests_array = JSON_SET(COALESCE(guests_array, '{}'), '$." . $guest_uid . "', '" . json_encode($guest) . "')";
	  $sql .= " WHERE uid = '" . $this->uid . "' LIMIT 1";

	  $booking = $db->query($sql);
    $logsClass->create("booking", "Guest added to [bookingUID:" . $this->uid . "] for [mealUID:" . $this->meal_uid . "]");

	  return $this->guests_array;
  }

  public function create($array = null) {
    global $db, $logsClass;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      $sqlColumns[] = $updateItem;
      $sqlValues[] = "'" . $value . "' ";
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);
//    echo $settingsClass->alert("success", "Success!". "Booking successfully created");
    $logsClass->create("booking", "[bookingUID:" . $create->lastInsertID() . "] made for " . $_SESSION['username'] . " for [mealUID:" . $array['meal_uid'] . "]");

    return $create;
  }

  public function update($array = null) {
    global $db, $logsClass, $settingsClass;

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
    echo $settingsClass->alert("success", "Success!", "Booking successfully updated");
    $logsClass->create("booking", "[bookingUID:" .  $this->uid  . "] updated by " . $_SESSION['username'] . " for [mealUID:" . $array['meal_uid'] . "]");

    return $update;
  }

  public function delete() {
    global $db, $logsClass, $settingsClass;

    $bookingUID = $this->uid;
    $mealUID = $this->meal_uid;

    $sql  = "DELETE FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $delete = $db->query($sql);
    $logsClass->create("booking", "[bookingUID:" .  $bookingUID  . "] deleted by " . $_SESSION['username'] . " for [mealUID:" . $mealUID . "]");
    echo $settingsClass->alert("success", "Success!", "Booking successfully deleted");

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

  public function display_aside() {
    global $settingsClass;

    $meal = new meal($this->meal_uid);

    if (date('Y-m-d', strtotime($meal->date_meal)) == date('Y-m-d')) {
      $class = "text-success";
    } else {
      $class = "text-muted";
    }

    if ($_SESSION['username'] == $this->member_ldap) {
      $url = "index.php?n=booking&mealUID=" . $meal->uid;
    } else {
      $url = "index.php?n=admin_meal&mealUID=" . $meal->uid;
    }

    $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
    $output .= "<div class=\"" . $class . "\">";
    $output .= "<h6 class=\"my-0\"><a href=\"" . $url . "\" class=\"" . $class . "\">" . $meal->name . "</a></h6>";
    $output .= "<small class=\"" . $class . "\">" . $meal->location . "</small>";
    $output .= "</div>";
    $output .= "<span class=\"" . $class . "\">" . dateDisplay($meal->date_meal) . "</span>";
    $output .= "</li>";

    return $output;
  }
}
?>
