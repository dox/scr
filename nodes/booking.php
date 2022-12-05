<?php
$bookingsClass = new bookings();
$mealObject = new meal($_GET['mealUID']);
$memberObject = new member($_SESSION['username']);

$bookingByMember = $bookingsClass->bookingForMealByMember($mealObject->uid, $_SESSION['username']);
$bookingObject = new booking($bookingByMember['uid']);

if (isset($_POST['bookingUID'])) {
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

if (isset($_POST['guest_name'])) {
  $bookingObject->update($_POST);
  $bookingObject = new booking($_GET['bookingUID']);
}
?>

<?php
$title = "Week " . $term->whichWeek($mealObject->date_meal) . " " . $mealObject->name;
$subtitle = $mealObject->type . ": " . $mealObject->location . ", " . dateDisplay($mealObject->date_meal, true);
if (isset($bookingByMember)) {
  $icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Booking", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#staticBackdrop\"");
  $icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add Guest", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#modalGuestAdd\" onclick=\"addGuestModal('" . $bookingObject->uid . "')\"");
}

echo makeTitle($title, $subtitle, $icons);

if (isset($bookingByMember)) {
?>
  <form method="post" id="bookingUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="row needs-validation" novalidate>
    <div class="col-md-5 col-lg-4 order-md-last">
      <div class="divide-y">
        <label for="charge_to" class="form-label">Charge To</label>
        <select class="form-select mb-3" id="charge_to" name="charge_to" aria-label="Charge To">
          <?php
          $chargeToOptions = explode(",", $settingsClass->value('booking_charge-to'));
          
          foreach ($chargeToOptions AS $chargeToOption) {
            $selected = "";
            if ($bookingObject->charge_to == $chargeToOption) {
              $selected = " selected";
              //$dontSelectAgain = true;
            } else {
              $selected = "";
            }
            
            $output = "<option " . $selected . " value=\"" . $chargeToOption . "\">" . $chargeToOption . "</option>";
            
            echo $output;
          }
          
          // show/hide the domus_reason text box:
          if ($bookingObject->charge_to == "Battels" || $bookingObject->charge_to == "Dining Entitlement") {
            $domusVisual = " visually-hidden";
          } else {
            $domusVisual = "";
          }
          ?>
        </select>
        
        <input class="form-control mb-3 <?php echo $domusVisual; ?>" type="text" id="domus_reason" name="domus_reason" placeholder="Domus Reason (required)" aria-label="Domus Reason (required)" value="<?php echo $bookingObject->domus_reason; ?>" <?php if ($bookingObject->charge_to == "Domus") { echo " required";} ?>>
        
        <div>
          <label class="row">
            <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#wine-glass"></svg> Wine (charged via Battels)</span>
            <span class="col-auto">
              <label class="form-check form-check-single form-switch">
                <?php
                if ($mealObject->allowed_wine == 1 && $mealObject->check_cutoff_ok(true)) {
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
            <?php
            $dessertHelper = "";
            $dessertChecked = "";
            $dessertDisabledCheck = " disabled";
            
            if ($bookingObject->dessert == 1) {
              // we're already booked on for dessert
              $dessertChecked = "checked";
              
              if ($mealObject->allowed_dessert == 1 && $mealObject->check_meal_bookable(true)) {
                $dessertDisabledCheck = "";
              } else {
                $dessertHelper = "Deadline passed";
              }
              
              // check if we have guests with dessert, if so, don't let this box be unticked!
              foreach ($bookingObject->guestsArray() AS $guest) {
                $guest = json_decode($guest);
                
                if ($guest->guest_dessert == "on") {
                  $dessertHelper = "(your guests are having dessert)";
                  $dessertDisabledCheck = " disabled";
                }
              }
              
              
            } else {
              // we're not yet booked on for dessert
              if ($mealObject->allowed_dessert == 1 && $mealObject->check_meal_bookable(true)) {
                $dessertDisabledCheck = "";
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
            <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#cookie"></svg> Dessert <?php echo $dessertHelper; ?></span>
            <span class="col-auto">
              <label class="form-check form-check-single form-switch">
                <input class="form-check-input" <?php echo $dessertDisabledCheck; ?> id="dessert" name="dessert" value="1" type="checkbox" <?php echo $dessertChecked; ?>>
              </label>
            </span>
          </label>
        </div>
        <button type="submit" class="btn btn-sm w-100 btn-primary <?php if (!$mealObject->check_cutoff_ok(true)) { echo " disabled"; }?>">Update Booking Preferences</button>
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
<div class="modal" tabindex="-1" id="staticBackdrop" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Meal Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
        if (!$mealObject->check_member_ok(true)) {
          $message = "<p>Your account is currently disabled.  You cannot make changes to this booking.  Please contact the Bursary for further assistance.</p>";
          $buttonStatus = "disabled";
        } elseif (!$mealObject->check_cutoff_ok(true)) {
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


<div class="modal fade" id="modalGuestAdd" tabindex="-1" aria-labelledby="modalGuestAdd" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add/Modify Guest Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="menuContentDiv"></div>
    </div>
  </div>
</div>

<script>
// logic for charge_to select
const element = document.getElementById("charge_to");
const domus_reason = document.getElementById("domus_reason");

element.addEventListener("change", (e) => {
  const value = e.target.value;
  const text = element.options[element.selectedIndex].text;
 
  if (value == "Domus" || value == "Entertainment Allowance") {
    // Domus/Entertainment sleected
    // show the domus_reason text box
    domus_reason.required = true;
    domus_reason.value = "";
    domus_reason.className = 'form-control mb-3';
  } else {
    // Battels selected
    // hide the domus_reason text box
    domus_reason.value = "";
    domus_reason.required = false;
    domus_reason.className = 'form-control mb-3 visually-hidden';
  }
});
</script>