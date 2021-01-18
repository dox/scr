<?php
include_once("../inc/autoload.php");
$termsClass = new terms();
$currentTerm = new term();

$suppliedWeek = $_GET['id'];
$currentWeek = $termsClass->currentWeek();

$mealsClass = new meals();

?>

<!--<h1><?php echo "<h1>" . $suppliedWeek . " <span class=\"text-muted\">" . $currentTerm->weekStartDate($suppliedWeek) . "</span></h1>"; ?></h1>-->

<?php

// iterate through the whole week (based on weekStartDate)
for($i = 0; $i < 7; $i++){
  $date = strtotime("+$i day", strtotime($suppliedWeek));

  echo "<h2 class=\"text-center mt-3\">" . date('l', $date) . " <span class=\"text-muted\">" . date('F jS', $date) . "</span></h2>";

  echo "<div class=\"row row-cols-md-3 row-cols-sm-1 justify-content-center\">";
  $meals = $mealsClass->allByDate(date('Y-m-d', $date));
  $meals = array_reverse($meals);

  foreach ($meals AS $meal) {
    $mealObject = new meal($meal['uid']);

    echo $mealObject->mealCard();
  }
  echo "</div>";
}

?>
<!--
<div class="row row-cols-2">
  <div class="col">
<div class="card text-white">
  <img src="../img/cover.jpg" class="card-img-top" alt="...">
  <div class="card-img-overlay">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
    <p class="card-text">Last updated 3 mins ago</p>
  </div>
  <div class="card-body ">
    <h1 class="card-title pricing-card-title">1 <small class="text-muted">/ 16 bookings</small></h1>
    <ul class="list-unstyled">
      <li>Wolfson Hall</li>
      <li>8am - 9.30am</li>
      <li>Collection from Wolfson Hall</li>
    </ul>

  </div>
  <div class="card-status-bottom bg-danger"></div>

</div>
</div>
<div class="col">

<div class="card bg-dark text-white">
  <img src="../img/cover.jpg" class="card-img" alt="...">
  <div class="card-img-overlay">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
    <p class="card-text">Last updated 3 mins ago</p>
  </div>
  <div class="card-status-bottom bg-danger"></div>
</div>
</div>
</div>

<style>

.border-primary2 {
	background: linear-gradient(45deg,#4099ff,#73b4ff);
}
</style>
-->
