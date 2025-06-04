<?php
class member {
  protected static $table_name = "members";

  public $uid;
  public $enabled;
  public $type;
  public $ldap;
  public $permissions;
  public $title;
  public $firstname;
  public $lastname;
  public $category;
  public $precedence;
  public $email;
  public $dietary;
  public $opt_in;
  public $email_reminders;
  public $default_wine; // retired
  public $default_wine_choice;
  public $default_dessert;
  public $date_lastlogon;
  public $calendar_hash;
  
  function __construct($memberUID = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $memberUID . "'";
    $sql .= " OR ldap = '" . $memberUID . "'";
    $sql .= " OR calendar_hash = '" . $memberUID . "'";

		$member = $db->query($sql)->fetchArray();

    if (empty($member)) {
      $member['uid'] = "0";
      $member['ldap'] = $memberUID;
      $member['title'] = "";
      $member['permissions'] = "";
      $member['type'] = "Unknown";
      $member['firstname'] = "";
      $member['lastname'] = $memberUID;
      $member['precedence'] = "9999";
      $member['email'] = "no-reply@seh.ox.ac.uk";
      $member['dietary'] = "";
      $member['opt_in'] = "0";
      $member['calendar_hash'] = "0";
    }

		foreach ($member AS $key => $value) {
			$this->$key = $value;
		}
  }

  public function displayName() {
    if (!empty($this->title)) {
      $title = $this->title . " ";
    }

    if (!empty($this->firstname)) {
      $firstname = $this->firstname . " ";
    }

    if (!empty($this->lastname)) {
      $lastname = $this->lastname;
    }

    $name = $title . $firstname . $lastname;

    return $name;
  }

  public function public_displayName() {
    if (checkpoint_charlie("members")) {
      $name = $this->displayName();
    } else {
      if ($this->opt_in == 1) {
        $name = $this->displayName();
      } else {
        $name  = "<div class=\"col-6\">";
        $name .= "<span class=\"placeholder col-1\"></span> ";
        $name .= "<span class=\"placeholder col-2\"></span> ";
        $name .= "<span class=\"placeholder col-3\"></span>";
        $name .= "</div>";
      }
    }

    if ($this->ldap == $_SESSION['username']) {
      $name = $this->displayName() . " <i>(You)</i>";
    }


    return $name;
  }

  public function isSteward() {
    global $settingsClass;

    $arrayofStewards = explode(",", strtoupper($settingsClass->value('member_steward')));

    if (in_array(strtoupper($this->ldap), $arrayofStewards)) {
      return true;
		} else {
			return false;
		}
  }

  public function stewardBadge() {
    if ($this->isSteward()) {
      global $settingsClass;

      $badge = " <span class=\"badge bg-warning\" >SCR Steward</span>";

      return $badge;
    }
  }

  public function updateMemberPrecendece($memberUID = null, $order = null) {
    global $db, $logsClass;

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET precedence = '" . $order . "' ";
    $sql .= " WHERE uid = '" . $memberUID . "' ";
    $sql .= " LIMIT 1";

    $terms = $db->query($sql);

    return $terms;
  }
  
  public function defaultWineChoice() {
    global $settingsClass;
    
    $wineOptions = explode(",", $settingsClass->value('booking_wine_options'));
    
    if (in_array($this->default_wine_choice, $wineOptions)) {
      return $this->default_wine_choice;
    } else {
      return end($wineOptions); // fallback to last option
    }
  }

  public function create($array = null, $displayAlert = true) {
	  global $db, $logsClass, $settingsClass;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'memberNew') {
        $sqlColumns[] = $updateItem;
        $sqlValues[] = "'" . escape($value) . "' ";
      }
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);
    if ($displayAlert == true) {
      echo $settingsClass->alert("success", "Success!", "Memer successfully created");
    }

    $logArray['category'] = "member";
    $logArray['result'] = "success";
    $logArray['description'] = "[memberUID:" . $create->lastInsertID() . "] created";
    $logsClass->create($logArray);

    return $create;
  }
  
  public function update($array) {
    global $db, $logsClass;
    
    // Initialize the set part of the query
    $setParts = [];
    
    //remove the memberUID
    unset($array['memberUID']);
    
    // Loop through the new values array
    foreach ($array as $field => $newValue) {
      if (is_array($newValue)) {
        $newValue = implode(",", $newValue);
      }
        // Check if the field exists in the current values and if the values are different
        if ($this->$field != $newValue) {
          
            // Sanitize the field and value to prevent SQL injection
            $field = escape($field);
            $newValue = escape($newValue);
            // Add to the set part
            $setParts[$field] = "`$field` = '$newValue'";
        }
    }
    
    // If there are no changes, return null
    if (empty($setParts)) {
        return null;
    }
    
    // Combine the set parts into a single string
    $setString = implode(", ", $setParts);
    
    // Construct the final UPDATE query
    $sql = "UPDATE " . self::$table_name . " SET " . $setString;
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";
    
    // check if username is changing, if so, update all meals booked under the old username and update to the new
    if (isset($setParts['ldap'])) {
      $sqlUpdate = "UPDATE bookings SET member_ldap = '" . $array['ldap'] . "' WHERE member_ldap = '" . $this->ldap . "'";
      
      $updateExistingBookings = $db->query($sqlUpdate);
      
      $logArray['category'] = "member";
      $logArray['result'] = "success";
      $logArray['description'] = "Updated existing meals from '" . $this->ldap . "' to '" . $array['ldap'] . "'";
      $logsClass->create($logArray);
    }
    
    $update = $db->query($sql);
    
    $logArray['category'] = "member";
    $logArray['result'] = "success";
    $logArray['description'] = "[memberUID:" . $this->uid . "] updated with fields " . $setString;
    $logsClass->create($logArray);
    
    return true;
  }
  
  public function updateLastLoginDate() {
    global $db;
    
    // Construct the final UPDATE query
    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET date_lastlogon = '" . date('c') . "'";
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";
    
    $update = $db->query($sql);
    
    return true;
  }

  public function bookingUIDS_upcoming() {
    global $db;
    
    $bookingUIDSarray = array();
    
    $sql  = "SELECT bookings.uid AS uid FROM bookings";
    $sql .= " LEFT JOIN meals ON bookings.meal_uid = meals.uid";
    $sql .= " WHERE meals.date_meal >= NOW()";
    $sql .= " AND bookings.member_ldap = '" . $this->ldap . "'";
    $sql .= " ORDER BY meals.date_meal DESC";

    $bookings = $db->query($sql)->fetchAll();

    foreach ($bookings AS $booking) {
      $bookingUIDSarray[] = $booking['uid'];
    }

    return $bookingUIDSarray;
  }
  
  public function bookingsByDay() {
    global $db;
    
    //mysql format
    $days = array(
        1 => 'Sunday',
        2 => 'Monday',
        3 => 'Tuesday',
        4 => 'Wednesday',
        5 => 'Thursday',
        6 => 'Friday',
        7 => 'Saturday'
    );
    
    $sql  = "SELECT DAYOFWEEK(meals.date_meal) AS dayofweek, count(*) AS totalMeals, meals.type";
    $sql .= " FROM bookings LEFT JOIN (meals) ON (bookings.meal_uid = meals.uid) ";
    $sql .= " WHERE member_ldap = '" . $this->ldap . "'";
    $sql .= " GROUP BY DAYOFWEEK(meals.date_meal), meals.type";
    $sql .= " ORDER BY DAYOFWEEK(meals.date_meal) ASC";
    
    $bookings = $db->query($sql)->fetchAll();
    
    $sql2  = "SELECT DISTINCT meals.type";
    $sql2 .= " FROM bookings LEFT JOIN (meals) ON (bookings.meal_uid = meals.uid) ";
    $sql2 .= " WHERE member_ldap = '" . $this->ldap . "'";

    $mealTypes = $db->query($sql)->fetchAll();
    
    foreach ($mealTypes AS $mealType) {      
      $returnArray[$mealType['type']]['Sunday'] = 0;
      $returnArray[$mealType['type']]['Monday'] = 0;
      $returnArray[$mealType['type']]['Tuesday'] = 0;
      $returnArray[$mealType['type']]['Wednesday'] = 0;
      $returnArray[$mealType['type']]['Thursday'] = 0;
      $returnArray[$mealType['type']]['Friday'] = 0;
      $returnArray[$mealType['type']]['Saturday'] = 0;
    }
    
    foreach ($bookings AS $booking) {
      $dayName = $days[$booking['dayofweek']];
      
      $returnArray[$booking['type']][$dayName] = $booking['totalMeals'];
    }
    
    return $returnArray;
  }

  public function bookingUIDS_previous() {
    global $db;
    
    $bookingUIDSarray = array();

    $sql  = "SELECT bookings.uid AS uid FROM bookings";
    $sql .= " LEFT JOIN meals ON bookings.meal_uid = meals.uid";
    $sql .= " WHERE meals.date_meal < NOW()";
    $sql .= " AND bookings.member_ldap = '" . $this->ldap . "'";
    $sql .= " ORDER BY meals.date_meal DESC";

    $bookings = $db->query($sql)->fetchAll();

    foreach ($bookings AS $booking) {
      $bookingUIDSarray[] = $booking['uid'];
    }

    return $bookingUIDSarray;
  }

  public function getAllBookingUIDS() {
    global $db;

    $sql  = "SELECT bookings.uid AS uid FROM bookings";
    $sql .= " LEFT JOIN meals ON bookings.meal_uid = meals.uid";
    $sql .= " WHERE bookings.member_ldap = '" . $this->ldap . "'";
    $sql .= " ORDER BY meals.date_meal DESC";

    $bookings = $db->query($sql)->fetchAll();

    foreach ($bookings AS $booking) {
      $bookingUIDSarray[] = $booking['uid'];
    }

    return $bookingUIDSarray;
  }
  
  public function countBookingsByType($date_range_days = null) {
    global $db;
  
    $sql  = "SELECT count(*) AS total, meals.type FROM bookings";
    $sql .= " INNER JOIN meals ON bookings.meal_uid = meals.uid";
    $sql .= " WHERE bookings.member_ldap = '" . $this->ldap . "'";
    
    if ($date_range_days != null) {
      $sql .= " AND meals.date_meal > DATE_SUB(NOW(), INTERVAL " . $date_range_days . " DAY)";
    }
    
    $sql .= " GROUP BY meals.type";
    
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }

  public function count_allBookings($type = null, $date_range_days = null) {
    global $db;

    $sql  = "SELECT count(*) AS total FROM bookings";
    $sql .= " INNER JOIN meals ON bookings.meal_uid = meals.uid";
    $sql .= " WHERE bookings.member_ldap = '" . $this->ldap . "'";
    
    if ($date_range_days != null) {
      $sql .= " AND meals.date_meal > DATE_SUB(NOW(), INTERVAL " . $date_range_days . " DAY)";
    }

    if ($type != null) {
      $sql .= " AND meals.type = '" . $type . "'";
    }
    
    $totalMeals = $db->query($sql)->fetchAll()[0]['total'];

    return $totalMeals;
  }

  public function count_allGuests() {
    global $db;

    $sql  = "SELECT * FROM bookings";
    $sql .= " WHERE member_ldap = '" . $this->ldap . "'";
    $sql .= " AND guests_array IS NOT NULL";

    $bookings = $db->query($sql)->fetchAll();

    foreach ($bookings AS $booking) {
      $guestsArray[] = count(json_decode($booking['guests_array']));
    }

    return array_sum($guestsArray);
  }
  
  public function delete() {
    global $db, $logsClass
    ;
    // warning - deletes a member and all their bookings!
    $memberUID = $this->uid;
    $memberName = $this->displayName();
    
    $sqlMember  = "DELETE FROM members";
    $sqlMember .= " WHERE uid = '" . $this->uid . "'";
    $sqlMember .= " LIMIT 1";

    $sqlBookings  = "DELETE FROM bookings";
    $sqlBookings .= " WHERE member_ldap = '" . $this->ldap . "'";
    
    $deleteMember   = $db->query($sqlMember);
    $deleteBookings = $db->query($sqlBookings);
    
    $logArray['category'] = "member";
    $logArray['result'] = "danger";
    $logArray['description'] = "Deleted  " . $memberName . " [memberUID:" . $memberUID . "] and all associated bookings";
    $logsClass->create($logArray);
    
    return true;
  }
  
  public function memberRow() {
    global $settingsClass;
    
    $scrStewardLDAP = $settingsClass->value('member_steward');
    
    if ($this->type == "SCR" && $this->enabled == "1") {
      $handle  = "<svg width=\"1em\" height=\"1em\" class=\"handle\"><use xlink:href=\"img/icons.svg#grip-vertical\"/></svg>";
    } else {
      $handle = "";
    }
    
    $output  = "<li class=\"list-group-item\" id=\"" . $this->uid . "\">";
    $output .= $handle;
    $output .= "<a href=\"index.php?n=member&memberUID=" . $this->uid . "\">" . $this->displayName() . "</a>";
    $output .= " <span class=\"text-muted\">[" . $this->ldap ."]</span>";
    
    $output .= "<span class=\"float-end\">";
    $output .= $this->stewardBadge() ." ";
    
    //$output .= $this->adminBadge() ." ";
    
    $output .= "<span class=\"text-muted\">" . $this->category . "</span>";
    
    $output .= "</span>";
    $output .= "</li>";
    
    return $output;
  }
}
?>
