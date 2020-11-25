<?php
include_once("../inc/autoload.php");
$termsClass = new terms();
$currentTerm = new term();

$suppliedWeek = $_GET['id'];
$currentWeek = $termsClass->currentWeek();

$mealsClass = new meals();

$datetimeFormatShort = $settingsClass->value('datetime_format_short');
?>

<h1><?php echo "<h1>Week " . $suppliedWeek . " <span class=\"text-muted\">" . $currentTerm->weekStartDate($suppliedWeek) . "</span></h1>"; ?></h1>

<?php
// iterate through the whole week (based on weekStartDate)
for($i = 0; $i < 7; $i++){
  $date = strtotime("+$i day", strtotime($currentTerm->weekStartDate($suppliedWeek)));

  echo "<h2>" . date('l', $date) . "</h2>";
  echo "<div class=\"row row-cols-1 row-cols-md-3 mb-3 text-center\">";
  $meals = $mealsClass->allByDate(date('Y-m-d', $date));

  foreach ($meals AS $meal) {
    $mealObject = new meal($meal['uid']);

    echo $mealObject->mealCard();
  }
  echo "</div>";
}

?>

<div class="card border-primary2 mb-4 shadow-sm"><div class="card-header"><h4 class="my-0 font-weight-normal">Test</h4></div><div class="card-body "><h1 class="card-title pricing-card-title">1 <small class="text-muted">/ 16 bookings</small></h1><ul class="list-unstyled mt-3 mb-4"><li>Wolfson Hall</li><li>8am - 9.30am</li><li>Collection from Wolfson Hall</li></ul><div class="btn-group"><button type="button" id="mealUID-1" class="btn btn-outline-primary" onclick="bookMealQuick(this.id)">Book Meal</button><button type="button" id="mealUID_dropdown-1" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false"><span class="visually-hidden">Toggle Dropdown</span></button><ul class="dropdown-menu"><li><a class="dropdown-item" href="index.php?n=booking&amp;bookingUID=1">Manage Booking</a></li><li><a class="dropdown-item" href="#">Manage Meal</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item" href="#">Cancel Booking</a></li></ul></div></div></div>

<style>

.border-primary2 {
	background: linear-gradient(45deg,#4099ff,#73b4ff);
}
</style>
