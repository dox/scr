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

if (isset($_POST['memberUID'])) {
  if (!isset($_POST['opt_in'])) {
    $_POST['opt_in'] = 0;
  }
  $memberObject->update($_POST);
  $memberObject = new member($memberUID);
}
?>
<?php
$title = $memberObject->displayName();
$subtitle = $memberObject->type . $memberObject->memberBadge();
$icons[] = array("class" => "btn-danger", "name" => "Test1", "value" => "");
$icons[] = array("class" => "btn-primary", "name" => "Test2", "value" => "");

echo ALTmakeTitle($title, $subtitle, $icons);
?>

<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Upcoming Meals</span>
      <span class="badge bg-secondary rounded-pill">3</span>
    </h4>
    <ul class="list-group mb-3">
      <li class="list-group-item d-flex justify-content-between lh-sm">
        <div>
          <h6 class="my-0">Product name</h6>
          <small class="text-muted">Brief description</small>
        </div>
        <span class="text-muted">$12</span>
      </li>
      <li class="list-group-item d-flex justify-content-between lh-sm">
        <div>
          <h6 class="my-0">Second product</h6>
          <small class="text-muted">Brief description</small>
        </div>
        <span class="text-muted">$8</span>
      </li>
      <li class="list-group-item d-flex justify-content-between lh-sm">
        <div>
          <h6 class="my-0">Third item</h6>
          <small class="text-muted">Brief description</small>
        </div>
        <span class="text-muted">$5</span>
      </li>
      <li class="list-group-item d-flex justify-content-between bg-light">
        <div class="text-success">
          <h6 class="my-0">Promo code</h6>
          <small>EXAMPLECODE</small>
        </div>
        <span class="text-success">−$5</span>
      </li>
    </ul>

    <hr />

    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Previous Meals</span>
      <span class="badge bg-secondary rounded-pill">3</span>
    </h4>
    <ul class="list-group mb-3">
      <li class="list-group-item d-flex justify-content-between">
        <span><a href="index.php?n=member_detailed&memberUID=<?php echo $memberObject->uid; ?>">View all</a></span>
      </li>
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
            <span class="input-group-text">@</span>
            <input type="text" class="form-control" name="ldap" id="ldap" placeholder="LDAP Username" value="<?php echo $memberObject->ldap; ?>" required>
            <div class="invalid-feedback">
              LDAP Username is required.
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <label for="type" class="form-label">Member Type</label>
          <select class="form-select" name="type" id="type" required>
            <?php
            foreach ($membersClass->memberTypes() AS $type) {
              if ($type == $memberObject->type) {
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
            Please select a valid Member Type.
          </div>
        </div>

        <div class="col-12">
          <label for="dietary" class="form-label">Dietary Information</label>
          <input type="text" class="form-control" name="dietary" id="dietary" placeholder="">
        </div>

        <div class="col-12">
          <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
          <input type="email" class="form-control" name="email" id="email" placeholder="" value="<?php echo $memberObject->email; ?>">
          <div class="invalid-feedback">
            Please enter a valid email address for shipping updates.
          </div>
        </div>

        <hr class="my-4">

        <div class="divide-y">
          <div>
            <label class="row">
              <span class="col">Default Domus</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" checked=""></label>
              </span>
            </label>
          </div>
          <div>
            <label class="row">
              <span class="col">Default Wine</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" checked=""></label>
              </span>
            </label>
          </div>
          <div>
            <label class="row">
              <span class="col">Default Dessert</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" checked=""></label>
              </span>
            </label>
          </div>
        </div>

        <hr />

        <div class="input-icon">
          <input id="calendar-time" type="text" value="2020-06-20" class="form-control flatpickr-input active" placeholder="Select a date" readonly="readonly">
          <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><rect x="4" y="5" width="16" height="16" rx="2"></rect><line x1="16" y1="3" x2="16" y2="7"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="4" y1="11" x2="20" y2="11"></line><line x1="11" y1="15" x2="12" y2="15"></line><line x1="12" y1="15" x2="12" y2="18"></line></svg>
          </span>
        </div>

        <hr />

        <h4 class="mb-3">Privacy</h4>

        <div class="my-3">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="opt_in" name="opt_in" value="1" <?php if ($memberObject->opt_in == "1") { echo " checked";} ?>>
            <label class="form-check-label" for="opt_in">Allow my name to appear on dining lists (also applies to my guests)</label>
          </div>
        </div>

        <div class="row gy-3">
          <div class="col-md-6">
            <label for="cc-name" class="form-label">Name on card</label>
            <input type="text" class="form-control" id="cc-name" placeholder="" required>
            <small class="text-muted">Full name as displayed on card</small>
            <div class="invalid-feedback">
              Name on card is required
            </div>
          </div>

          <div class="col-md-6">
            <label for="cc-number" class="form-label">Credit card number</label>
            <input type="text" class="form-control" id="cc-number" placeholder="" required>
            <div class="invalid-feedback">
              Credit card number is required
            </div>
          </div>

          <div class="col-md-3">
            <label for="cc-expiration" class="form-label">Expiration</label>
            <input type="text" class="form-control" id="cc-expiration" placeholder="" required>
            <div class="invalid-feedback">
              Expiration date required
            </div>
          </div>

          <div class="col-md-3">
            <label for="cc-cvv" class="form-label">CVV</label>
            <input type="text" class="form-control" id="cc-cvv" placeholder="" required>
            <div class="invalid-feedback">
              Security code required
            </div>
          </div>
        </div>

        <hr class="my-4">

        <input type="hidden" name="memberUID" id="memberUID" value="<?php echo $memberObject->uid;?>">
        <button class="btn btn-primary btn-lg btn-block" type="submit">Update Member Details</button>
      </form>
    </div>
  </div>
</div>
