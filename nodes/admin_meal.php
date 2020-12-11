<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/js/main.js"></script>

<?php
admin_gatekeeper();
$dateFormat = $settingsClass->value('datetime_format_short');

$mealsClass = new meals();

$mealObject = new meal($_GET['mealUID']);

printArray($_POST);

?>
<?php
$title = $mealObject->name;
$subtitle = $mealObject->location . " " . date($dateFormat, strtotime($mealObject->date_meal));
//$icons[] = array("class" => "btn-danger", "name" => "Test1", "value" => "");
//$icons[] = array("class" => "btn-primary", "name" => "Test2", "value" => "");

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
            $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=booking&mealUID=" . $memberObject->uid . "\" class=\"text-muted\">" . $memberObject->displayName() . "</a></h6>";
            $output .= "<small class=\"text-muted\">" . date($dateFormat, strtotime($booking['date'])) . " " . date('H:i:s', strtotime($booking['date'])) . "</small>";
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
        <form method="post" id="mealUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
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
          </div>
          <div class="row">
            <div class="col-4">
              <label for="date_meal" class="form-label">Meal Date/Time</label>
              <input type="date" class="form-control" name="date_meal" id="date_meal" placeholder="" value="<?php echo $mealObject->date_meal; ?>" required>
              <div class="invalid-feedback">
                Meal Date is required.
              </div>
            </div>
            <div class="col-8">
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






            <div class="row">
              <div class="col-6">
                <label for="scr_capacity" class="form-label">SCR Capacity</label>
                <input type="number" class="form-control" name="scr_capacity" id="scr_capacity" placeholder="" value="<?php echo $mealObject->scr_capacity; ?>" required>
                <div class="invalid-feedback">
                  SCR Capacity is required.
                </div>
              </div>

              <div class="col-6">
                <label for="scr_guests" class="form-label">SCR Guests (per member)</label>
                <input type="number" class="form-control" name="scr_guests" id="scr_guests" placeholder="" value="<?php echo $mealObject->scr_guests; ?>" required>
                <div class="invalid-feedback">
                  SCR Guests is required.
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-6">
                <label for="mcr_capacity" class="form-label">MCR Capacity</label>
                <input type="number" class="form-control" name="mcr_capacity" id="mcr_capacity" placeholder="" value="<?php echo $mealObject->mcr_capacity; ?>" required>
                <div class="invalid-feedback">
                  SCR Capacity is required.
                </div>
              </div>

              <div class="col-6">
                <label for="mcr_guests" class="form-label">MCR Guests (per member)</label>
                <input type="number" class="form-control" name="mcr_guests" id="mcr_guests" placeholder="" value="<?php echo $mealObject->mcr_guests; ?>" required>
                <div class="invalid-feedback">
                  SCR Guests is required.
                </div>
              </div>
            </div>

            <label for="notes" class="form-label">Notes</label>
            <input type="text" class="form-control" name="notes" id="notes" placeholder="" value="<?php echo $mealObject->notes; ?>" required>
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
              <div>
                <label class="row">
                  <span class="col">Dessert</span>
                  <span class="col-auto">
                    <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" checked=""></label>
                  </span>
                </label>
              </div>
            </div>
</div>



<!--


Where:


Wine:
Dessert:

Dessert Capacity:
(0 for unlimited)
Dessert:

Deadline Time
Unbookable
(& contact):


-->

          <hr class="my-4">
          <input type="hidden" name="mealUID" id="mealUID" value="<?php echo $mealObject->uid;?>">
          <button class="btn btn-primary btn-lg btn-block" type="submit">Update Meal Details</button>
        </form>
      </div>
    </div>


<script>
var picker = new Litepicker({
  element: document.getElementById('date_meal'),
  firstDay: 0,
  format: 'YYYY-MM-DD',
  singleMode: true
});
</script>
