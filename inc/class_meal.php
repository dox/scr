<?php
class meal {
  protected static $table_name = "meals";

  public $uid;
  public $template;
  public $name;
  public $type;
  public $date_meal;
  public $date_cutoff;
  public $location;
  public $domus;
  public $allowed_wine;
  public $allowed_dessert;
  public $scr_capacity;
  public $mcr_capacity;
  public $scr_guests;
  public $mcr_guests;
  public $scr_dessert_capacity;
  public $mcr_dessert_capacity;
  public $menu;
  public $notes;
  public $photo;


  function __construct($mealUID = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $mealUID . "'";

		$meal = $db->query($sql)->fetchArray();

		foreach ($meal AS $key => $value) {
			$this->$key = $value;
		}
  }

  public function mealCard() {
    $mealURL = "index.php?n=admin_meal&mealUID=" . $this->uid;

    $output  = "<div class=\"col-12 mb-3\">";
    $output .= "<div class=\"blog-card\">";
    $output .= "<div class=\"card border-light\">";

    if (!empty($this->photo)) {
      $imageURL = "../../img/cards/" . $this->photo;
      if(file_exists($imageURL)) {
        $output .= "<img src=\"" . $imageURL . "\" class=\"card-img-top rounded-top\" alt=\"Meal Image\">";
      }
    }

    $output .= "<div class=\"card-body text-center\">";

    $output .= $this->menuTooltip();

    if ($_SESSION['admin'] == true) {
      $output .= "<h2 class=\"h5\"><a href=\"" . $mealURL . "\">" . $this->name . "</a></h2>";
    } else {
      $output .= "<h2 class=\"h5\">" . $this->name . "</h2>";
    }

    $output .= "<p class=\"card-text my-2\">";

    $output .= "<ul class=\"list-unstyled\">";
    $output .= "<li>" . $this->type . ", " . $this->location . "</li>";
    $output .= "<li>" . timeDisplay($this->date_meal) . "</li>";

    if (!empty($this->notes)) {
      $output .= "<li>" . $this->notes . "</li>";
    }

    $output .= "</ul>";

    $output .= "</p>";

    if ($this->total_bookings_this_meal('SCR') > 0 || $this->totalCapacity('SCR') > 0) {
      $scrCapacity = "(SCR " . $this->total_bookings_this_meal('SCR') . "/" . $this->totalCapacity('SCR') . ")";
    }
    if ($this->total_bookings_this_meal('MCR') > 0 || $this->totalCapacity('MCR') > 0) {
      $mcrCapacity = "(MCR " . $this->total_bookings_this_meal('MCR') . "/" . $this->totalCapacity('MCR') . ")";
    }
    $output .= "<small class=\"d-block\">Bookings: " . $scrCapacity . "  " .  $mcrCapacity . "</small>";

    $output .= "</div>";
    $output .= "<div class=\"card-footer bg-white border-0 px-0 py-0\">";
    $output .= $this->bookingButton();
    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";

    return $output;
  }

  private function bookingButton() {
    $bookingsClass = new bookings();
    $bookingsThisMeal = $this->total_bookings_this_meal();

    if ($bookingsClass->bookingExistCheck($this->uid, $_SESSION['username'])) {
      // Booking exists for users - show the manage button
      $bookingLink = "index.php?n=booking&mealUID=" . $this->uid;
      $bookingClass = "btn-success";
      $bookingOnClick = "";
      $bookingDisplayText = "Manage Booking";
    } else {
      $bookingLink = "#";
      $bookingClass = "btn-primary";
      $bookingOnClick = "onclick=\"bookMealQuick(this.id)\"";
      $bookingDisplayText = "Book Meal";

      // check for meal capacity
      if ($this->check_capacity_ok()) {
        // meal capacity fine
      } else {
        // meal capacity reached
        if ($_SESSION['admin'] == true) {
          // allow admin override
          $bookingLink = "#";
          $bookingClass = "btn-warning";
          $bookingOnClick = "onclick=\"bookMealQuick(this.id)\"";
          $bookingDisplayText = "Capacity Reached";
        } else {
          // prevent users from booking
          $bookingLink = "#";
          $bookingClass = "btn-warning disabled";
          $bookingOnClick = "";
          $bookingDisplayText = "Capacity Reached";
        }
      }

      // check for meal cutoff expiry date
      if ($this->check_cutoff_ok()) {
        // meal cutoff fine
      } else {
        // meal cutoff passed
        if ($_SESSION['admin'] == true) {
          // allow admin override
          $bookingLink = "#";
          $bookingClass = "btn-secondary";
          $bookingOnClick = "onclick=\"bookMealQuick(this.id)\"";
          $bookingDisplayText = "Deadline Passed";
        } else {
          // prevent users from booking
          $bookingLink = "#";
          $bookingClass = "btn-secondary disabled";
          $bookingOnClick = "";
          $bookingDisplayText = "Deadline Passed";
        }
      }

      // check for member status (enabled/disabled)
      if ($this->check_cutoff_ok()) {
        // meal cutoff fine
      } else {
        // meal cutoff passed
        if ($_SESSION['admin'] == true) {
          // allow admin override
          $bookingLink = "#";
          $bookingClass = "btn-secondary";
          $bookingOnClick = "onclick=\"bookMealQuick(this.id)\"";
          $bookingDisplayText = "Deadline Passed";
        } else {
          // prevent users from booking
          $bookingLink = "#";
          $bookingClass = "btn-secondary disabled";
          $bookingOnClick = "";
          $bookingDisplayText = "Deadline Passed";
        }
      }

      // check for member disabled
      if ($this->check_member_ok()) {
        // member status fine
      } else {
        // member disabled
        $bookingLink = "#";
        $bookingClass = "btn-secondary";
        $bookingOnClick = "";
        $bookingDisplayText = "Your account is disabled";
      }
    }

    $output .= "<div class=\"d-grid\">";
    $output .= "<a class=\"btn btn-sm " . $bookingClass . " rounded-0 rounded-bottom\" href=\"" . $bookingLink . "\" id=\"mealUID-" . $this->uid . "\" " . $bookingOnClick . " >";
    $output .= $bookingDisplayText;
    $output .= "</a>";
    $output .= "</div>";

    //$output = "<a href=\"" . $bookingLink . "\" role=\"button\" class=\"btn w-100 " . $bookingClass . "\" id=\"" . $bookingID . "\" " . $bookingOnClick . ">" . $bookingDisplayText . "</a>";

    return $output;
  }

  public function totalCapacity($memberType = null) {
    if ($memberType == "SCR") {
      $totalCapacity = $this->scr_capacity;
    } elseif ($memberType == "MCR") {
      $totalCapacity = $this->mcr_capacity;
    } else {
      $totalCapacity = $this->scr_capacity + $this->mcr_capacity;
    }

    return $totalCapacity;
  }

  public function termNumber() {
    return "??";
  }

  public function bookings_this_meal() {
    global $db;

    $sql  = "SELECT * FROM bookings";
    $sql .= " LEFT JOIN members ON bookings.member_ldap = members.ldap";
    $sql .= " WHERE meal_uid = '" . $this->uid . "'";
    $sql .= " ORDER BY members.type DESC, members.precedence ASC, members.lastname ASC";

    /*
    $sql  = "SELECT * FROM bookings";
    $sql .= " WHERE meal_uid = '" . $this->uid . "'";
    $sql .= " ORDER BY date ASC";
    */

    $bookings = $db->query($sql)->fetchAll();

    return $bookings;
  }

  public function total_bookings_this_meal($memberType = null) {
    global $db;

    if ($memberType == "SCR") {
      $sql  = "SELECT JSON_LENGTH(guests_array) AS totalGuestsPerBooking FROM bookings";
      $sql .= " WHERE meal_uid = '" . $this->uid . "'";
      $sql .= " AND type = 'SCR'";
    } elseif ($memberType == "MCR") {
      $sql  = "SELECT JSON_LENGTH(guests_array) AS totalGuestsPerBooking FROM bookings";
      $sql .= " WHERE meal_uid = '" . $this->uid . "'";
      $sql .= " AND type = 'MCR'";
    } else {
      $sql  = "SELECT JSON_LENGTH(guests_array) AS totalGuestsPerBooking FROM bookings";
      $sql .= " WHERE meal_uid = '" . $this->uid . "'";
    }

    $sql2 = "SELECT count(*) as totalBookings, SUM(x.totalGuestsPerBooking) AS totalGuests FROM (" . $sql . ") AS x";


    $bookings = $db->query($sql2)->fetchAll();
    //printArray($bookings);

    $memberBookings = $bookings[0]['totalBookings'];
    $guestBookings = $bookings[0]['totalGuests'];

    $totalGuests = $memberBookings + $guestBookings;

    return $totalGuests;
  }

  public function menuTooltip() {
    if (!empty($this->menu)) {

      $output  = "<a href=\"#\" class=\"float-start\" id=\"menuUID-" . $this->uid . "\" data-bs-toggle=\"modal\" data-bs-target=\"#menuModal\" onclick=\"displayMenu(this.id)\">";
      $output .= "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#info-circle\"/></svg>";
      $output .= "</a>";
    }

    return $output;
  }

  public function display_mealAside() {
    global $settingsClass;

    if (date('Y-m-d', strtotime($this->date_meal)) == date('Y-m-d')) {
      $class = "text-success";
    } else {
      $class = "text-muted";
    }

    $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
    $output .= "<div class=\"" . $class . "\">";
    $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=booking&mealUID=" . $this->uid . "\" class=\"" . $class . "\">" . $this->name . "</a></h6>";
    $output .= "<small class=\"" . $class . "\">" . $this->location . "</small>";
    $output .= "</div>";
    $output .= "<span class=\"" . $class . "\">" . dateDisplay($this->date_meal) . "</span>";
    $output .= "</li>";

    return $output;
  }

  public function update($array = null) {
    global $db, $logsClass, $settingsClass;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'mealUID' && $value != '<p><br></p>') {
        $value = str_replace("'", "\'", $value);
        $sqlUpdate[] = $updateItem ." = '" . $value . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);
    echo $settingsClass->alert("success", "Success!", "Meal successfully updated");
    $logsClass->create("meal", "[mealUID:" . $this->uid . "] updated");

    return $update;
  }

  public function create($array = null) {
    global $db, $logsClass, $settingsClass;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'mealNEW' && $value != '<p><br></p>') {
        $sqlColumns[] = $updateItem;
        $sqlValues[] = "'" . $value . "' ";
      }
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);
    echo $settingsClass->alert("success", "Success!", "Meal successfully created");
    $logsClass->create("meal", "[mealUID:" . $create->lastInsertID() . "] created");

    return $create;
  }

  public function delete() {
    global $db, $logsClass, $settingsClass;

    $mealUID = $this->uid;

    // delete all bookings
    $sql  = "DELETE FROM bookings";
    $sql .= " WHERE meal_uid = '" . $mealUID . "' ";

    $deleteBookings = $db->query($sql);

    $sql  = "DELETE FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $mealUID . "' ";
    $sql .= " LIMIT 1";

    $deleteMeal = $db->query($sql);
    echo $settingsClass->alert("success", "Success!", "Meal successfully deleted");
    $logsClass->create("meal", "[mealUID:" . $mealUID . "] (and any associated bookings) deleted");

    return $deleteMeal;
  }

  public function check_capacity_ok() {
    $mealCapacity = $this->totalCapacity();
    $currentBookingsCount = $this->total_bookings_this_meal();

    if ($currentBookingsCount < $mealCapacity) {
      $capacityStatus = true;
    } else {
      $capacityStatus = false;
    }

    return $capacityStatus;
  }

  public function check_cutoff_ok() {
    $mealCutoffDateTime = date('Y-m-d H:i:s', strtotime($this->date_cutoff));
    $nowDateTime = date('Y-m-d H:i:s');

    if ($nowDateTime < $mealCutoffDateTime) {
      $cutoffStatus = true;
    } else {
      $cutoffStatus = false;
    }

    return $cutoffStatus;
  }

  public function check_member_ok() {
    $currentMemberStatus = $_SESSION['enabled'];

    if ($currentMemberStatus == 1) {
      $memberStatus = true;
    } else {
      $memberStatus = false;
    }

    return $memberStatus;
  }

  public function check_meal_bookable() {
    $check_capacity = $this->check_capacity_ok();
    $check_cutoff   = $this->check_cutoff_ok();
    $check_member   = $this->check_member_ok();

    if ($check_capacity && $check_cutoff && $check_member) {
      return true;
    } else {
      return false;
    }
  }
}
?>
