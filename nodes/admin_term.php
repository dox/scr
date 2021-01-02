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
$subtitle = $termObject->date_start . " - " . dateDisplay($termObject->date_end);

echo makeTitle($title, $subtitle);
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
      <div class="row">
        <div class="col mb-3">
          <label for="name" class="form-label">Term name</label>
          <input type="text" class="form-control" name="name" id="name" value="<?php echo $termObject->name; ?>" required>
          <div class="invalid-feedback">
            Valid term name is required.
          </div>
        </div>
      <div class="row">
        <div class="col mb-3">
          <label for="date_start" class="form-label">Term Start Date</label>
          <input type="text" class="form-control" name="date_start" id="date_start" value="<?php echo date('Y-m-d', strtotime($termObject->date_start)); ?>" required>
          <div class="invalid-feedback">
            Valid term start date is required.
          </div>
        </div>

        <div class="col mb-3">
          <label for="date_end" class="form-label">Term End Date</label>
          <input type="date" class="form-control" name="date_end" id="date_end" placeholder="" value="<?php echo date('Y-m-d', strtotime($termObject->date_end)); ?>" required>
          <div class="invalid-feedback">
            Valid term end date is required.
          </div>
        </div>
      </div>
    </div>

    <hr class="my-4">

    <input type="hidden" name="termUID" id="termUID" value="<?php echo $termObject->uid; ?>">
    <button class="btn btn-primary btn-lg btn-block" type="submit">Update Term Details</button>
  </form>
</div>


<script>
var fp = flatpickr("#date_start", {
  dateFormat: "Y-m-d"
})

var fp = flatpickr("#date_end", {
  dateFormat: "Y-m-d"
})
</script>
