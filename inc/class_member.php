<?php
class member {
  protected static $table_name = "members";

  public $uid;
  public $enabled;
  public $type;
  public $ldap;
  public $title;
  public $firstname;
  public $lastname;
  public $category;
  public $precedence;
  public $email;
  public $dietary;
  public $opt_in;
  public $default_domus;
  public $default_wine;
  public $default_dessert;
  public $date_lastlogon;

  function __construct($memberUID = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $memberUID . "'";
    $sql .= " OR ldap = '" . $memberUID . "'";

		$member = $db->query($sql)->fetchArray();

    if (empty($member)) {
      $member['uid'] = "0";
      $member['ldap'] = $memberUID;
      $member['title'] = "";
      $member['type'] = "Unknown";
      $member['firstname'] = "";
      $member['lastname'] = $memberUID;
      $member['precedence'] = "9999";
      $member['email'] = "no-reply@seh.ox.ac.uk";
      $member['dietary'] = "";
      $member['opt_in'] = "0";
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
    if ($_SESSION['admin'] == true) {
      $name = $this->displayName();
    } else {
      if ($this->opt_in == 1) {
        $name = $this->displayName();
      } else {
        $name = "HIDDEN";
      }
    }

    if ($this->ldap == $_SESSION['username']) {
      $name = $name . " <i>(You)</i>";
    }


    return $name;
  }

  public function isAdmin() {
    global $settingsClass;

    $arrayOfAdmins = explode(",", strtoupper($settingsClass->value('member_admins')));

    if (in_array(strtoupper($this->ldap), $arrayOfAdmins)) {
      return true;
		} else {
			return false;
		}
  }

  public function isSteward() {
    global $settingsClass;

    $arrayOfAdmins = explode(",", strtoupper($settingsClass->value('member_steward')));

    if (in_array(strtoupper($this->ldap), $arrayOfAdmins)) {
      return true;
		} else {
			return false;
		}
  }

  public function adminBadge() {
    if ($this->isAdmin()) {
      global $settingsClass;

      $badge = " <span class=\"badge bg-info\" >Administrator</span>";

      return $badge;
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
    global $db;
    global $logsClass;

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET precedence = '" . $order . "' ";
    $sql .= " WHERE uid = '" . $memberUID . "' ";
    $sql .= " LIMIT 1";

    $terms = $db->query($sql);

    return $terms;
  }

  public function create($array = null) {
	   global $db;
     global $logsClass;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'memberNew') {
        $sqlColumns[] = $updateItem;
        $sqlValues[] = "'" . $value . "' ";
      }
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);
    $logsClass->create("admin", "Member [memberUID:" . $create->lastInsertID() . "] created");

    return $create;
  }

  public function update($array = null) {
    global $db;
    global $logsClass;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'memberUID') {
        $sqlUpdate[] = $updateItem ." = '" . $value . "' ";
      }
      if (is_array($value)) {
        $sqlUpdate[] = $updateItem ." = '" . implode(",", $value) . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);
    $logsClass->create("members_update", "Member [memberUID:" . $array['memberUID'] . "] updated");

    return $update;
  }

  public function mealUIDS_upcoming() {
    global $db;

    $sql  = "SELECT * FROM bookings, meals";
    $sql .= " WHERE meals.date_meal >= NOW()";
    $sql .= " AND bookings.member_ldap = '" . $this->ldap . "'";
    $sql .= " AND bookings.meal_uid = meals.uid ";
    $sql .= " ORDER BY meals.date_meal DESC";

    $bookings = $db->query($sql)->fetchAll();

    foreach ($bookings AS $booking) {
      $bookingUIDSarray[] = $booking['meal_uid'];
    }

    return $bookingUIDSarray;
  }

  public function mealUIDS_previous() {
    global $db;

    $sql  = "SELECT * FROM bookings, meals";
    $sql .= " WHERE meals.date_meal < NOW()";
    $sql .= " AND bookings.member_ldap = '" . $this->ldap . "'";
    $sql .= " AND bookings.meal_uid = meals.uid ";
    $sql .= " ORDER BY meals.date_meal DESC";

    $bookings = $db->query($sql)->fetchAll();

    foreach ($bookings AS $booking) {
      $bookingUIDSarray[] = $booking['meal_uid'];
    }

    return $bookingUIDSarray;
  }

  public function count_allBookings($type = null) {
    global $db;

    $sql  = "SELECT count(*) AS total FROM bookings";
    $sql .= " INNER JOIN meals ON bookings.meal_uid = meals.uid";
    $sql .= " WHERE bookings.member_ldap = '" . $this->ldap . "'";

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
}
?>
