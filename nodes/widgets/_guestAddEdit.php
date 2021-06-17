<?php
include_once("../../inc/autoload.php");
$membersClass = new members;
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
  $memberObject->dietary = array();
}
//echo $title;
?>

<div class="modal-body">
  <?php
  if ($mealObject->check_meal_bookable(true) && count($bookingObject->guestsArray()) < $mealObject->getTotalGuestsAllowed()) {
  ?>
    <form method="post" class="needs-validation" action="../actions/booking_add_guest.php" onsubmit="return addGuest(this);">
      <div class="form-group">
        <label for="name">Guest Name</label>
        <input type="text" class="form-control" name="guest_name" id="guest_name" aria-describedby="termNameHelp" value="<?php echo $guestObject->guest_name; ?>" required>
        <small id="nameHelp" class="form-text text-muted">This name will show on the sign-up list</small>
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
          <?php
          $domusDisabledCheck = " disabled";
          if ($mealObject->domus == 1 || $mealObject->check_meal_bookable(true)) {
            $domusDisabledCheck = "";
          }

          $domusChecked = "";
          $domusCheckedReason = "visually-hidden";
          if ($guestObject->guest_domus == "on") {
            $domusChecked = "checked";
            $domusCheckedReason = "";
          }
          ?>
          <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#graduation-cap"></svg> Domus</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch">
              <input class="form-check-input" id="guest_domus" name="guest_domus" type="checkbox" <?php echo $domusChecked; ?> onchange="guestDomus(this.id)" <?php echo $domusDisabledCheck; ?>>
            </label>
          </span>

          <div class="form-group guest_domus_descriptionDiv <?php echo $domusCheckedReason; ?>">
            <label for="date_start">Domus Description</label>
            <input type="text" class="form-control needs-validation" name="guest_domus_description" id="guest_domus_description" aria-describedby="guest_domus_description" placeholder="Domus reason (required)" value="<?php echo $guestObject->guest_domus_description; ?>">
            <small id="guest_domus_descriptionHelp" class="form-text text-muted">A brief description of why this guest is Domus</small>
          </div>

        </label>
      </div>
      <div>
        <label class="row">
          <?php
          $wineDisabledCheck = " disabled";
          if ($mealObject->allowed_wine == 1 && $mealObject->check_meal_bookable(true)) {
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
          <input type="text" class="form-control" id="domus_description" placeholder="Domus reason (required)" hidden>
          <small id="domus_descriptionHelp" class="form-text text-muted" hidden>A brief description of why your booking is Domus</small>
        </label>
      </div>
      <div>
        <label class="row">
          <?php
          $dessertDisabledCheck = " disabled";
          if ($mealObject->allowed_dessert == 1 && $mealObject->check_meal_bookable(true)) {
            $dessertDisabledCheck = "";
          }

          $checked = "";
          if ($guestObject->guest_dessert == "on") {
            $checked = "checked";
          }
          ?>
          <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#cookie"></svg> Guest Dessert</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch">
              <input class="form-check-input" id="guest_dessert" name="guest_dessert" type="checkbox" <?php echo $checked; ?> <?php echo $dessertDisabledCheck; ?>>
            </label>
          </span>
          <input type="text" class="form-control" id="domus_description" name="guest_domus_description" placeholder="Domus reason (required)" hidden>
          <small id="domus_descriptionHelp" class="form-text text-muted" hidden>A brief description of why your booking is Domus</small>
        </label>
      </div>


    </form>


  <?php } else {
    $buttonDeleteDisable = "disabled";
    if (date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($mealObject->date_cutoff))) {
      echo "<p>The deadline for making changes to this booking has passed.  Please contact the Bursary for further assistance.</p>";
    } elseif (count($bookingObject->guestsArray()) >= $mealObject->getTotalGuestsAllowed()) {
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
  if ($mealObject->check_meal_bookable(true)) {
    if ($mode == "edit") {
      echo "<button type=\"submit\" class=\"btn btn-danger " . $buttonDeleteDisable . "\" onclick=\"deleteGuest('" . $guestObject->guest_uid . "')\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Guest</button>";
      echo "<button type=\"submit\" class=\"btn btn-primary " . $buttonAddDisable . "\" onclick=\"editGuest('" . $guestObject->guest_uid . "')\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Modify Guest</button>";
    } elseif ($mode == "add") {
      echo "<button type=\"submit\" class=\"btn btn-primary " . $buttonAddDisable . "\" onclick=\"addGuest()\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add Guest</button>";
    }
  }
  ?>
</div>
