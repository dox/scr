<?php
// CSV columns to include
$columns = array(
  "booking_uid",
  "booking_date",
  "booking_charge_to",
  "booking_domus_reason",
  "booking_wine",
  "booking_wine_choice",
  "booking_dessert",
  "booking_guests",
  "meal_name",
  "meal_default_charge_to",
  "meal_notes",
  "meal_date",
  "meal_time",
  "member_diner_type", //member/guest
  "member_type",
  "member_ldap",
  "member_name",
  "member_category"
);

//get all meals in the previous month
$sqlMeals  = "SELECT uid FROM meals WHERE date_meal >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, '%Y/%m/01' ) AND date_meal < DATE_FORMAT( CURRENT_DATE, '%Y/%m/01' ) ORDER BY `meals`.`date_meal`  ASC";

//get all meals in THIS month
//$sqlMeals  = "SELECT uid FROM meals WHERE date_meal >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, '%Y/%m/01' ) AND date_meal < CURRENT_DATE ORDER BY `meals`.`date_meal`  ASC";

// get all the bookings that reference the meals from the previous month
$sql = "SELECT * FROM bookings WHERE meal_uid IN (" . $sqlMeals . ")";

$bookings = $db->query($sql)->fetchAll();

foreach ($bookings AS $booking) {
  $bookingRow = null;

  $bookingObject = new booking($booking['uid']);
  $memberObject = new member($booking['member_ldap']);
  $mealObject = new meal($booking['meal_uid']);

  $bookingGuests = $bookingObject->guestsArray();

  $bookingRow['booking_uid'] = $bookingObject->uid;
  $bookingRow['booking_date'] = $bookingObject->date;
  $bookingRow['booking_charge_to'] = $bookingObject->charge_to;
  $bookingRow['booking_domus_reason'] = $bookingObject->domus_reason;
  $bookingRow['booking_wine'] = $bookingObject->wine;
  $bookingRow['booking_wine_choice'] = $bookingObject->wine_choice;
  $bookingRow['booking_dessert'] = $bookingObject->dessert;
  $bookingRow['booking_guests'] = count($bookingGuests);
  $bookingRow['meal_name'] = $mealObject->name;
  $bookingRow['meal_default_charge_to'] = $mealObject->charge_to;
  $bookingRow['meal_notes'] = $mealObject->notes;
  $bookingRow['meal_date'] = date('Y-m-d', strtotime($mealObject->date_meal));
  $bookingRow['meal_time'] = date('H:i', strtotime($mealObject->date_meal));
  $bookingRow['member_diner_type'] = "Member";
  $bookingRow['member_type'] = $memberObject->type;
  $bookingRow['member_ldap'] = $memberObject->ldap;
  $bookingRow['member_name'] = $memberObject->displayName();
  $bookingRow['member_category'] = $memberObject->category;

  foreach ($bookingGuests AS $guest) {
    $guest = json_decode($guest);
    $bookingRowGuest = null;
    
    if ($mealObject->domus == "1") {
      $charge_to = "Domus";
    } else {
      if (isset($guest->guest_charge_to)) {
        $charge_to = $guest->guest_charge_to;
      } else {
        $charge_to = "Unknown";
      }
    }

    $bookingRowGuest['booking_uid'] = $bookingObject->uid;
    $bookingRowGuest['booking_date'] = $bookingObject->date;
    $bookingRowGuest['booking_charge_to'] = $charge_to;
    $bookingRowGuest['booking_domus_reason'] = $guest->guest_domus_reason;
    $bookingRowGuest['booking_wine'] = onToOne($guest->guest_wine);
    $bookingRowGuest['booking_wine_choice'] = $guest->guest_wine_choice;
    $bookingRowGuest['booking_dessert'] = $bookingObject->dessert; // takes value from host booking
    $bookingRowGuest['meal_name'] = $mealObject->name;
    $bookingRowGuest['meal_notes'] = $mealObject->notes;
    $bookingRowGuest['meal_date'] = date('Y-m-d', strtotime($mealObject->date_meal));
    $bookingRowGuest['meal_time'] = date('H:i', strtotime($mealObject->date_meal));
    $bookingRowGuest['member_diner_type'] = "Guest";
    $bookingRowGuest['member_type'] = "Guest";
    $bookingRowGuest['meal_default_charge_to'] = $mealObject->charge_to; 
    $bookingRowGuest['member_ldap'] = $memberObject->ldap;
    $bookingRowGuest['member_name'] = htmlspecialchars_decode($guest->guest_name);
    $bookingRowGuest['member_category'] = $memberObject->category . " Guest";

    $bookingsArray[] = $bookingRowGuest;
  }

  $bookingsArray[] = $bookingRow;
}

// Build the CSV from the bookingsArray...
foreach ($bookingsArray AS $booking) {
  $rowOutput = null;

  foreach ($columns AS $column) {
    if (!empty($booking[$column])) {
      $rowOutput[] = $booking[$column];
    } else {
      $rowOutput[] = '';
    }

  }

  $csvOUTPUT[] = $rowOutput;

}

// output the column headings
fputcsv($output, $columns);

// loop over the rows, outputting them
foreach ($csvOUTPUT AS $row) {
  fputcsv($output, $row);
  //printArray($report);

}

$logArray['category'] = "report";
$logArray['result'] = "success";
$logArray['description'] = "[reportUID:" . $report['uid'] . "] run";
$logsClass->create($logArray);
?>
