<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
pageAccessCheck("meals");

$mealsClass = new meals();

$mealObject = new meal($_GET['mealUID']);


if (isset($_POST['mealUID'])) {
  if (isset($_POST['allowed'])) {
    $_POST['allowed'] = implode(",", $_POST['allowed']);
  } else {
    $_POST['allowed'] = null;
  }
  
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
  $icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person\"/></svg> Guest List", "value" => "onclick=\"window.open('guestlist.php?mealUID=" . $mealObject->uid . "')\"");
}

echo makeTitle($title, $subtitle, $icons, true);

?>

<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span>Diners Signed Up</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($mealObject->bookings_this_meal()); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      
      $currentMembersBooked = array();
      
      foreach ($mealObject->bookings_this_meal() AS $booking) {
        $memberObject = new member($booking['member_ldap']);

        $guests = (array)json_decode($booking['guests_array']);

        $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
        $output .= "<div class=\"text-muted\">";
        $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\" class=\"text-muted\">" . $memberObject->displayName() . "</a></h6>";
        $output .= "<small class=\"text-muted\">" . dateDisplay($booking['date']) . " " . date('H:i:s', strtotime($booking['date'])) . "</small>";
        $output .= "</div>";

        if (count($guests) > 0) {
          $output .= "<span class=\"text-muted\">+" . count($guests) . autoPluralise(" guest", " guests", count($guests)) . "</span>";
        }

        $output .= "</li>";

        echo $output;
        
        $currentMembersBooked[] = $memberObject->ldap;
      }
      ?>
      <li class="list-group-item d-flex justify-content-between lh-sm">
        <div class="input-group">
          <input class="form-control" type="text" id='quick-add-member' list='members-list' placeholder="Quick Add Member" aria-label="Quick Add Member">
          <button type="submit" id="quick-add-member-submit" onclick='quickAdd()' name="quick-add-member-submit" class="btn btn-primary"> Add</button>
        </div>
      </li>
    </ul>
    <a href="report.php?reportUID=9&mealUID=<?php echo $mealObject->uid; ?>" class="text-muted float-end">Export all meal bookings</a>
    
    <div id="chart-meals_by_day"></div>
    
  </div>
  <div class="col-md-7 col-lg-8">
    <h4 class="mb-3">Meal Information</h4>
    <?php
    if (isset($_GET['add'])) {
      echo "<form method=\"post\" id=\"mealUpdate\" action=\"index.php?n=admin_meals\" class=\"needs-validation\" >";
    } else {
      echo "<form method=\"post\" id=\"mealUpdate\" action=\"" . $_SERVER['REQUEST_URI'] . "\" class=\"needs-validation\" >";
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
    
    <div class="row">
      <div class="col">
        
        <div class="accordion" id="accordionAllowed">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              Allowed Groups
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionAllowed">
            <div class="accordion-body">
              <strong>Select none for everyone to be allowed, otherwise only those member types selected can book this meal</strong>
              
              <?php
              $memberTypes = explode(",", $settingsClass->value('member_categories'));
              $mealTypesAllowed = explode(",", $mealObject->allowed);
              
              $i = 0;
              foreach ($memberTypes AS $memberType) {
                if (in_array($memberType, $mealTypesAllowed)) {
                  $checked = " checked";
                } else {
                  $checked = "";
                }
                $output  = "<div class=\"form-check\">";
                $output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $memberType . "\" name=\"allowed[]\" id=\"flexCheckDefault_" . $i . "\" " . $checked . ">";
                $output .= "<label class=\"form-check-label\" for=\"flexCheckDefault_" . $i . "\">" . $memberType . "</label>";
                $output .= "</div>";
                
                $i++;
                
                echo $output;
                
              }
              ?>
            </div>
          </div>
        </div>
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
        <label for="mcr_dessert_capacity" class="form-label">MCR Dessert Capacity</label>
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
        <textarea rows="4" class="form-control" name="menu" id="menu"><?php echo $mealObject->menu; ?></textarea>
      </div>
    </div>

    <hr />

    <div class="mb-3">
      <label for="notes" class="form-label">Notes (Private)</label>
        <textarea rows="4" class="form-control" name="notes" id="notes"><?php echo $mealObject->notes; ?></textarea>
    </div>

    <div class="mb-3">
      <label for="photo" class="form-label">Photo</label>
      
      <div class="row row-cols-1 row-cols-lg-3">
        <?php
        foreach ($mealsClass->mealCardImages() AS $photo) {
          if ($photo == $mealObject->photo) {
            $selected = " checked ";
          } else {
            $selected = "";
          }
          
          $output  = "<div class=\"col\">";
          $output .= "<div class=\"card mb-3\">";
          $output .= "<img src=\"img/cards/" . $photo . "\" class=\"card-img-top\" alt=\"...\">";
          $output .= "<div class=\"card-body\">";
          $output .= "<p class=\"card-text\"><label for=\"photo-" . $photo . "\" class=\"form-label\">";
          $output .= "<input class=\"form-check-input\" type=\"radio\" name=\"photo\" id=\"photo-" . $photo . "\" value=\"" . $photo . "\"" . $selected . "> ";
          $output .= $photo . "</label></p>";
          $output .= "</div>";
          $output .= "</div>";
          $output .= "</div>";
          
          echo $output;
        }
        ?>
      </div>
    </div>

    <hr />
    
    <div class="mb-3">
      <label for="type" class="form-label">Default Charge Meal To</label>
      <select class="form-select" name="charge_to" id="charge_to">
        <?php
        $charge_to_options = explode(",", $settingsClass->value('booking_charge-to'));

        foreach ($charge_to_options AS $option) {
          if ($option == $mealObject->charge_to) {
            $selected = " selected ";
          } else {
            $selected = "";
          }
          $output = "<option value=\"" . $option . "\"" . $selected . ">" . $option . "</option>";

          echo $output;
        }
        ?>
      </select>
      <div id="scr_charge_toHelp" class="form-text">* wine charged via Battels</div>
    </div>
    
    
    <hr />

    <div class="divide-y">
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
        echo "<button class=\"btn btn-primary w-100\" type=\"submit\">Add New Meal</button>";
      } else {
        echo "<input type=\"hidden\" name=\"mealUID\" id=\"mealUID\" value=\"" . $mealObject->uid . "\">";
        echo "<button class=\"btn btn-primary w-100\" type=\"submit\">Update Meal Details</button>";
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
        <input type="text" class="form-control" id="delete_confirm" placeholder="Type 'DELETE' to confirm" onkeyup="deleteMealInputCheck()">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <a href="index.php?n=admin_meals&mealDELETE=<?php echo $mealObject->uid; ?>" role="button" id="delete_meal_button" class="btn btn-danger disabled"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#trash"/></svg> Delete</a>
      </div>
    </div>
  </div>
</div>

<script>
function deleteMealInputCheck() {
  var input = document.getElementById("delete_confirm").value;
  var delete_button = document.getElementById("delete_meal_button");

  if (input == 'DELETE') {
    delete_button.classList.remove("disabled");
  } else {
    delete_button.classList.add("disabled");
  }
}

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
</script>



<?php
$chartReadingsArray = array();
foreach ($mealObject->bookings_this_meal() AS $booking) {
  $date = date('Y-m-d', strtotime($booking['date']));
  
  $bookingTotals[$date] = $bookingTotals[$date] + 1;
  $chartReadingsArray[strtotime($date)*1000] = "[" . (strtotime($date)*1000) . "," . $bookingTotals[$date] . "]";
}
?>
<script>
var options = {
  chart: {
    type: 'bar',
    height: '200px',
    toolbar: {
      show: false
    },
    zoom: {
      enabled: false,
    },
    background: 'transparent'
  },
  grid: {
    show: false
  },
  dataLabels: {
    enabled: false
  },
  series: [{
    name: "Total bookings by day",
    data: [<?php echo implode (",", $chartReadingsArray); ?>]
  }],
  xaxis: {
    type: 'datetime',
  },
  tooltip: {
    x: {
      format: 'yyyy MMM dd'
    }
  }
}

var chart = new ApexCharts(document.querySelector("#chart-meals_by_day"), options);

chart.render();
</script>


<datalist id="members-list">
  <?php
  $membersClass = new members();
  $members = $membersClass->allEnabled();

  foreach ($members AS $member) {
    $memberObject = new member($member['uid']);
    
    if (!in_array($memberObject->ldap, $currentMembersBooked)) {
      echo "<option id=\"" . $memberObject->ldap . "\" value=\"" . $memberObject->displayName() . "\"></option>";
    }
    
  }
  ?>
</datalist>



<script>
function quickAdd() {
  var buttonClicked = document.getElementById('quick-add-member-submit');
  var impersonateInput = document.getElementById('quick-add-member');

  var val = document.getElementById("quick-add-member").value;

  var opts = document.getElementById('members-list').childNodes;
  
  for (var i = 0; i < opts.length; i++) {
    if (opts[i].value === val) {
      var formData = new FormData();
      formData.append("meal_uid", "<?php echo $mealObject->uid; ?>");
      formData.append("member_ldap", opts[i].id);
      
      var request = new XMLHttpRequest();

      request.open("POST", "../actions/booking_create.php", true);
      request.send(formData);

      // 4. This will be called after the response is received
      request.onload = function() {
        if (request.status != 200) { // analyze HTTP status of the response
          alert(`Something went wrong. ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
        } else { // show the result
          location.reload();
          //alert(`Done, got ${request.response.length} bytes`); // response is the server response
        }
      };

      request.onerror = function() {
        alert("Request failed");
      };


      break;
    }
  }
}
</script>