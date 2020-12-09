<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/js/main.js"></script>

<?php
admin_gatekeeper();

$mealsClass = new meals();

$mealObject = new meal($_GET['mealUID']);

printArray($_POST);

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







Where:


Wine:
Dessert:

Dessert Capacity:
(0 for unlimited)
Dessert:

Deadline Time
Unbookable
(& contact):




          <hr class="my-4">
          <input type="hidden" name="mealUID" id="mealUID" value="<?php echo $mealObject->uid;?>">
          <button class="btn btn-primary btn-lg btn-block" type="submit">Update Meal Details</button>
        </form>
      </div>
    </div>
  </main>


<script>
var picker = new Litepicker({
  element: document.getElementById('date_meal'),
  firstDay: 0,
  format: 'YYYY-MM-DD',
  singleMode: true
});
</script>
