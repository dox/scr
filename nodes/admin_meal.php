<?php
admin_gatekeeper();

$mealsClass = new meals();

$mealObject = new meal($_GET['mealUID']);


?>
<div class="container">
  <div class="px-3 py-3 pt-md-5 pb-md-4 text-center">
    <h1 class="display-4"><?php echo $mealObject->name; ?></h1>
    <p class="lead"><?php echo $mealObject->date_meal; ?></p>
  </div>

  <main>
    <div class="row g-3">
      <div class="col-md-5 col-lg-4 order-md-last">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">Timings</span>
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
          <li class="list-group-item d-flex justify-content-between">
            <span>Total (USD)</span>
            <strong>$20</strong>
          </li>
        </ul>

        <hr />

        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">Meal Information</span>
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
          <li class="list-group-item d-flex justify-content-between">
            <span>Total (USD)</span>
            <strong>$20</strong>
          </li>
        </ul>
      </div>
      <div class="col-md-7 col-lg-8">
        <h4 class="mb-3">Meal Information</h4>
        <form method="post" id="memberUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-md-2">
              <label for="title" class="form-label">Type</label>
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

            <div class="col-12">
              <label for="ldap" class="form-label">LDAP Username</label>
              <div class="input-group">
                <span class="input-group-text">@</span>
                <input type="text" class="form-control" name="ldap" id="ldap" placeholder="LDAP Username" value="<?php echo $memberObject->ldap; ?>" required>
              <div class="invalid-feedback">
                  Your username is required.
                </div>
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
              <label for="dietary" class="form-label">Dietary Information</label>
              <input type="text" class="form-control" name="dietary" id="dietary" placeholder="">
            </div>


Fellows Only:
Where:
When:
Notes:
MCR Guests:
Wine:
Dessert:
SCR Capacity:
(0 for unlimited)
Dessert Capacity:
(0 for unlimited)
Dessert:
Meal Time
Term:
Week:
Day:
Time:	 :
Deadline Time
Unbookable
(& contact):
No guest bookings:
Term:
Week:
Day:
Time:	 :



          </div>

          <hr class="my-4">
          <input type="hidden" name="memberUID" id="memberUID" value="<?php echo $memberObject->uid;?>">
          <button class="btn btn-primary btn-lg btn-block" type="submit">Update Member Details</button>
        </form>
      </div>
    </div>
  </main>
