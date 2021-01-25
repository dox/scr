<link rel="stylesheet" href="css/flatpickr.min.css">
<script src="js/flatpickr.js"></script>

<?php
admin_gatekeeper();

$termsClass = new terms();
$termObject = new term($_GET['termUID']);

$nextTerm = $termObject->nextTerm();

$mealsClass = new meals();
$meals = $mealsClass->betweenDates($termObject->date_start, $termObject->date_end);
$mealsAfterTerm = $mealsClass->betweenDates($termObject->date_end, date('Y-m-d', strtotime($nextTerm[0]['date_start'] . " -1 day")));

if (isset($_POST['termUID'])) {
  $termObject->update($_POST);
  $termObject = new term($_GET['termUID']);
}

?>
<?php
$title = $termObject->name;
$subtitle = "From " . dateDisplay($termObject->date_start) . ", to " . dateDisplay($termObject->date_end);
$icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Term", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");

echo makeTitle($title, $subtitle, $icons);
?>
<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Meals</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($meals); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      foreach ($meals AS $meal) {
        $mealObject = new meal($meal['uid']);

        $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
        $output .= "<div class=\"text-muted\">";
        $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=admin_meal&mealUID=" . $mealObject->uid . "\" class=\"text-muted\">" . $mealObject->name . "</a></h6>";
        $output .= "<small class=\"text-muted\">" . dateDisplay($mealObject->date_meal) . " " . date('H:i', strtotime($mealObject->date_meal)) . "</small>";
        $output .= "</div>";
        $output .= "<span class=\"text-muted\">" . count(json_decode($booking['guests_array'])) . autoPluralise(" guest", " guests", count(json_decode($booking['guests_array']))) . "</span>";
        $output .= "</li>";

        echo $output;
      }
      ?>
    </ul>
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Meals Post-Term (Vacation)</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($mealsAfterTerm); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      foreach ($mealsAfterTerm AS $meal) {
        $mealObject = new meal($meal['uid']);

        $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
        $output .= "<div class=\"text-muted\">";
        $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=admin_meal&mealUID=" . $mealObject->uid . "\" class=\"text-muted\">" . $mealObject->name . "</a></h6>";
        $output .= "<small class=\"text-muted\">" . dateDisplay($mealObject->date_meal) . " " . date('H:i', strtotime($mealObject->date_meal)) . "</small>";
        $output .= "</div>";
        $output .= "<span class=\"text-muted\">" . count(json_decode($booking['guests_array'])) . autoPluralise(" guest", " guests", count(json_decode($booking['guests_array']))) . "</span>";
        $output .= "</li>";

        echo $output;
      }
      ?>
    </ul>
  </div>
  <div class="col-md-7 col-lg-8">
    <h4 class="mb-3">Term Information</h4>
    <form method="post" id="termUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
      <div class="col mb-3">
        <label for="name" class="form-label">Term name</label>
        <input type="text" class="form-control" name="name" id="name" value="<?php echo $termObject->name; ?>" required>
        <small id="nameHelp" class="form-text text-muted">e.g. 'Trinity <?php echo date('Y')+1; ?>'</small>
        <div class="invalid-feedback">
          Valid term name is required.
        </div>
      </div>
      <div class="row">
        <div class="col mb-3">
          <label for="date_start" class="form-label">Term Start Date</label>
          <div class="input-group">
            <span class="input-group-text" id="date_start-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
            <input type="text" class="form-control" name="date_start" id="date_start" value="<?php echo date('Y-m-d', strtotime($termObject->date_start)); ?>" aria-describedby="date_start" required>
          </div>
          <small id="date_startHelp" class="form-text text-muted">Sunday of 1st week</small>
          <div class="invalid-feedback">
            Valid term start date is required.
          </div>
        </div>

        <div class="col mb-3">
          <label for="date_end" class="form-label">Term End Date</label>
          <div class="input-group">
            <span class="input-group-text" id="date_end-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
            <input type="text" class="form-control" name="date_end" id="date_end" value="<?php echo date('Y-m-d', strtotime($termObject->date_end)); ?>" aria-describedby="date_end" required>
          </div>
          <small id="date_endHelp" class="form-text text-muted">Saturday of 8th week</small>
          <div class="invalid-feedback">
            Valid term end date is required.
          </div>
        </div>
      </div>

      <hr class="my-4">

      <input type="hidden" name="termUID" id="termUID" value="<?php echo $termObject->uid; ?>">
      <button class="btn btn-primary w-100 btn-lg btn-block" type="submit">Update Term Details</button>
    </form>
  </div>
</div>


<!-- Modal -->
<div class="modal" tabindex="-1" id="deleteTermModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Term</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this Term?  This will not delete any meals/bookings that have been made during this term.  WARNING! This cannot be undone!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <a href="index.php?n=admin_terms&termDELETE=<?php echo $termObject->uid; ?>" role="button" class="btn btn-danger"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#trash"/></svg> Delete</a>
      </div>
    </div>
  </div>
</div>

<script>
var fp = flatpickr("#date_start", {
  dateFormat: "Y-m-d"
})

var fp = flatpickr("#date_end", {
  dateFormat: "Y-m-d"
})
</script>
