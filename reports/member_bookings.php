<?php
$memberObject = new member($_GET['memberUID']);
$bookings = $memberObject->getAllBookingUIDS();

if ($_SESSION['admin'] != true) {
  if (strtoupper($_SESSION['username']) != strtoupper($memberObject->ldap)) {
    admin_gatekeeper();
  }
}
// CSV columns to include
$columns = array(
  "booking_uid",
  "meal_uid",
  "booking_charge_to",
  "booking_domus_reason",
  "booking_wine",
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



foreach ($bookings AS $bookingUID) {
  $bookingRow = null;

  $bookingObject = new booking($bookingUID);
  $mealObject = new meal($bookingObject->meal_uid);

  $bookingGuests = json_decode($bookingObject->guests_array, true);

  $bookingRow['booking_uid'] = $bookingObject->uid;
  $bookingRow['meal_uid'] = $bookingObject->meal_uid;
  $bookingRow['booking_charge_to'] = $bookingObject->charge_to;
  $bookingRow['booking_domus_reason'] = $bookingObject->domus_reason;
  $bookingRow['booking_wine'] = $bookingObject->wine;
  $bookingRow['booking_dessert'] = $bookingObject->dessert;
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
    $bookingRowGuest['meal_uid'] = $bookingObject->meal_uid;
    $bookingRowGuest['booking_charge_to'] = $guest->guest_charge_to;
    $bookingRowGuest['booking_domus_reason'] = $guest->guest_domus_reason;
    $bookingRowGuest['booking_wine'] = onToOne($guest->guest_wine) ;
    $bookingRowGuest['booking_dessert'] = onToOne($guest->guest_dessert);
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
