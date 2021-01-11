<?php
admin_gatekeeper();

$sql  = "SELECT * FROM bookings WHERE DATE(date) BETWEEN '2008-1-01' AND '2008-12-20'";
$sql .= "";
$sql .= "";

$bookings = $db->query($sql)->fetchAll();

foreach ($bookings AS $booking) {
  $csvOUTPUT[] = array($booking['date'], $booking['member_ldap'], 'Booking', $booking['bookingUID']);
  if (!empty($booking['guests_array'])) {
    $guests = json_decode($booking['guests_array']);

    foreach ($guests AS $guest) {
      $csvOUTPUT[] = array($booking['date'], $booking['member_ldap'], 'Guest', $guest->guest_name);
    }
  }
}

$rows = $db->query($sql)->fetchAll();
$rowsColumns = explode(",", "bookingDate,bookingUID,bookingType");

// output the column headings
fputcsv($output, $rowsColumns);

// loop over the rows, outputting them
foreach ($csvOUTPUT AS $row) {
  fputcsv($output, $row);
  //printArray($report);

}

?>
