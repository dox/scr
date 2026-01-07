<?php
$bookingsClass = new bookings();

$mealObject = new meal($_GET['mealUID']);
$bookings = $bookingsClass->bookingsUIDsByMealUID($mealObject->uid);

if (checkpoint_charlie("reports")) {
  // user is an admin, generate the report
} elseif (strtoupper($_SESSION['username']) == strtoupper($memberObject->ldap)) {
  // user is themselves, that's fine too
} else {
  // user attempting to access report for someone else - not allowed
  die("Access not granted");
}

// CSV columns to include
$columns = array(
  "booking_uid",
  "meal_uid",
  "booking_created",
  "booking_charge_to",
  "booking_domus_reason",
  "booking_wine",
  "booking_wine_choice",
  "booking_dessert",
  "booking_guests",
  "meal_name",
  "meal_notes",
  "meal_date",
  "meal_time",
  "member_diner_type", //member/guest
  "member_type",
  "member_ldap",
  "member_name",
  "member_category"
);



foreach ($bookings AS $booking) {
  $bookingRow = null;
  
  $bookingObject = new booking($booking['uid']);
  $memberObject = new member($bookingObject->member_ldap);
  $bookingGuests = json_decode($bookingObject->guests_array, true);
  
  if (!is_array($bookingGuests)) {
    $bookingGuests = array();
  }

  $bookingRow['booking_uid'] = $bookingObject->uid;
  $bookingRow['meal_uid'] = $mealObject->uid;
  $bookingRow['booking_created'] = $bookingObject->date;
  $bookingRow['booking_charge_to'] = $bookingObject->charge_to;
  $bookingRow['booking_domus_reason'] = $bookingObject->domus_reason;
  $bookingRow['booking_wine'] = ($bookingObject->wine);
  $bookingRow['booking_wine_choice'] = ($bookingObject->wine_choice);
  $bookingRow['booking_dessert'] = ($bookingObject->dessert);
  $bookingRow['booking_guests'] = count($bookingGuests);
  $bookingRow['meal_name'] = $mealObject->name;
  $bookingRow['meal_notes'] = $mealObject->notes;
  $bookingRow['meal_date'] = date('Y-m-d', strtotime($mealObject->date_meal));
  $bookingRow['meal_time'] = date('H:i', strtotime($mealObject->date_meal));
  $bookingRow['member_diner_type'] = "Member";
  $bookingRow['member_type'] = $memberObject->type;
  $bookingRow['member_ldap'] = $memberObject->ldap;
  $bookingRow['member_name'] = $memberObject->displayName();
  $bookingRow['member_category'] = $memberObject->category;

  foreach ($bookingGuests AS $guest) {
    $bookingRowGuest = null;
    
    $guest = json_decode($guest);

    $bookingRowGuest['booking_uid'] = $bookingObject->uid;
    $bookingRowGuest['meal_uid'] = $mealObject->uid;
    $bookingRowGuest['booking_created'] = $bookingObject->date;
    $bookingRowGuest['booking_charge_to'] = $guest->guest_charge_to;
    $bookingRowGuest['booking_domus_reason'] = $guest->guest_domus_reason;
    $bookingRowGuest['booking_wine'] = onToOne($guest->guest_wine) ;
    $bookingRowGuest['booking_wine_choice'] = onToOne($guest->guest_wine_choice) ;
    $bookingRowGuest['booking_dessert'] = $bookingObject->dessert; // takes value from host booking
    $bookingRowGuest['meal_name'] = htmlspecialchars_decode($mealObject->name);
    $bookingRowGuest['meal_notes'] = $mealObject->notes;
    $bookingRowGuest['meal_date'] = date('Y-m-d', strtotime($mealObject->date_meal));
    $bookingRowGuest['meal_time'] = date('H:i', strtotime($mealObject->date_meal));
    $bookingRowGuest['member_diner_type'] = "Guest";
    $bookingRowGuest['member_type'] = "Guest";
    $bookingRowGuest['member_ldap'] = $memberObject->ldap;
    $bookingRowGuest['member_name'] = $guest->guest_name;
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
$logArray['description'] = "[reportUID:" . $report['uid'] . "] run for [memberUID:" . $memberObject->uid . "]";
$logsClass->create($logArray);
?>
