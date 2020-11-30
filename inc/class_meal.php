<?php
class meal {
  protected static $table_name = "meals";

  public $uid;
  public $name;
  public $capacity;
  public $guests_allowed;
  public $date_meal;


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
    $bookingsClass = new bookings();
    $bookingsThisMeal = $bookingsClass->totalBookingsByMealUID($this->uid);

    $output  = "<div class=\"col\">";
    $output .= "<div class=\"card mb-4 shadow-sm\">";//border-warning mb-3
    $output .= "<div class=\"card-header\">";
    $output .= "<h4 class=\"my-0 font-weight-normal\">" . $this->name . "</h4>";
    $output .= "</div>";

    $output .= "<div class=\"card-body \">";
    $output .= "<h1 class=\"card-title pricing-card-title\">" . $bookingsThisMeal . " <small class=\"text-muted\">/ " . $this->capacity . " bookings</small></h1>";
    $output .= "<ul class=\"list-unstyled mt-3 mb-4\">";
    $output .= "<li>Wolfson Hall</li>";
    $output .= "<li>8am - 9.30am</li>";
    $output .= "<li>Collection from Wolfson Hall</li>";
    $output .= "</ul>";

    if ($bookingsClass->bookingExistCheck($this->uid, $_SESSION['username'])) {
      $output .= "<a href=\"index.php?n=booking&mealUID=" . $this->uid . "\" role=\"button\" class=\"btn btn-success\" id=\"mealUID-" . $this->uid . "\">Manage Booking</a>";
    } else {
      $output .= "<a href=\"#\" role=\"button\" class=\"btn btn-outline-primary\" id=\"mealUID-" . $this->uid . "\" onclick=\"bookMealQuick(this.id)\">Book Meal</a>";
    }

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

  public function termNumber() {
    return "??";
  }

  public function bookings_this_meal() {
    global $db;

    $sql  = "SELECT *  FROM bookings";
    $sql .= " WHERE meal_uid = '" . $this->uid . "'";

    $bookings = $db->query($sql)->fetchAll();

    return $bookings;
  }
}
?>
