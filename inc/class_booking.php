<?php
class booking {
  protected static $table_name = "bookings";

  public $uid;
  public $type;
  public $date;
  public $meal_uid;
  public $member_ldap;
  public $guests_array; // json encoded array
  public $charge_to;
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
      // check for htmlspecialchars = but not for dietary info!
      if ($key == "guest_dietary") {
        $guest[$key] = $value;        
      } else {
        $guest[$key] = htmlspecialchars($value, ENT_QUOTES);
      }
    }

	  $sql  = "UPDATE " . self::$table_name;
	  $sql .= " SET guests_array = JSON_SET(COALESCE(guests_array, '{}'), '$." . $guest_uid . "', '" . json_encode($guest, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . "')";
	  $sql .= " WHERE uid = '" . $this->uid . "' LIMIT 1";

	  $booking = $db->query($sql);

    $logArray['category'] = "booking";
    $logArray['result'] = "success";
    $logArray['description'] = "Guest '" . $newGuestArray['guest_name'] . "' added to [bookingUID:" . $this->uid . "] for [mealUID:" . $this->meal_uid . "]. Wine: " . $newGuestArray['guest_wine'];
    $logsClass->create($logArray);

	  return $this->guests_array;
  }

  public function updateGuest($newGuestArray = null) {
	  global $db, $logsClass;

    foreach ($newGuestArray AS $key => $value) {
      $guest[$key] = ($value);
    }

    $sql  = "UPDATE bookings ";
    $sql .= "SET guests_array = JSON_SET(guests_array, '$." . $guest['guest_uid'] . "', '" . json_encode($guest, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . "') ";
    $sql .= "WHERE uid = " . $this->uid . " ";
    $sql .= "LIMIT 1";

    echo $sql;

	  $booking = $db->query($sql);

    $logArray['category'] = "booking";
    $logArray['result'] = "success";
    $logArray['description'] = "Guest updated for [bookingUID:" . $this->uid . "] for [mealUID:" . $this->meal_uid . "]";
    $logArray['description'] = $sql;
    $logsClass->create($logArray);

	  return $this->guests_array;
  }

  public function create($array = null) {
    global $db, $logsClass;
    
    $bookingsCheck = new bookings();
    
    // check if already booked onto this meal
    if ($bookingsCheck->bookingExistCheck($array['meal_uid'], $array['member_ldap'])) {
      $logArray['category'] = "booking";
      $logArray['result'] = "warning";
      $logArray['description'] = $array['member_ldap'] . " attempted to book twice for [mealUID:" . $array['meal_uid'] . "]";
      $logsClass->create($logArray);
      
      return false;
    } else {
      $sql  = "INSERT INTO " . self::$table_name;
      
      foreach ($array AS $updateItem => $value) {
        $sqlColumns[] = $updateItem;
        $sqlValues[] = "'" . $value . "' ";
      }
      
      $sql .= " (" . implode(",", $sqlColumns) . ") ";
      $sql .= " VALUES (" . implode(",", $sqlValues) . ")";
      
      $create = $db->query($sql);
      
      $logArray['category'] = "booking";
      $logArray['result'] = "success";
      $logArray['description'] = "[bookingUID:" . $create->lastInsertID() . "] made for " . $_SESSION['username'] . " for [mealUID:" . $array['meal_uid'] . "].  Dessert: " . $array['dessert'] . " Wine: " . $array['wine'];
      $logsClass->create($logArray);
      
      return $create;
    }
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

    $logArray['category'] = "booking";
    $logArray['result'] = "success";
    $logArray['description'] = "[bookingUID:" .  $this->uid  . "] updated by " . $_SESSION['username'] . " for [mealUID:" . $this->meal_uid . "]";
    $logsClass->create($logArray);

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
    echo $settingsClass->alert("success", "Success!", "Booking successfully deleted");

    $logArray['category'] = "booking";
    $logArray['result'] = "success";
    $logArray['description'] = "[bookingUID:" .  $bookingUID  . "] deleted by " . $_SESSION['username'] . " for [mealUID:" . $mealUID . "]";
    $logsClass->create($logArray);

    return $delete;
  }

  public function deleteGuest($guest_uid = null) {
    global $db;
    global $logsClass;

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET guests_array = JSON_REMOVE(guests_array, '$." . $guest_uid . "')";
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";
    
    echo $sql;

    $delete = $db->query($sql);

    $logArray['category'] = "booking";
    $logArray['result'] = "success";
    $logArray['description'] = "Guest deleted from [bookingUID:" .  $this->uid  . "] for [mealUID:" . $this->meal_uid . "]";
    $logsClass->create($logArray);

    return $delete;
  }

  public function displayListGroupItem() {
    global $settingsClass;

    $meal = new meal($this->meal_uid);

    if (date('Y-m-d', strtotime($meal->date_meal)) == date('Y-m-d')) {
      $class = "text-success";
    } else {
      $class = "text-muted";
    }

    $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
    $output .= "<div class=\"" . $class . " d-inline-block text-truncate\" style=\"max-width: 73%;\">";
    $output .= "<h6 class=\"my-0\">";

    // if admin, link to the meal itself, otherwise, link to the booking for the user
    if (checkpoint_charlie("meals")) {
      $output .= "<a href=\"index.php?n=admin_meal&mealUID=" . $meal->uid . "\" class=\"" . $class . "\">" . $meal->name . "</a>";
    } else {
      $output .= "<a href=\"index.php?n=booking&mealUID=" . $meal->uid . "\" class=\"" . $class . "\">" . $meal->name . "</a>";
    }

    $output .= "</h6>";
    $output .= "<small class=\"" . $class . "\">" . $meal->location . "</small>";
    $output .= "</div>";
    $output .= "<span class=\"" . $class . "\">" . dateDisplay($meal->date_meal) . "</span>";
    $output .= "</li>";

    return $output;
  }
}
?>
