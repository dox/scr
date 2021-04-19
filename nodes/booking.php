<?php
$bookingsClass = new bookings();
$mealObject = new meal($_GET['mealUID']);

$bookingByMember = $bookingsClass->bookingForMealByMember($mealObject->uid, $_SESSION['username']);
$bookingObject = new booking($bookingByMember['uid']);

if (isset($_POST['bookingUID'])) {
  if (empty($_POST['domus'])) {
    $_POST['domus'] = 0;
  }
  if (empty($_POST['wine'])) {
    $_POST['wine'] = 0;
  }
  if (empty($_POST['dessert'])) {
    $_POST['dessert'] = 0;
  }

  $bookingObject->update($_POST);
  $bookingObject = new booking($_POST['bookingUID']);
}

$termsClass = new terms();
$checkTerm = $termsClass->checkIsInTerm($mealObject->date_meal);
$term = new term($checkTerm[0]['uid']);

$membersClass = new members();



$dietaryOptionsMax = $settingsClass->value('meal_dietary_allowed');

if (isset($_POST['guest_name'])) {
  $bookingObject->update($_POST);
  $bookingObject = new booking($_GET['bookingUID']);
}



//$bookingsThisMeal = $bookingsClass->bookings_this_meal($mealObject->uid);

/*
$arr = array(
  array('name'=>'John Smith', 'dietary' => 'No fish'),
  array('name'=>'Jane Doe', 'dietary' => 'No pork')
);
echo json_encode($arr);
printArray($arr);
*/
?>

<?php
$title = "Week " . $term->whichWeek($mealObject->date_meal) . " " . $mealObject->name;
$subtitle = $mealObject->type . ": " . $mealObject->location . ", " . dateDisplay($mealObject->date_meal, true);
if (isset($bookingByMember)) {
  $icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Booking", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#staticBackdrop\"");
  $icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add Guest", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#modal_guest_add\"");
}

echo makeTitle($title, $subtitle, $icons);

if (isset($bookingByMember)) {
?>
  <form method="post" id="bookingUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="row needs-validation" novalidate>
    <div class="col-md-5 col-lg-4 order-md-last">
      <div class="divide-y">
        <?php
        //DOMUS form element

        $output  = "<div>";
        $output .= "<label class=\"row\">";
        $output .= "<span class=\"col\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#graduation-cap\"></svg> Domus</span>";
        $output .= "<span class=\"col-auto\">";
        $output .= "<label class=\"form-check form-check-single form-switch\">";

        if ($mealObject->domus == 1 || $_SESSION['admin'] == true) {
          $domusDisabledCheck = "";
        } else {
          $domusDisabledCheck = " disabled";
        }

        if ($bookingObject->domus == 1) {
          $checked = "checked";
        } else {
          $checked = "";
        }

        $output .= "<input class=\"form-check-input needs-validation\" novalidate id=\"domus\" name=\"domus\" type=\"checkbox\"" . $checked . " value=\"1\" onchange=\"domusCheckbox(this.id)\">";
        $output .= "</label>";
        $output .= "</span>";
        $output .= "<input type=\"text\" class=\"form-control\" id=\"domus_reason\" name=\"domus_reason\" placeholder=\"Domus reason (required)\" hidden>";
        $output .= "<small id=\"domus_reasonHelp\" class=\"form-text text-muted mb-3\" hidden>A brief description of why your booking is Domus</small>";
        $output .= "</label>";
        $output .= "</div>";
        $output .= "";

        echo $output;
        ?>
        <div>
          <label class="row">
            <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#wine-glass"></svg> Wine</span>
            <span class="col-auto">
              <label class="form-check form-check-single form-switch">
                <?php
                if ($mealObject->allowed_wine == 1) {
                  $wineDisabledCheck = "";
                } else {
                  $wineDisabledCheck = " disabled";
                }
                ?>
                <input class="form-check-input" <?php echo $wineDisabledCheck; ?> id="wine" name="wine" value="1" type="checkbox" <?php if ($bookingObject->wine == 1) { echo "checked";} ?>>
              </label>
            </span>
          </label>
        </div>
        <div>
          <label class="row">
            <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#cookie"></svg> Dessert</span>
            <span class="col-auto">
              <label class="form-check form-check-single form-switch">
                <?php
                if ($mealObject->allowed_dessert == 1) {
                  $dessertDisabledCheck = "";
                } else {
                  $dessertDisabledCheck = " disabled";
                }
                ?>
                <input class="form-check-input" <?php echo $dessertDisabledCheck; ?> id="dessert" name="dessert" value="1" type="checkbox" <?php if ($bookingObject->dessert == 1) { echo "checked";} ?>>
              </label>
            </span>
          </label>
        </div>
        <button type="submit" class="btn btn-sm w-100 btn-primary">Update Booking Preferences</button>
        <input type="hidden" name="bookingUID" id="bookingUID" value="<?php echo $bookingObject->uid; ?>">
      </div>

      <hr />

      <div id="guests_list"><?php include_once("widgets/_bookingGuestList.php"); ?></div>
    </div>
    <div class="col-md-7 col-lg-8">
      <div id="meal_guest_list"><?php include_once("widgets/_mealGuestList.php"); ?></div>
      <?php
      if (isset($mealObject->menu)) {
        echo "<div class=\"card text-center\">";
        echo "<div class=\"card-body\">";
        echo "<h4 class=\"card-title text-center\">Menu</h4>";
        echo "<p><i>" . $mealObject->location . ", " . dateDisplay($mealObject->date_meal, true) . "</i></p>";
        echo $mealObject->menu;
        echo "</div>";
        echo "</div>";
      }
      ?>
    </div>
  </form>

<!-- Modal -->
<div class="modal fade" id="modal_guest_add" tabindex="-1" aria-labelledby="modal_guest_add" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" class="needs-validation" action="../actions/booking_add_guest.php" onsubmit="return addGuest(this);">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Guest</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
        if (date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($mealObject->date_cutoff)) && $_SESSION['admin'] != "true") {
          echo "<p>The deadline for making changes to this booking has passed.  Please contact the Bursary for further assistance.</p>";
        } elseif (count($bookingObject->guestsArray()) >= $mealObject->getTotalGuestsAllowed() && $_SESSION['admin'] != "true") {
          echo "<p>You have added the maximum number of guests permitted for this meal.</p>";
          $buttonAddDisable = "disabled";
        } else {
        ?>
          <div class="form-group">
            <label for="name">Guest Name</label>
            <input type="text" class="form-control" name="guest_name" id="guest_name" aria-describedby="termNameHelp" required>
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
              $memberDietary = explode(",", $memberObject->dietary);
              foreach ($membersClass->dietaryOptions() AS $dietaryOption) {
                $output  = "<div class=\"form-check\">";
                $output .= "<input class=\"form-check-input dietaryOptionsMax\" type=\"checkbox\" onclick=\"checkMaxCheckboxes(" . $dietaryOptionsMax . ")\" name=\"guest_dietary[]\" id=\"guest_dietary\" value=\"" . $dietaryOption . "\">";
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
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#graduation-cap"></svg> Domus</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch">
                  <input class="form-check-input" id="guest_domus" name="guest_domus" type="checkbox" <?php if ($bookingObject->domus == 1) { echo "checked";} ?> onchange="guestDomus(this.id)">
                </label>
              </span>

              <div class="form-group guest_domus_descriptionDiv visually-hidden">
                <label for="date_start">Domus Description</label>
                <input type="text" class="form-control needs-validation" name="guest_domus_description" id="guest_domus_description" aria-describedby="domus_description" placeholder="Domus reason (required)">
                <small id="guest_domus_descriptionHelp" class="form-text text-muted">A brief description of why this guest is Domus</small>
              </div>

            </label>
          </div>
          <div>
            <label class="row">
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#wine-glass"></svg> Guest Wine</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch">
                  <input class="form-check-input" id="guest_wine" name="guest_wine" type="checkbox" <?php echo $wineDisabledCheck; if ($bookingObject->guest_wine == 1) { echo "checked";} ?>>
                </label>
              </span>
              <input type="text" class="form-control" id="domus_description" placeholder="Domus reason (required)" hidden>
              <small id="domus_descriptionHelp" class="form-text text-muted" hidden>A brief description of why your booking is Domus</small>
            </label>
          </div>
          <div>
            <label class="row">
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#cookie"></svg> Guest Dessert</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch">
                  <input class="form-check-input" id="guest_dessert" name="guest_dessert" type="checkbox" <?php echo $dessertDisabledCheck; if ($bookingObject->guest_dessert == 1) { echo "checked";} ?>>
                </label>
              </span>
              <input type="text" class="form-control" id="domus_description" name="guest_domus_description" placeholder="Domus reason (required)" hidden>
              <small id="domus_descriptionHelp" class="form-text text-muted" hidden>A brief description of why your booking is Domus</small>
            </label>
          </div>
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <?php
        if (date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($mealObject->date_cutoff)) && $_SESSION['admin'] != "true") {
          echo "<button type=\"submit\" class=\"btn btn-primary disabled\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add Guest</button>";
        } else {
          //echo "<a href=\"index.php?deleteBookingUID=" . $bookingObject->uid . "\" role=\"button\" class=\"btn btn-danger\" onclck=\"bookingDeleteButton();\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete</a>";
          echo "<button type=\"submit\" class=\"btn btn-primary " . $buttonAddDisable . "\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add Guest</button>";
        }
        ?>
      </div>
      <input type="hidden" id="bookingUID" name="bookingUID" value="<?php echo $bookingObject->uid; ?>">
      <input type="hidden" id="mealUID" name="mealUID" value="<?php echo $bookingObject->meal_uid; ?>">
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" tabindex="-1" id="staticBackdrop" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Meal Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
        if (!$mealObject->check_member_ok()) {
          $message = "<p>Your account is currently disabled.  You cannot make changes to this booking.  Please contact the Bursary for further assistance.</p>";
          $buttonStatus = "disabled";
        } elseif (!$mealObject->check_cutoff_ok() && $_SESSION['admin'] != "true") {
          $message = "<p>The deadline for making changes to this booking has passed.  Please contact the Bursary for further assistance.</p>";
          $buttonStatus = "disabled";
        } else {
          $message = "<p>Are you sure you want to delete this meal booking?  This will also delete any guests you have booked for this meal.</p>";
          $buttonStatus = "";
        }

        echo $message;
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <a href="index.php?deleteBookingUID=<?php echo $bookingObject->uid; ?>" role="button" class="btn btn-danger <?php echo $buttonStatus; ?>" onclck="bookingDeleteButton();"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#trash"/></svg> Delete</a>
      </div>
    </div>
  </div>
</div>

<script>
checkMaxCheckboxes(<?php echo $dietaryOptionsMax; ?>);

// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()
</script>
<?php
} else {
  $output  = "You have not made a booking for this meal.";
  $output .= "";
  $output .= "";

  echo $output;
}
?>
