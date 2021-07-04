<?php
include_once("../../inc/autoload.php");
$termsClass = new terms();
$currentTerm = new term();

$suppliedWeek = $_GET['id'];
$currentWeek = $termsClass->currentWeek();

$mealsClass = new meals();

// iterate through the whole week (based on weekStartDate)
for($i = 0; $i < 7; $i++){
  $date = strtotime("+$i day", strtotime($suppliedWeek));

  echo "<h2 class=\"text-center mt-3\">" . date('l', $date) . " <span class=\"text-muted\">" . date('F jS', $date) . "</span></h2>";

  echo "<div class=\"row row-cols-1 row-cols-md-3 justify-content-center\">";

  $meals = $mealsClass->allByDate(date('Y-m-d', $date));
  $meals = array_reverse($meals);

  foreach ($meals AS $meal) {
    $mealObject = new meal($meal['uid']);

    echo $mealObject->mealCard();
  }
  echo "</div>";
}

?>
