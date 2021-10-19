<?php
include_once("../../inc/autoload.php");

$membersClass = new members;

$memberObject = new member($_SESSION['username']);
$bookingObject = new booking($_GET['bookingUID']);
$mealObject = new meal($bookingObject->meal_uid);

$dietaryOptionsMax = $settingsClass->value('meal_dietary_allowed');

// check if we're adding a new guest, or modifying an existing guest
if (isset($_GET['guestUID'])) {
  // we're modifying an existing guest
  $mode = "edit";
  $title = "Edit Guest";

  $guests = json_decode($bookingObject->guests_array);

  foreach ($guests AS $guest) {
    $guest = json_decode($guest);
    if ($guest->guest_uid == $_GET['guestUID']) {
      $guestObject = $guest;
    }
  }
} else {
  // we're adding a new guest
  $mode = "add";
  $title = "Add Guest";
}
?>

<div class="modal-body">
  <?php
  if ($mealObject->check_meal_bookable(true)) {
  ?>
    <form method="post" class="needs-validation" action="../actions/booking_add_guest.php">
      <div class="form-group mb-3">
        <label for="name">Guest Name</label>
        <input type="text" class="form-control" name="guest_name" id="guest_name" aria-describedby="termNameHelp" value="<?php echo $guestObject->guest_name; ?>" required>
        <?php
        if ($memberObject->opt_in == "1") {
          echo "<small id=\"nameHelp\" class=\"form-text text-muted\">This name will appear on the sign-up list</small>";
        } else {
          echo "<small id=\"nameHelp\" class=\"form-text text-muted\">This name will be hidden on the sign-up list.  You can change your default privacy settings in <a href=\"index.php?n=member\">your profile</a></small>";
        }
        ?>
      </div>

      <div class="mb-3">
        <label for="dietary" class="form-label">Guest's Dietary Information</label>
        <div class="selectBox" onclick="showCheckboxes()">
          <select class="form-select">
            <option>Select up to <?php echo $dietaryOptionsMax; ?> dietary preferences</option>
          </select>
          <small id="nameHelp" class="form-text text-muted"><?php echo $settingsClass->value('meal_dietary_message'); ?></small>
          <div class="overSelect"></div>
        </div>
        <div id="checkboxes" class="mt-2">
          <?php
          foreach ($membersClass->dietaryOptions() AS $dietaryOption) {
            if (in_array($dietaryOption, $guestObject->guest_dietary)) {
              $checked = " checked ";
            } else {
              $checked = "";
            }

            $output  = "<div class=\"form-check\">";
            $output .= "<input class=\"form-check-input dietaryOptionsMax\" type=\"checkbox\" onclick=\"checkMaxCheckboxes(" . $dietaryOptionsMax . ")\" name=\"guest_dietary\" id=\"guest_dietary\" value=\"" . $dietaryOption . "\" " . $checked . ">";
            $output .= "<label class=\"form-check-label\" for=\"" . $dietaryOption . "\">" . $dietaryOption . "</label>";
            $output .= "</div>";

            echo $output;
          }
          ?>
        </div>
      </div>

      <hr />

      <div>
        <label class="row">
          <div>
            <label for="guest_charge_to" class="form-label">Charge Guest Booking To</label>
            <select class="form-select mb-3" id="guest_charge_to" name="guest_charge_to" aria-label="Charge To">
              <?php
              $chargeToOptions = explode(",", $settingsClass->value('booking_guest_charge-to'));
              
              foreach ($chargeToOptions AS $chargeToOption) {
                $selected = "";
                
                if ($guestObject->guest_charge_to == $chargeToOption) {
                  $selected = " selected";
                }
                
                $output = "<option " . $selected . " value=\"" . $chargeToOption . "\">" . $chargeToOption . "</option>";
                
                echo $output;
              }
              
              // show/hide the domus_reason text box:
              if ($guestObject->guest_charge_to == "Battels" || $mode == "add") {
                $domusVisual = " visually-hidden";
              } else {
                $domusVisual = "";
              }
              ?>
            </select>
            
            
            <input class="form-control mb-3 <?php echo $domusVisual; ?>" type="text" id="guest_domus_reason" name="guest_domus_reason" placeholder="Description (required)" aria-label="Charge Guest To Description" value="<?php echo $guestObject->guest_domus_reason; ?>" <?php if ($guestObject->guest_charge_to <> "Battels") { echo " required";} ?>>
          </div>

        </label>
      </div>
      <div>
        <label class="row">
          <?php
          $wineDisabledCheck = " disabled";
          if ($mealObject->allowed_wine == 1 && $mealObject->check_cutoff_ok(true)) {
            $wineDisabledCheck = "";
          }

          $checked = "";
          if ($guestObject->guest_wine == "on") {
            $checked = "checked";
          }
          ?>
          <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#wine-glass"></svg> Guest Wine</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch">
              <input class="form-check-input" id="guest_wine" name="guest_wine" type="checkbox" <?php echo $checked ?> <?php echo $wineDisabledCheck; ?>>
            </label>
          </span>
        </label>
      </div>
      <div>
        <label class="row">
          <?php
          $dessertDisabledCheck = " disabled";
          $dessertHelper = "(dessert is not available)";
          $dessertChecked = "";
          
          if ($guestObject->guest_dessert == "on") {
            // guest already booked on for dessert
            $dessertChecked = " checked";
            
            if ($mealObject->allowed_dessert == 1 && $mealObject->check_meal_bookable(true)) {
              $dessertDisabledCheck = "";
              $dessertHelper = "";
            } else {
              $dessertHelper = "Deadline passed";
            }
            
            
          } else {
            // we're not yet booked on for dessert
            if ($mealObject->allowed_dessert == 1 && $mealObject->check_meal_bookable(true)) {
              $dessertDisabledCheck = "";
              $dessertHelper = "";
            }
            
            // check if member has booked dessert - if they haven't then guests aren't allowed either
            if ($bookingObject->dessert != 1) {
              $dessertDisabledCheck = " disabled";
              $dessertHelper =  "(you are required to have dessert also)";
            }
            
            // check if dessert capacity is reached
            if ($mealObject->total_dessert_bookings_this_meal() >= $mealObject->scr_dessert_capacity) {
              $dessertDisabledCheck = " disabled";
              $dessertHelper =  "(capacity for dessert reached)";
            }
          }
          
          //admin override for dessert booking!
          if ($_SESSION['admin'] == 1) {
            $dessertDisabledCheck = "";
          }
          ?>
          <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#cookie"></svg> Guest Dessert <?php echo $dessertHelper; ?></span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch">
              <input class="form-check-input" id="guest_dessert" name="guest_dessert" type="checkbox" <?php echo $dessertChecked; ?> <?php echo $dessertDisabledCheck; ?>>
            </label>
          </span>
        </label>
      </div>


    </form>


  <?php } else {
    $buttonDeleteDisable = "disabled";
    
    if (!$mealObject->check_cutoff_ok(true)) {
      echo "<p>The deadline for making changes to this booking has passed.  Please contact the Bursary for further assistance.</p>";
    } elseif ($mealObject->check_capacity_ok()) {
      echo "<p>You have added the maximum number of guests permitted for this meal.</p>";
      $buttonAddDisable = "disabled";
      $buttonDeleteDisable = "";
    } else {
      echo "<p>Meal not bookable.</p>";
    }
  } ?>
  <input type="hidden" id="bookingUID" name="bookingUID" value="<?php echo $bookingObject->uid; ?>">
  <input type="hidden" id="mealUID" name="mealUID" value="<?php echo $mealObject->uid; ?>">
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
  <?php
  $buttonEditDisable = "";
  $buttonAddDisable = "";
  
  if ($mode == "add") {
    if (!$mealObject->check_cutoff_ok(true)) {
      $buttonAddDisable = " disabled";
    }
    
    echo "<button type=\"submit\" class=\"btn btn-primary " . $buttonAddDisable . "\" onclick=\"addGuest()\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add Guest</button>";
  } elseif ($mode == "edit") {
    if (!$mealObject->check_cutoff_ok(true)) {
      // we're editing the guest, and there's nothing stopping us
      $buttonEditDisable = " disabled";
    }
    
    echo "<button type=\"submit\" class=\"btn btn-danger " . $buttonEditDisable . "\" onclick=\"deleteGuest('" . $guestObject->guest_uid . "')\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Guest</button>";
    echo "<button type=\"submit\" class=\"btn btn-primary " . $buttonEditDisable . "\" onclick=\"editGuest('" . $guestObject->guest_uid . "')\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Modify Guest</button>";
  }
  ?>
</div>

