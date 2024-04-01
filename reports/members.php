<?php
// CSV columns to include
$columns = array(
  "uid",
  "enabled",
  "type",
  "precedence",
  "category",
  "ldap",
  "title",
  "firstname",
  "lastname",
  "email",
  "dietary",
  "opt_in",
  "email_reminders",
  "default_wine",
  "default_dessert",
  "date_lastlogon"
);

//get all members
$sql  = "SELECT * FROM members ORDER BY enabled DESC, type DESC, precedence ASC, lastname ASC, firstname ASC";

$members = $db->query($sql)->fetchAll();

foreach ($members AS $member) {
  $bookingRow = null;
  
  $memberObject = new member($member['uid']);
  
  $memberRow['uid'] = $memberObject->uid;
  $memberRow['enabled'] = $memberObject->enabled;
  $memberRow['type'] = $memberObject->type;
  $memberRow['precedence'] = $memberObject->precedence;
  $memberRow['category'] = $memberObject->category;
  $memberRow['ldap'] = $memberObject->ldap;
  $memberRow['title'] = $memberObject->title;
  $memberRow['firstname'] = $memberObject->firstname;
  $memberRow['lastname'] = $memberObject->lastname;
  $memberRow['email'] = $memberObject->email;
  $memberRow['dietary'] = $memberObject->dietary;
  $memberRow['opt_in'] = $memberObject->opt_in;
  $memberRow['email_reminders'] = $memberObject->email_reminders;
  $memberRow['default_wine'] = $memberObject->default_wine;
  $memberRow['default_dessert'] = $memberObject->default_dessert;
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
