
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

$membersClass = new members();
$memberObject = new member($memberUID);
$upcomingMealUIDS = $memberObject->mealUIDS_upcoming();
$previousMealUIDS = $memberObject->mealUIDS_previous();

$dietaryOptionsMax = $settingsClass->value('meal_dietary_allowed');

if (isset($_POST['memberUID'])) {
  if (!isset($_POST['opt_in'])) {
    $_POST['opt_in'] = 0;
  }
  if (!isset($_POST['default_domus'])) {
    $_POST['default_domus'] = 0;
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

echo makeTitle($title, $subtitle);

include_once('_member_stats.php');

?>

<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Upcoming Meals</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($upcomingMealUIDS); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      foreach ($upcomingMealUIDS AS $mealUID) {
        $mealObject = new meal($mealUID);

        echo $mealObject->display_mealAside();
      }
      ?>
    </ul>

    <hr />

    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Recent Meals</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($previousMealUIDS); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      $i = 0;
      $mealsToDisplay = $settingsClass->value('member_previous_meals_displayed');
      do {
        $mealObject = new meal($previousMealUIDS[$i]);

        if (isset($mealObject->uid)) {
          echo $mealObject->display_mealAside();
        }

        $i++;
      } while($i <= $mealsToDisplay);
      ?>
    </ul>
  </div>

  <div class="col-md-7 col-lg-8">
    <h4 class="d-flex mb-3">Personal Information</h4>
    <form method="post" id="memberUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
      <div class="row g-3">
        <div class="col-md-2">
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

        <div class="col-sm-5">
          <label for="firstname" class="form-label">First name</label>
          <input type="text" class="form-control" name="firstname" id="firstname" placeholder="" value="<?php echo $memberObject->firstname; ?>" required>
          <div class="invalid-feedback">
            Valid first name is required.
          </div>
        </div>

        <div class="col-sm-5">
          <label for="lastname" class="form-label">Last name</label>
          <input type="text" class="form-control" name="lastname" id="lastname" placeholder="" value="<?php echo $memberObject->lastname; ?>" required>
          <div class="invalid-feedback">
            Valid last name is required.
          </div>
        </div>

        <div class="col-7">
          <label for="ldap" class="form-label">LDAP Username</label>
          <div class="input-group">
            <span class="input-group-text" onclick="ldapLookup()">@</span>
            <input type="text" class="form-control" name="ldap" id="ldap" placeholder="LDAP Username" value="<?php echo $memberObject->ldap; ?>" required>

            <div class="invalid-feedback">
              LDAP Username is required.
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <label for="category" class="form-label">Member Category</label>
          <select class="form-select" name="category" id="category" required>
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

        <div class="col-12">
          <label for="dietary" class="form-label">Dietary Information</label>
          <div class="selectBox" onclick="showCheckboxes()">
            <select class="form-select">
              <option>Select up to <?php echo $dietaryOptionsMax; ?> dietary preferences</option>
            </select>
            <div class="overSelect"></div>
          </div>
          <div id="checkboxes" name="dietary" id="dietary">
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

        <div class="col-12">
          <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
          <input type="email" class="form-control" name="email" id="email" placeholder="" value="<?php echo $memberObject->email; ?>">
          <div class="invalid-feedback">
            Please enter a valid email address for shipping updates.
          </div>
        </div>
        <div class="col-12">
          <label for="enabled" class="form-label">Enabled/Disabled Status</label>
          <select class="form-select" name="enabled" id="enabled" required>
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
              <span class="col"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#graduation-cap"></svg> Always Domus <small>Overrides meal setting</small></span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" id="default_domus" name="default_domus" value="1" <?php if ($memberObject->default_domus == "1") { echo " checked";} ?>></label>
              </span>
            </label>
          </div>
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

        <hr class="my-4">

        <input type="hidden" name="memberUID" id="memberUID" value="<?php echo $memberObject->uid;?>">
        <button class="btn btn-primary btn-lg btn-block" type="submit">Update Member Details</button>
      </form>
    </div>
  </div>
</div>

<script>
checkMaxCheckboxes(<?php echo $dietaryOptionsMax; ?>);
</script>
