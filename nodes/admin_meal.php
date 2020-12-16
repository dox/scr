<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
admin_gatekeeper();

$mealsClass = new meals();

$mealObject = new meal($_GET['mealUID']);


if (isset($_POST['mealUID'])) {
  $mealObject->update($_POST);
  $mealObject = new meal($_GET['mealUID']);
}

?>
<?php
if (isset($_GET['add'])) {
  $title = "Add New Meal";
  $subtitle = "Add new meal - instant";
  //$icons[] = array("class" => "btn-primary", "name" => "Guest List", "value" => "");
  //$icons[] = array("class" => "btn-primary", "name" => "Test2", "value" => "");
} else {
  $title = $mealObject->name;
  $subtitle = $mealObject->location . " " . dateDisplay($mealObject->date_meal);
  $icons[] = array("class" => "btn-primary", "name" => "Guest List", "value" => "");
  //$icons[] = array("class" => "btn-primary", "name" => "Test2", "value" => "");
}


echo makeTitle($title, $subtitle, $icons);
?>
<div class="row g-3">
      <div class="col-md-5 col-lg-4 order-md-last">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">Diners Signed Up</span>
          <span class="badge bg-secondary rounded-pill"><?php echo count($mealObject->bookings_this_meal()); ?></span>
        </h4>
        <ul class="list-group mb-3">
          <?php
          foreach ($mealObject->bookings_this_meal() AS $booking) {
            $memberObject = new member($booking['member_ldap']);

            $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
            $output .= "<div class=\"text-muted\">";
            $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=booking&memberLDAP=" . $booking['member_ldap'] . "&mealUID=" . $booking['meal_uid'] . "\" class=\"text-muted\">" . $memberObject->displayName() . "</a></h6>";
            $output .= "<small class=\"text-muted\">" . dateDisplay($booking['date']) . " " . date('H:i:s', strtotime($booking['date'])) . "</small>";
            $output .= "</div>";
            $output .= "<span class=\"text-muted\">" . count(json_decode($booking['guests_array'])) . autoPluralise(" guest", " guests", count(json_decode($booking['guests_array']))) . "</span>";
            $output .= "</li>";

            echo $output;
          }
          ?>
        </ul>
      </div>
      <div class="col-md-7 col-lg-8">
        <h4 class="mb-3">Meal Information</h4>
        <?php
        if (isset($_GET['add'])) {
          echo "<form method=\"post\" id=\"mealUpdate\" action=\"index.php?n=admin_meals\" class=\"needs-validation\" novalidate>";
        } else {
          echo "<form method=\"post\" id=\"mealUpdate\" action=\"" . $_SERVER['REQUEST_URI'] . "\" class=\"needs-validation\" novalidate>";
        }
        ?>
          <div class="row">
            <div class="col-4">
              <label for="type" class="form-label">Type</label>
              <select class="form-select" name="type" id="type" required>
                <?php
                foreach ($mealsClass->mealTypes() AS $type) {
                  if ($type == $mealObject->type) {
                    $selected = " selected ";
                  } else {
                    $selected = "";
                  }
                  $output = "<option value=\"" . $type . "\"" . $selected . ">" . $type . "</option>";

                  echo $output;
                }
                ?>
              </select>
              <div class="invalid-feedback">
                Title is required.
              </div>
            </div>
            <div class="col-8">
              <label for="name" class="form-label">Meal name</label>
              <input type="text" class="form-control" name="name" id="name" placeholder="" value="<?php echo $mealObject->name; ?>" required>
              <div class="invalid-feedback">
                Valid Meal name is required.
              </div>
            </div>

            <div class="col-12">
              <label for="location" class="form-label">Location</label>
              <input type="text" list="locations_datalist" class="form-control" name="location" id="location" placeholder="" value="<?php echo $mealObject->location; ?>" required>
              <datalist id="locations_datalist">
                <?php
                foreach ($mealsClass->mealLocations() AS $location) {
                  echo "<option value=\"" . $location['location'] . "\">";
                }
                ?>
              </datalist>
              <div class="invalid-feedback">
                Location is required.
              </div>
            </div>
          </div>

          <hr />

          <?php
          if (isset($_GET['add'])) {
            $cutoffMinutes = $settingsClass->value('booking_cutoff');

            $date_meal = date('Y-m-d 12:00');
            $date_cutoff = date('Y-m-d 12:00', strtotime(date('Y-m-d') . ' -' . $cutoffMinutes . " minutes"));
          } else {
            $date_meal = date('Y-m-d H:i', strtotime($mealObject->date_meal));
            $date_cutoff = date('Y-m-d H:i', strtotime($mealObject->date_cutoff));
          }
          ?>
          <div class="row">
            <div class="col-6">
              <label for="date_meal" class="form-label">Meal Date/Time</label>
              <input type="text" class="form-control" name="date_meal" id="date_meal" placeholder="" value="<?php echo $date_meal; ?>" required>
              <div class="invalid-feedback">
                Meal Date is required.
              </div>
            </div>

            <div class="col-6">
              <label for="date_cutoff" class="form-label">Meal Date/Time Cut-Off</label>
              <input type="date" class="form-control" name="date_cutoff" id="date_cutoff" placeholder="" value="<?php echo $date_cutoff; ?>" required>
              <div class="invalid-feedback">
                Meal Cutoff Date is required.
              </div>
            </div>




          </div>
          <hr />

          <div class="row">
            <div class="col-4">
              <label for="scr_capacity" class="form-label">SCR Capacity</label>
              <input type="number" class="form-control" name="scr_capacity" id="scr_capacity" value="<?php echo $mealObject->scr_capacity; ?>" min=0 required>
              <div class="invalid-feedback">
                SCR Capacity is required.
              </div>
            </div>

            <div class="col-4">
              <label for="scr_guests" class="form-label">SCR Guests (per member)</label>
              <input type="number" class="form-control" name="scr_guests" id="scr_guests" value="<?php echo $mealObject->scr_guests; ?>" min=0 required>
              <div class="invalid-feedback">
                SCR Guests is required.
              </div>
            </div>

            <div class="col-4">
              <label for="scr_dessert_capacity" class="form-label">SCR Dessert Capacity</label>
              <input type="number" class="form-control" name="scr_dessert_capacity" id="scr_dessert_capacity" value="<?php echo $mealObject->scr_dessert_capacity; ?>" min=0 required>
              <div class="invalid-feedback">
                SCR Dessert Capacity is required.
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-4">
              <label for="mcr_capacity" class="form-label">MCR Capacity</label>
              <input type="number" class="form-control" name="mcr_capacity" id="mcr_capacity" value="<?php echo $mealObject->mcr_capacity; ?>" min=0 required>
              <div class="invalid-feedback">
                SCR Capacity is required.
              </div>
            </div>

            <div class="col-4">
              <label for="mcr_guests" class="form-label">MCR Guests (per member)</label>
              <input type="number" class="form-control" name="mcr_guests" id="mcr_guests" value="<?php echo $mealObject->mcr_guests; ?>" min=0 required>
              <div class="invalid-feedback">
                SCR Guests is required.
              </div>
            </div>

            <div class="col-4">
              <label for="mcr_dessert_capacity" class="form-label">MCR Desert Capacity</label>
              <input type="number" class="form-control" name="mcr_dessert_capacity" id="mcr_dessert_capacity" value="<?php echo $mealObject->mcr_dessert_capacity; ?>" min=0 required>
              <div class="invalid-feedback">
                MCR Dessert Capacity is required.
              </div>
            </div>
          </div>

          <hr />

          <label for="notes" class="form-label">Notes</label>
          <input type="text" class="form-control" name="notes" id="notes" value="<?php echo $mealObject->notes; ?>" required>
          <div class="invalid-feedback">
            Valid Meal name is required.
          </div>

          <hr />

          <div class="divide-y">
            <div>
              <label class="row">
                <span class="col">Domus</span>
                <span class="col-auto">
                  <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" checked=""></label>
                </span>
              </label>
            </div>
            <div>
              <label class="row">
                <span class="col">Wine</span>
                <span class="col-auto">
                  <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" checked=""></label>
                </span>
              </label>
            </div>
          </div>
        </div>
</div>
          <hr class="my-4">
          <?php
          if (isset($_GET['add'])) {
            echo "<input type=\"hidden\" name=\"mealNEW\" id=\"mealNEW\">";
            echo "<button class=\"btn btn-primary btn-lg btn-block\" type=\"submit\">Add New Meal</button>";
          } else {
            echo "<input type=\"hidden\" name=\"mealUID\" id=\"mealUID\" value=\"" . $mealObject->uid . "\">";
            echo "<button class=\"btn btn-primary btn-lg btn-block\" type=\"submit\">Update Meal Details</button>";
          }
          ?>

        </form>
      </div>
    </div>

    <script>
    var fp = flatpickr("#date_meal", {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      time_24hr: true
    })

    var fp = flatpickr("#date_cutoff", {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      time_24hr: true
    })
    </script>
