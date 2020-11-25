<?php
class booking {
  protected static $table_name = "bookings";

  public $uid;
  public $date;
  public $mealUID;
  public $memberUID;
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
	  
	  $allGuests = $this->guestsArray();
	  array_push($allGuests, $newGuestArray);
	  $allGuests = json_encode($allGuests);
	  
	  $sql  = "UPDATE " . self::$table_name;
	  $sql .= " SET guests_array = '" . $allGuests . "' ";
	  $sql .= " WHERE uid = '" . $this->uid . "' LIMIT 1";
	  
	  $booking = $db->query($sql);
	  
	  return $this->guests_array;
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

    return $update;
  }
}
?>
