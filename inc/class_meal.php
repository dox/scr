<?php
class meal {
  protected static $table_name = "meals";

  public $uid;
  public $name;
  public $date_meal;
  public $location;
  public $scr_capacity;
  public $mcr_capacity;
  public $scr_guests;
  public $mcr_guests;
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
    $bookingsClass = new bookings();
    $bookingsThisMeal = $bookingsClass->totalBookingsByMealUID($this->uid);

    $output  = "<div class=\"col\">";
    $output .= "<div class=\"card mb-4 shadow-sm\">";//border-warning mb-3
    $output .= "<div class=\"card-header\">";
    $output .= "<h4 class=\"my-0 font-weight-normal text-center\">" . $this->name . "</h4>";

    $output .= "<a href=\"index.php?n=admin_meal&mealUID=" . $this->uid . "\" class=\"ml-auto\">";
    $output .= "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path><path d=\"M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z\"></path><circle cx=\"12\" cy=\"12\" r=\"3\"></circle></svg>";
    $output .= "</a>";

    $output .= "</div>";

    $output .= "<div class=\"card-body \">";
    $output .= "<h1 class=\"card-title pricing-card-title\">" . $bookingsThisMeal . " <small class=\"text-muted\">/ " . $this->totalCapacity() . " bookings</small></h1>";
    $output .= "<ul class=\"list-unstyled mt-3 mb-4\">";
    $output .= "<li>" . $this->location . "</li>";
    $output .= "<li>" . date('H:i', strtotime($this->date_meal)) . "</li>";

    if (isset($this->notes)) {
      $output .= "<li>" . $this->notes . "</li>";
    }

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

    $sql  = "SELECT *  FROM bookings, members";
    $sql .= " WHERE bookings.member_ldap = members.ldap";
    $sql .= " AND meal_uid = '" . $this->uid . "'";
    $sql .= " ORDER BY members.precedence ASC";

    $bookings = $db->query($sql)->fetchAll();

    return $bookings;
  }
}
?>
