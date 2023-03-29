
<?php
if (isset($_GET['memberUID'])) {
  if ($_SESSION['admin'] == true) {
    $memberUID = $_GET['memberUID'];
  } else {
    admin_gatekeeper();
  }
} else {
  $memberUID = $_SESSION['username'];
}

$bookingsClass = new bookings();
$membersClass = new members();
$memberObject = new member($memberUID);
$upcomingMealUIDS = $memberObject->bookingUIDS_upcoming();
$previousMealUIDS = $memberObject->bookingUIDS_previous();

$dietaryOptionsMax = $settingsClass->value('meal_dietary_allowed');

if ($_SESSION['admin'] == true) {
  $disabledCheck = "";
} else {
  $disabledCheck = " disabled ";
}

if (isset($_POST['memberUID'])) {
  if (!isset($_POST['opt_in'])) {
    $_POST['opt_in'] = 0;
  }
  if (!isset($_POST['default_wine'])) {
    $_POST['default_wine'] = 0;
  }
  if (!isset($_POST['default_dessert'])) {
    $_POST['default_dessert'] = 0;
  }
  if (!isset($_POST['dietary'])) {
    $_POST['dietary'] = null;
  }
  $memberObject->update($_POST);

  $memberObject = new member($memberUID);
}
?>
<?php
$title = $memberObject->displayName() . $memberObject->stewardBadge();
$subtitle = $memberObject->type . " (" . $memberObject->category . ")" . $memberObject->adminBadge();
if ($_SESSION['admin'] == 1) {
  $icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Member", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMemberModal\"");
}
echo makeTitle($title, $subtitle, $icons);

include_once('_member_stats.php');

?>

<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span>Upcoming Meals</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($upcomingMealUIDS); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      $futureBookings = $bookingsClass->booking_uids_future_by_member($memberObject->ldap);
      foreach ($futureBookings AS $booking) {
        $bookingObject = new booking($booking['uid']);
        echo $bookingObject->displayListGroupItem();
      }
      ?>
    </ul>

    <hr />

    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span>Recent Meals</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($previousMealUIDS); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      //$pastBookings = $bookingsClass->booking_uids_past_by_member($memberObject->ldap);
      $i = 0;
      $mealsToDisplay = $settingsClass->value('member_previous_meals_displayed');

      do {

        $bookingObject = new booking($previousMealUIDS[$i]);

        if (isset($bookingObject->uid)) {
          echo $bookingObject->displayListGroupItem();
        }

        $i++;
      } while($i <= $mealsToDisplay);
      ?>
    </ul>
    <a href="report.php?reportUID=3&memberUID=<?php echo $memberObject->uid; ?>" class="text-muted text-end">Export all meal bookings</a>
    
    <hr />
    
    <h4 class="d-flex justify-content-between align-items-center mb-3">Bookings by Day</h4>
    <div id="chart-meals_by_day"></div>
  </div>

  <div class="col-md-7 col-lg-8">
    <h4 class="d-flex mb-3">Personal Information</h4>
    <form method="post" id="memberUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
      <div class="row g-3">
        <div class="col-lg-2 col-md-4 col-sm-4 col-4 mb-3">
          <label for="title" class="form-label">Title</label>
          <select class="form-select" name="title" id="title" required>
            <?php
            foreach ($membersClass->memberTitles() AS $title) {
              if ($title == $memberObject->title) {
                $selected = " selected ";
              } else {
                $selected = "";
              }
              $output = "<option value=\"" . $title . "\"" . $selected . ">" . $title . "</option>";

              echo $output;
            }
            ?>
          </select>
          <div class="invalid-feedback">
            Title is required.
          </div>
        </div>

        <div class="col-lg-5 col-md-8 col-sm-8 col-8 mb-3">
          <label for="firstname" class="form-label">First name</label>
          <input type="text" class="form-control" name="firstname" id="firstname" placeholder="" value="<?php echo $memberObject->firstname; ?>" required>
          <div class="invalid-feedback">
            Valid first name is required.
          </div>
        </div>

        <div class="col-lg-5 mb-3">
          <label for="lastname" class="form-label">Last name</label>
          <input type="text" class="form-control" name="lastname" id="lastname" placeholder="" value="<?php echo $memberObject->lastname; ?>" required>
          <div class="invalid-feedback">
            Valid last name is required.
          </div>
        </div>

        <div class="col-lg-7 mb-3">
          <label for="ldap" class="form-label">LDAP Username</label>
          <div class="input-group">
            <span class="input-group-text" onclick="ldapLookup()">@</span>
            <input type="text" class="form-control" name="ldap" id="ldap" placeholder="LDAP Username" value="<?php echo $memberObject->ldap; ?>" <?php echo $disabledCheck; ?>required>
            <div class="invalid-feedback">
              LDAP Username is required.
            </div>
          </div>
          <?php
          if (isset($memberObject->date_lastlogon)) {
            echo "<small class=\"form-text text-muted\">Last logon: " . dateDisplay($memberObject->date_lastlogon) . " " . timeDisplay($memberObject->date_lastlogon) . "</small>";
          }
          ?>
        </div>

        <div class="col-lg-5 mb-3">
          <label for="category" class="form-label">Member Category</label>
          <select class="form-select" name="category" id="category" <?php echo $disabledCheck; ?> required>
            <?php
            foreach ($membersClass->memberCategories() AS $category) {
              if ($category == $memberObject->category) {
                $selected = " selected ";
              } else {
                $selected = "";
              }
              $output = "<option value=\"" . $category . "\"" . $selected . ">" . $category . "</option>";

              echo $output;
            }
            ?>
          </select>
          <div class="invalid-feedback">
            Please select a valid Member Type.
          </div>
        </div>

        <div class="mb-3">
          <label for="dietary" class="form-label">Dietary Information</label>
          <div class="selectBox" onclick="showCheckboxes()">
            <select class="form-select">
              <option>Select up to <?php echo $dietaryOptionsMax; ?> dietary preferences</option>
            </select>
            <small id="nameHelp" class="form-text text-muted"><?php echo $settingsClass->value('meal_dietary_message'); ?></small>
            <div class="overSelect"></div>
          </div>
          <div id="checkboxes" name="dietary" id="dietary" class="mt-2">
            <?php
            $memberDietary = explode(",", $memberObject->dietary);
            foreach ($membersClass->dietaryOptions() AS $dietaryOption) {
              if (in_array($dietaryOption, $memberDietary)) {
                $checked = " checked";
              } else {
                $checked = "";
              }
              $output  = "<div class=\"form-check\">";
              $output .= "<input class=\"form-check-input dietaryOptionsMax\" type=\"checkbox\" onclick=\"checkMaxCheckboxes(" . $dietaryOptionsMax . ")\" name=\"dietary[]\" id=\"dietary\" value=\"" . $dietaryOption . "\" " . $checked . ">";
              $output .= "<label class=\"form-check-label\" for=\"" . $dietaryOption . "\">" . $dietaryOption . "</label>";
              $output .= "</div>";

              echo $output;
            }
            ?>
          </div>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
          <input type="email" class="form-control" name="email" id="email" placeholder="" value="<?php echo $memberObject->email; ?>">
          <div class="invalid-feedback">
            Please enter a valid email address for shipping updates.
          </div>
        </div>

        <div class="col-6 mb-3">
          <label for="enabled" class="form-label">Member Type</label>
          <select class="form-select" name="type" id="type" <?php echo $disabledCheck; ?> required>
            <option value="SCR" <?php if ($memberObject->type == "SCR") { echo " selected"; } ?>>SCR</option>
            <option value="MCR" <?php if ($memberObject->type == "MCR") { echo " selected"; } ?>>MCR</option>
          </select>
          <div class="invalid-feedback">
            Status is required.
          </div>
        </div>

        <div class="col-6 mb-3">
          <label for="enabled" class="form-label">Enabled/Disabled Status</label>
          <select class="form-select" name="enabled" id="enabled" <?php echo $disabledCheck; ?> required>
            <option value="1" <?php if ($memberObject->enabled == "1") { echo " selected"; } ?>>Enabled</option>
            <option value="0" <?php if ($memberObject->enabled == "0") { echo " selected"; } ?>>Disabled</option>
          </select>
          <div class="invalid-feedback">
            Status is required.
          </div>
        </div>





        <hr class="my-4">

        <div class="divide-y">
          <h4 class="mb-3">Default Preferences</h4>
          <div>
            <label class="row">
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#person-check"></svg> Allow my name to appear on dining lists (also applies to my guests)</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" id="opt_in" name="opt_in" value="1" <?php if ($memberObject->opt_in == "1") { echo " checked";} ?>></label>
              </span>
            </label>
          </div>
          <hr />
          <div>
            <label class="row">
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#wine-glass"></svg> Default Wine <small>(when available)</small></span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" id="default_wine" name="default_wine" value="1" <?php if ($memberObject->default_wine == "1") { echo " checked";} ?>></label>
              </span>
            </label>
          </div>
          <div>
            <label class="row">
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#cookie"></svg> Default Dessert <small>(when available)</small></span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" id="default_dessert" name="default_dessert" value="1" <?php if ($memberObject->default_dessert == "1") { echo " checked";} ?>></label>
              </span>
            </label>
          </div>
        </div>

        <input type="hidden" name="memberUID" id="memberUID" value="<?php echo $memberObject->uid;?>">
        <button class="btn btn-primary  btn-block" type="submit">Update Member Details</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Member Modal -->
<div class="modal" tabindex="-1" id="deleteMemberModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Member <span class="text-danger"><strong>WARNING!</strong></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><span class="text-danger"><strong>WARNING!</strong> Are you sure you want to delete this member?</p>
        <p>This will also delete <strong>all</strong> bookings (past and present) for this member.<p>
        <p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" onclick="memberDelete(<?php echo $memberObject->uid; ?>)"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#trash"/></svg> Delete Member</button>
      </div>
    </div>
  </div>
</div>
  
<script>
checkMaxCheckboxes(<?php echo $dietaryOptionsMax; ?>);

function memberDelete(member_uid) {
  var member_uid = member_uid;
  
  if (window.confirm("Are you really sure you want to delete this member and all of their past/future bookings?")) {
    var xhr = new XMLHttpRequest();

    var formData = new FormData();
    formData.append("member_uid", member_uid);
    
    xhr.onload = async function() {
      // Close the Guest Add modal
      var deleteMemberModal = bootstrap.Modal.getInstance(document.getElementById('deleteMemberModal'));
      deleteMemberModal.hide();
      
      location.href = 'index.php?n=admin_members';
    }

    xhr.onerror = function(){
      // failure case
      alert (xhr.responseText);
    }

    xhr.open ("POST", "../actions/member_delete.php", true);
    xhr.send (formData);

    return false;
  }
}
</script>


<?php
$chartArray = array();
foreach ($memberObject->bookingsByDay() AS $mealName => $mealBookings) {
  $output  = "{name: '" . $mealName . "',";
  $output .= "data: [" . implode(",", $mealBookings) . "]}";
  
  $chartArray[] = $output;
}
?>
<script>
var options = {
  chart: {
    type: 'bar',
    stacked: true,
    height: '300px',
    toolbar: {
      show: false
    }
  },
  dataLabels: {
    enabled: false
  },
  series: [<?php echo implode(",", $chartArray); ?>],
  xaxis: {
    categories: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
  }
}

var chart = new ApexCharts(document.querySelector("#chart-meals_by_day"), options);

chart.render();
</script>