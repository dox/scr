<?php
if (isset($_POST['assignFrom']) && isset($_POST['assignTo'])) {
  $sql = "UPDATE bookings SET member_ldap = '" . $_POST['assignTo'] . "' WHERE member_ldap = '" . $_POST['assignFrom'] . "';";

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

<datalist id="members">
  <?php
  foreach ($members AS $member) {
    $content = $member['firstname'] . " " . $member['lastname'] . " (" . $member['ldap'] . ")";
    echo "<option data-value=\"" . $member['ldap'] . "\" value=\"" . $content . "\">";
  }
  ?>
</datalist>

<div class="p-5 mb-4 bg-body-tertiary rounded-3">
  <div class="container-fluid py-5">
    <h1 class="display-5 fw-bold">Orphaned Bookings</h1>
    <p class="col-md-8 fs-4"><?php echo number_format($totalOrphanedBookings); ?> bookings that belong to <?php echo number_format($totalOrphanedMembers); ?> unknown members</p>
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
      $dropdown  = "<div class=\"input-group mb-3\">";
      $dropdown .= "<input type=\"text\" class=\"form-control\" placeholder=\"Member Name\" list=\"members\" name=\"" . $booking['member_ldap'] . "-browser\" id=\"" . $booking['member_ldap'] . "-browser\">";
      $dropdown .= "<button class=\"btn btn-outline-secondary\" type=\"button\" id=\"button-addon2\" data-member=\"" . $booking['member_ldap'] . "\" onclick=\"test(this)\">Reassign</button>";
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


<script>
function test(ldap) {
  var assignFrom = ldap.getAttribute('data-member');
  
  var shownVal = document.getElementById(assignFrom + "-browser").value;
  var value2send = document.querySelector("#members option[value='"+shownVal+"']").dataset.value;
  
  var url = "/report.php?reportUID=4";
  
  var xhr = new XMLHttpRequest();
  var formData = new FormData();
  xhr.open("POST", url, true);
  
  formData.append("assignFrom", assignFrom);
  formData.append("assignTo", value2send);
  
  xhr.send(formData);
  
  xhr.onload = function() {
    if (xhr.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${xhr.status}: ${xhr.statusText}`); // e.g. 404: Not Found
    } else {
      // success
      location.reload();
    }
  }
  
  xhr.onerror = function() {
    alert("Request failed");
  };
}
</script>