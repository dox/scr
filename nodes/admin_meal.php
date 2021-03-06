<link rel="stylesheet" href="css/flatpickr.min.css">
<script src="js/flatpickr.js"></script>
<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>

<?php
admin_gatekeeper();

$mealsClass = new meals();

$mealObject = new meal($_GET['mealUID']);


if (isset($_POST['mealUID'])) {
  if (!isset($_POST['domus'])) {
    $_POST['domus'] = '0';
  }
  if (!isset($_POST['allowed_wine'])) {
    $_POST['allowed_wine'] = '0';
  }
  if (!isset($_POST['allowed_dessert'])) {
    $_POST['allowed_dessert'] = '0';
  }
  if (!isset($_POST['template'])) {
    $_POST['template'] = '0';
  }
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
  $icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Meal", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
  $icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Guest List", "value" => "onclick=\"window.open('guestlist.php?mealUID=" . $mealObject->uid . "')\"");
}


echo makeTitle($title, $subtitle, $icons);
?>
<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span>Diners Signed Up</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($mealObject->bookings_this_meal()); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      foreach ($mealObject->bookings_this_meal() AS $booking) {
        $memberObject = new member($booking['member_ldap']);

        $guests = (array)json_decode($booking['guests_array']);

        $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
        $output .= "<div class=\"text-muted\">";
        $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\" class=\"text-muted\">" . $memberObject->displayName() . "</a></h6>";
        $output .= "<small class=\"text-muted\">" . dateDisplay($booking['date']) . " " . date('H:i:s', strtotime($booking['date'])) . "</small>";
        $output .= "</div>";
        $output .= "<span class=\"text-muted\">" . count($guests) . autoPluralise(" guest", " guests", count($guests)) . "</span>";
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
      <div class="col-4 mb-3">
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
      <div class="col-8 mb-3">
        <label for="name" class="form-label">Meal name</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="" value="<?php echo $mealObject->name; ?>" required>
        <div class="invalid-feedback">
          Valid Meal name is required.
        </div>
      </div>
      <div class="col-12 mb-3">
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
      $defaultCutoffMins = $settingsClass->value('meal_default_cutoff');

      $date_meal = date('Y-m-d' . ' 12:00');
      $date_cutoff = date('Y-m-d H:i', strtotime($date_meal . ' -' . $defaultCutoffMins . " minutes"));
      $capacitySCR = 0;
      $capacitySCRGuests = 0;
      $capacitySCRDessert = 0;
      $capacityMCR = 0;
      $capacityMCRGuests = 0;
      $capacityMCRDessert = 0;
    } else {
      $date_meal = date('Y-m-d H:i', strtotime($mealObject->date_meal));
      $date_cutoff = date('Y-m-d H:i', strtotime($mealObject->date_cutoff));
      $capacitySCR = $mealObject->scr_capacity;
      $capacitySCRGuests = $mealObject->scr_guests;
      $capacitySCRDessert = $mealObject->scr_dessert_capacity;
      $capacityMCR = $mealObject->mcr_capacity;
      $capacityMCRGuests = $mealObject->mcr_guests;
      $capacityMCRDessert = $mealObject->mcr_dessert_capacity;
    }
    ?>

    <div class="row">
      <div class="col-6 mb-3">
        <label for="date_meal" class="form-label">Meal Date/Time</label>
        <div class="input-group">
          <span class="input-group-text" id="date_meal-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
          <input type="text" class="form-control" name="date_meal" id="date_meal" placeholder="" value="<?php echo $date_meal; ?>" aria-describedby="date_meal-addon" required>
        </div>
        <div class="invalid-feedback">
          Meal Date is required.
        </div>
      </div>

      <div class="col-6 mb-3">
        <label for="date_cutoff" class="form-label">Meal Date/Time Cut-Off</label>
        <div class="input-group">
          <span class="input-group-text" id="date_cutoff-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
          <input type="text" class="form-control" name="date_cutoff" id="date_cutoff" placeholder="" value="<?php echo $date_cutoff; ?>" aria-describedby="date_cutoff-addon" required>
        </div>
        <div class="invalid-feedback">
          Meal Cutoff Date is required.
        </div>
      </div>
    </div>

    <hr />

    <div class="row mb-3">
      <div class="col">
        <label for="scr_capacity" class="form-label">SCR Capacity</label>
        <input type="number" class="form-control" name="scr_capacity" id="scr_capacity" value="<?php echo $capacitySCR; ?>" min=0 required>
        <div class="invalid-feedback">
          SCR Capacity is required.
        </div>
      </div>

      <div class="col mb-3">
        <label for="scr_dessert_capacity" class="form-label">SCR Dessert Capacity</label>
        <input type="number" class="form-control" name="scr_dessert_capacity" id="scr_dessert_capacity" value="<?php echo $capacitySCRDessert; ?>" min=0 required>
        <div class="invalid-feedback">
          SCR Dessert Capacity is required.
        </div>
      </div>

      <div class="col mb-3">
        <label for="scr_guests" class="form-label">SCR Guests</label>
        <input type="number" class="form-control" name="scr_guests" id="scr_guests" value="<?php echo $capacitySCRGuests; ?>" min=0 required>
        <div id="scr_guestsHelp" class="form-text">Per member</div>
        <div class="invalid-feedback">
          SCR Guests is required.
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col mb-3">
        <label for="mcr_capacity" class="form-label">MCR Capacity</label>
        <input type="number" class="form-control" name="mcr_capacity" id="mcr_capacity" value="<?php echo $capacityMCR; ?>" min=0 required>
        <div class="invalid-feedback">
          SCR Capacity is required.
        </div>
      </div>

      <div class="col mb-3">
        <label for="mcr_dessert_capacity" class="form-label">MCR Desert Capacity</label>
        <input type="number" class="form-control" name="mcr_dessert_capacity" id="mcr_dessert_capacity" value="<?php echo $capacityMCRDessert; ?>" min=0 required>
        <div class="invalid-feedback">
          MCR Dessert Capacity is required.
        </div>
      </div>

      <div class="col mb-3">
        <label for="mcr_guests" class="form-label">MCR Guests</label>
        <input type="number" class="form-control" name="mcr_guests" id="mcr_guests" value="<?php echo $capacityMCRGuests; ?>" min=0 required>
        <div id="mcr_guestsHelp" class="form-text">Per member</div>
        <div class="invalid-feedback">
          SCR Guests is required.
        </div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col">
        <label for="menu" class="form-label">Menu</label>
        <input type="text" class="form-control" name="menu" id="menu" value="<?php echo htmlspecialchars($mealObject->menu, ENT_QUOTES); ?>">
      </div>
    </div>


    <hr />

    <div class="mb-3">
      <label for="notes" class="form-label">Notes (Private)</label>
      <input type="text" class="form-control" name="notes" id="notes" value="<?php echo $mealObject->notes; ?>">
    </div>

    <div class="mb-3">
      <label for="type" class="form-label">Photo</label>
      <select class="form-select" name="photo" id="photo">
        <option value=""></option>
        <?php
        $photos = explode(",", $settingsClass->value('meal_photos'));

        foreach ($photos AS $photo) {
          if ($photo == $mealObject->photo) {
            $selected = " selected ";
          } else {
            $selected = "";
          }
          $output = "<option value=\"" . $photo . "\"" . $selected . ">" . $photo . "</option>";

          echo $output;
        }
        ?>
      </select>
    </div>

    <hr />

    <div class="divide-y">
      <div>
        <label class="row">
          <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#graduation-cap"></svg> Domus (for all members)</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" name="domus" <?php if ($mealObject->domus == 1) { echo "checked=\"\""; } ?> value="1"></label>
          </span>
        </label>
      </div>
      <div>
        <label class="row">
          <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#wine-glass"></svg> Wine Available</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" name="allowed_wine" <?php if ($mealObject->allowed_wine == 1) { echo "checked=\"\""; } ?> value="1"></label>
          </span>
        </label>
      </div>
      <div>
        <label class="row mb-3">
          <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#cookie"></svg> Dessert Available</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" name="allowed_dessert" <?php if ($mealObject->allowed_dessert == 1) { echo "checked=\"\""; } ?> value="1"></label>
          </span>
        </label>
      </div>
      <hr />
      <div>
        <label class="row">
          <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#bullseye"></svg> Template</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" name="template" <?php if ($mealObject->template == 1) { echo "checked=\"\""; } ?> value="1"></label>
          </span>
        </label>
      </div>
      <hr />
      <?php
      if (isset($_GET['add'])) {
        echo "<input type=\"hidden\" name=\"mealNEW\" id=\"mealNEW\">";
        echo "<button class=\"btn btn-primary btn-lg w-100\" type=\"submit\">Add New Meal</button>";
      } else {
        echo "<input type=\"hidden\" name=\"mealUID\" id=\"mealUID\" value=\"" . $mealObject->uid . "\">";
        echo "<button class=\"btn btn-primary btn-lg w-100\" type=\"submit\">Update Meal Details</button>";
      }
      ?>
    </div>
  </div>
  </form>
</div>


<!-- Modal -->
<div class="modal" tabindex="-1" id="deleteMealModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Meal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this meal?  This will also delete all associated bookings (members will not be notified).</p>
        <p class="text-danger"><strong>WARNING!</strong> This action cannot be undone!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <a href="index.php?n=admin_meals&mealDELETE=<?php echo $mealObject->uid; ?>" role="button" class="btn btn-danger"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#trash"/></svg> Delete</a>
      </div>
    </div>
  </div>
</div>

<script>
var fp = flatpickr("#date_meal", {
  enableTime: true,
  time_24hr: true,
  onChange: function(selectedDates, dateStr, instance) {
    var d=new Date(selectedDates);
    var diff = <?php echo $settingsClass->value('meal_default_cutoff');?>;

    var newDateObj = new Date(d.getTime() - diff*60000);
    fp2.setDate(newDateObj)
  }
})

var fp2 = flatpickr("#date_cutoff", {
  enableTime: true,
  time_24hr: true
})

function test(date) {
  var date = '<?php echo date('Y-m-d', strtotime(selectedDates)); ?>';
  return date;
}
</script>

<script>
const editor = SUNEDITOR.create(document.getElementById('menu'),{
  "height":200,
  "buttonList": [
		[
			"formatBlock",
			"bold",
			"underline",
			"italic",
			"strike",
			"fontColor",
			"hiliteColor",
			"removeFormat",
      "align",
			"horizontalRule",
			"link",
			"fullScreen",
			"codeView",
		]
	]
});

window.addEventListener("click", function(event) {
  document.getElementById('menu').value = editor.getContents();
});
</script>
