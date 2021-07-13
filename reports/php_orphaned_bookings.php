<?php
if (isset($_GET['assignFrom']) && isset($_GET['assignTo'])) {
  $sql = "UPDATE bookings SET member_ldap = '" . $_GET['assignTo'] . "' WHERE member_ldap = '" . $_GET['assignFrom'] . "';";

  echo $sql;

  $db->query($sql);
}
$membersClass = new members();
$sql = "SELECT count(*) AS bookingsCount, member_ldap FROM bookings WHERE member_ldap NOT IN(SELECT ldap FROM members) GROUP BY member_ldap ORDER BY bookingsCount DESC;";
$orphanedBookings = $db->query($sql)->fetchAll();

$totalOrphanedMembers = count($orphanedBookings);
$totalOrphanedBookings = 0;
foreach ($orphanedBookings AS $booking) {
  $totalOrphanedBookings = $totalOrphanedBookings + $booking['bookingsCount'];
}

$members = $membersClass->all();


?>
<div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
  <div class="p-lg-5 mx-auto my-5">
    <h1 class="display-4 fw-normal">Orphaned Bookings</h1>
    <p class="lead fw-normal"><?php echo number_format($totalOrphanedBookings); ?> bookings that belong to <?php echo number_format($totalOrphanedMembers); ?> unknown members</p>
  </div>
</div>

<table class="table">
  <thead>
    <tr>
      <th scope="col">Bookings</th>
      <th scope="col">Booking LDAP</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($orphanedBookings AS $booking) {
      $dropdown  = "<div class=\"dropdown\">";
      $dropdown .= "<button class=\"btn btn-secondary dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton1\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">";
      $dropdown .= "Reassign Bookings To:";
      $dropdown .= "</button>";
      $dropdown .= "<ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton1\">";
      foreach ($members AS $member) {
        $url = $_SERVER[REQUEST_URI] . "&assignFrom=" . $booking['member_ldap'] . "&assignTo=" . $member['ldap'];

        $dropdown .= "<li><a class=\"dropdown-item\" href=\"" . $url . "\">" . $member['firstname'] . " " . $member['lastname'] . " (" . $member['ldap'] . ")</a></li>";
      }
      $dropdown .= "</ul>";
      $dropdown .= "</div>";

      $output  = "<tr>";
      $output .= "<td>" . $booking['bookingsCount'] . "</td>";
      $output .= "<td><kbd>" . $booking['member_ldap'] . "</kbd></td>";
      $output .= "<td>" . $dropdown . "</td>";
      $output .= "</tr>";

      echo $output;
    }
    ?>
  </tbody>
</table>
