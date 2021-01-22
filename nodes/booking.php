<?php
$termsClass = new terms();
$membersClass = new members();

$meal = new meal($_GET['mealUID']);
$checkTerm = $termsClass->checkIsInTerm($meal->date_meal);
$term = new term($checkTerm[0]['uid']);

$bookingsClass = new bookings();
$bookingByMember = $bookingsClass->bookingForMealByMember($meal->uid, $_SESSION['username']);
$bookingObject = new booking($bookingByMember['uid']);

$dietaryOptionsMax = $settingsClass->value('meal_dietary_allowed');

if (!empty($_POST['guest_name'])) {
  $bookingObject->update($_POST);
  $bookingObject = new booking($_GET['bookingUID']);
  echo $settingsClass->alert("success", $_POST['name'], " booking updated");
}

//$bookingsThisMeal = $bookingsClass->bookings_this_meal($meal->uid);

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
$title = "Week " . $term->whichWeek($meal->date_meal) . " " . $meal->name;
$subtitle = $meal->type . ": " . $meal->location . ", " . dateDisplay($meal->date_meal, true);
if ($_SESSION['admin'] == true) {
  //$icons[] = array("class" => "btn-warning", "name" => $icon_edit. " Edit Meal", "value" => "a href=\"index.php?n=admin_meal=" . $meal->uid . "\"");
}
$icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Booking", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#staticBackdrop\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add Guest", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#modal_guest_add\"");

echo makeTitle($title, $subtitle, $icons);
?>

<div class="row g-3">
      <div class="col-md-5 col-lg-4 order-md-last">
        <div class="divide-y">
          <div>
            <label class="row">
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#graduation-cap"></svg> Domus</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch">
                  <?php
                  if ($meal->domus == 1 && $_SESSION['admin'] != true) {
                    $domusDisabledCheck = " disabled";
                  } else {
                    $domusDisabledCheck = "";
                  }
                  ?>
                  <input class="form-check-input" <?php echo $domusDisabledCheck; ?> id="domus" name="domus" type="checkbox" <?php if ($bookingObject->domus == 1) { echo "checked";} ?> onchange="domus(this.id)">
                </label>
              </span>
              <input type="text" class="form-control" id="domus_description" name="domus_description" placeholder="Domus reason (required)" hidden>
              <small id="domus_descriptionHelp" class="form-text text-muted" hidden>A brief description of why your booking is Domus</small>
            </label>
          </div>
          <div>
            <label class="row">
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#wine-glass"></svg> Wine</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch">
                  <?php
                  if ($meal->allowed_wine == 1) {
                    $wineDisabledCheck = "";
                  } else {
                    $wineDisabledCheck = " disabled";
                  }
                  ?>
                  <input class="form-check-input" <?php echo $wineDisabledCheck; ?> id="wine" name="wine" type="checkbox" <?php if ($bookingObject->wine == 1) { echo "checked";} ?>>
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
                  if ($meal->allowed_dessert == 1) {
                    $dessertDisabledCheck = "";
                  } else {
                    $dessertDisabledCheck = " disabled";
                  }
                  ?>
                  <input class="form-check-input" <?php echo $dessertDisabledCheck; ?> id="dessert" name="dessert" type="checkbox" <?php if ($bookingObject->dessert == 1) { echo "checked";} ?>>
                </label>
              </span>
            </label>
          </div>
        </div>

        <hr />

        <div id="guests_list"><?php include_once("widgets/_bookingGuestList.php"); ?></div>
      </div>
      <div class="col-md-7 col-lg-8">
        <div id="meal_guest_list"><?php include_once("widgets/_mealGuestList.php"); ?></div>

        <?php
        if (isset($meal->menu)) {
          echo "<div class=\"card text-center\">";
          echo "<div class=\"card-body\">";
          echo "<h4 class=\"card-title text-center\">Menu</h4>";
          echo "<p><i>" . $meal->location . ", " . dateDisplay($meal->date_meal, true) . "</i></p>";
          echo $meal->menu;
          echo "</div>";
          echo "</div>";
        }
        ?>
      </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="modal_guest_add" tabindex="-1" aria-labelledby="modal_guest_add" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="../actions/booking_add_guest.php" onsubmit="return addGuest(this);">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Guest</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="name">Guest Name</label>
            <input type="text" class="form-control" name="guest_name" id="guest_name" aria-describedby="termNameHelp">
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
                <input type="text" class="form-control" name="guest_domus_description" id="guest_domus_description" aria-describedby="domus_description" placeholder="Domus reason (required)">
                <small id="guest_domus_descriptionHelp" class="form-text text-muted">A brief description of why this guest is Domus</small>
              </div>

            </label>
          </div>
          <div>
            <label class="row">
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#wine-glass"></svg> Guest Wine</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch">
                  <input class="form-check-input" id="guest_wine" name="guest_wine" type="checkbox" <?php if ($bookingObject->guest_wine == 1) { echo "checked";} ?>>
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
                  <input class="form-check-input" id="guest_dessert" name="guest_dessert" type="checkbox" <?php if ($bookingObject->guest_dessert == 1) { echo "checked";} ?>>
                </label>
              </span>
              <input type="text" class="form-control" id="domus_description" name="guest_domus_description" placeholder="Domus reason (required)" hidden>
              <small id="domus_descriptionHelp" class="form-text text-muted" hidden>A brief description of why your booking is Domus</small>
            </label>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#person-plus"/></svg> Add Guest</button>
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
        if (date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($meal->date_cutoff)) && $_SESSION['admin'] != "true") {
          echo "<p>The deadline for making changes to this booking has passed.  Please contact the Bursary for further assistance.</p>";
        } else {
          echo "<p>Are you sure you want to delete this meal booking?  This will also delete any guests you have booked for this meal.</p>";
        }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <?php
        if (date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($meal->date_cutoff)) && $_SESSION['admin'] != "true") {
          echo "<a href=\"#\" role=\"button\" class=\"btn btn-danger disabled\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete</a>";
        } else {
          echo "<a href=\"index.php?deleteBookingUID=" . $bookingObject->uid . "\" role=\"button\" class=\"btn btn-danger\" onclck=\"bookingDeleteButton();\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete</a>";
        }
        ?>
      </div>
    </div>
  </div>
</div>

<script>
checkMaxCheckboxes(<?php echo $dietaryOptionsMax; ?>);
</script>
