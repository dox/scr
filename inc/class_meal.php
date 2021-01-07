<?php
class meal {
  protected static $table_name = "meals";

  public $uid;
  public $name;
  public $date_meal;
  public $date_cutoff;
  public $location;
  public $scr_capacity;
  public $mcr_capacity;
  public $scr_guests;
  public $mcr_guests;
  public $menu;
  public $notes;


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
    global $icon_edit;



    $output  = "<div class=\"col\">";
    $output .= "<div class=\"card mb-4 shadow-sm\">";//border-warning mb-3
    $output .= "<div class=\"card-header\">";

    $output .= $this->menuTooltip();

    if ($_SESSION['admin'] == true) {
      $output .= "<a href=\"index.php?n=admin_meal&mealUID=" . $this->uid . "\" class=\"float-end\">";
      $output .= "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#sliders\"/></svg>";
      $output .= "</a>";
    }


    $output .= "<h4 class=\"my-0 font-weight-normal text-center\">" . $this->name . "</h4>";
    $output .= "</div>";

    $output .= "<div class=\"card-body \">";

    $output .= "<h1 class=\"card-title pricing-card-title\"><span id=\"capacityUID-" . $this->uid . "\">" . $this->total_bookings_this_meal() . "</span> <small class=\"text-muted\">/ " . $this->totalCapacity() . " bookings</small></h1>";
    $output .= "<ul class=\"list-unstyled mt-3 mb-4\">";
    $output .= "<li>" . $this->type . ", " . $this->location . "</li>";
    $output .= "<li>" . date('H:i', strtotime($this->date_meal)) . "</li>";

    if (isset($this->notes)) {
      $output .= "<li>" . $this->notes . "</li>";
    }

    $output .= "</ul>";

    $output .= $this->bookingButton();

    //$output .= "<div class=\"btn-group\">";
    //$output .= "<button type=\"button\" id=\"mealUID-" . $this->uid . "\" class=\"btn btn-outline-primary\" onclick=\"bookMealQuick(this.id)\">Book Meal</button>";
    //$output .= "<button type=\"button\" id=\"mealUID_dropdown-" . $this->uid . "\" class=\"btn btn-outline-primary dropdown-toggle dropdown-toggle-split\" data-toggle=\"dropdown\" aria-expanded=\"false\">";
    //$output .= "<span class=\"visually-hidden\">Toggle Dropdown</span>";
    //$output .= "</button>";
    //$output .= "<ul class=\"dropdown-menu\">";
    //$output .= "<li><a class=\"dropdown-item\" href=\"index.php?n=booking&mealUID=" . $this->uid . "\">Manage Booking</a></li>";
    //$output .= "<li><a class=\"dropdown-item\" href=\"index.php?n=admin_meal&mealUID=" . $this->uid . "\">Manage Meal</a></li>";
    //$output .= "<li><hr class=\"dropdown-divider\"></li>";
    //$output .= "<li><a class=\"dropdown-item\" href=\"#\">Cancel Booking</a></li>";
    //$output .= "</ul>";
    //$output .= "</div>";

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
      $bookingID = "mealUID-" . $this->uid;
      $bookingOnClick = "";
      $bookingDisplayText = "Manage Booking";
    } else {
      $bookingLink = "#";
      $bookingClass = "btn-outline-primary";
      $bookingID = "mealUID-" . $this->uid;
      $bookingOnClick = "onclick=\"bookMealQuick(this.id)\"";
      $bookingDisplayText = "Book Meal";

      // check for meal cutoff expiry date
      if (date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($this->date_cutoff))) {
        if ($_SESSION['admin'] == true) {
          $bookingLink = "#";
          $bookingClass = "btn-outline-secondary";
          $bookingID = "mealUID-" . $this->uid;
          $bookingOnClick = "onclick=\"bookMealQuick(this.id)\"";
          $bookingDisplayText = "Deadline Passed";
        } else {
          $bookingLink = "#";
          $bookingClass = "btn-outline-secondary disabled";
          $bookingID = "mealUID-" . $this->uid;
          $bookingOnClick = "";
          $bookingDisplayText = "Deadline Passed";
        }
      }

      // check for meal capacity
      if ($this->total_bookings_this_meal() >= $this->totalCapacity()) {
        if ($_SESSION['admin'] == true) {
          $bookingLink = "#";
          $bookingClass = "btn-outline-warning";
          $bookingID = "mealUID-" . $this->uid;
          $bookingOnClick = "onclick=\"bookMealQuick(this.id)\"";
          $bookingDisplayText = "Capacity Reached";
        } else {
          $bookingLink = "#";
          $bookingClass = "btn-outline-warning disabled";
          $bookingID = "mealUID-" . $this->uid;
          $bookingOnClick = "";
          $bookingDisplayText = "Capacity Reached";
        }
      }
    }

    $output = "<a href=\"" . $bookingLink . "\" role=\"button\" class=\"btn w-100 " . $bookingClass . "\" id=\"" . $bookingID . "\" " . $bookingOnClick . ">" . $bookingDisplayText . "</a>";

    return $output;
  }

  public function totalCapacity() {
    $scrCapacity = $this->scr_capacity;
    $mcrCapacity = $this->mcr_capacity;

    $totalCapacity = $scrCapacity + $mcrCapacity;

    return $totalCapacity;
  }

  public function termNumber() {
    return "??";
  }

  public function bookings_this_meal() {
    global $db;

    $sql  = "SELECT *  FROM bookings";
    $sql .= " LEFT JOIN members ON bookings.member_ldap = members.ldap";
    $sql .= " WHERE meal_uid = '" . $this->uid . "'";
    $sql .= " ORDER BY members.type DESC, members.precedence ASC, members.lastname ASC";

    $bookings = $db->query($sql)->fetchAll();

    return $bookings;
  }

  public function total_bookings_this_meal() {
    global $db;


    foreach ($this->bookings_this_meal() AS $booking) {
      $guestsArray[] = count(json_decode($booking['guests_array']));
    }


    $totalGuests = count($this->bookings_this_meal()) + array_sum($guestsArray);

    return $totalGuests;
  }

  public function menuTooltip() {
    if (!empty($this->menu)) {

      $output  = "<button type=\"button\" class=\"btn btn-sm btn-primary float-start\" data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\" data-bs-whatever=\"@mdo\">Menu</button>";

      $output  = "<button type=\"button\" class=\"btn btn-secondary\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Tooltip on top\">";
      $output .= "Menu";
      $output .= "</button>";

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
    global $db;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'mealUID') {
        $sqlUpdate[] = $updateItem ." = '" . $value . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);

    return $update;
  }

  public function create($array = null) {
	global $db;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'mealNEW') {
        $sqlColumns[] = $updateItem;
        $sqlValues[] = "'" . $value . "' ";
      }
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);

    return $create;
  }


}
?>
