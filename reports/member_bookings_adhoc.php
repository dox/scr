<?php
// CSV columns to include
$columns = array(
  "uid",
  "enabled",
  "type",
  "category",
  "title",
  "firstname",
  "lastname",
  "email",
  "meals_scr_guest_night",
  "meals_formal_hall",
  "meals_dinner",
  "meals_lunch",
  "meals_buffet",
  "meals_breakfast",
  "date_lastlogon"
);

//get all members
$sql  = "SELECT * FROM members ORDER BY enabled DESC, type DESC, precedence ASC, lastname ASC, firstname ASC";

$members = $db->query($sql)->fetchAll();

$startDate = $_GET['start-date'];
$endDate = $_GET['end-date'];

foreach ($members AS $member) {
  $bookingRow = null;
  
  $memberObject = new member($member['uid']);
  
  $sql  = "SELECT meals.type, COUNT(*) AS total_meals";
  $sql .= " FROM bookings";
  $sql .= " LEFT JOIN meals ON bookings.meal_uid = meals.uid";
  $sql .= " WHERE bookings.member_ldap = '" . $memberObject->ldap . "'";
  $sql .= " AND DATE(meals.date_meal) >= '" . $startDate . "'";
  $sql .= " AND DATE(meals.date_meal) <= '" . $endDate . "'";
  $sql .= " GROUP BY meals.type";
  
  $bookings = $db->query($sql)->fetchAll();
  
  $bookingArray = array();
  
  foreach ($bookings AS $booking) {
    $bookingArray[$booking['type']] = $booking['total_meals'];
  }
  
  $memberRow['uid'] = $memberObject->uid;
  $memberRow['enabled'] = $memberObject->enabled;
  $memberRow['type'] = $memberObject->type;
  $memberRow['category'] = $memberObject->category;
  $memberRow['title'] = $memberObject->title;
  $memberRow['firstname'] = $memberObject->firstname;
  $memberRow['lastname'] = $memberObject->lastname;
  $memberRow['email'] = $memberObject->email;

  $memberRow['meals_scr_guest_night'] = $bookingArray['SCR Guest Night'];
  $memberRow['meals_formal_hall'] = $bookingArray['Formal Hall'];
  $memberRow['meals_dinner'] = $bookingArray['Dinner'];
  $memberRow['meals_lunch'] = $bookingArray['Lunch'];
  $memberRow['meals_buffet'] = $bookingArray['Buffet'];
  $memberRow['meals_breakfast'] = $bookingArray['Breakfast'];
  
  $memberRow['date_lastlogon'] = date('Y-m-d H:i:s', strtotime($memberObject->date_lastlogon));
  
  $membersArray[] = $memberRow;
}

// Build the CSV from the bookingsArray...
foreach ($membersArray AS $member) {
  $rowOutput = null;

  foreach ($columns AS $column) {
    $rowOutput[] = $member[$column];
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
