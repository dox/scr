<?php
include_once("../../inc/autoload.php");
$termsClass = new terms();
$currentTerm = new term();

$suppliedWeek = $_GET['id'];
$currentWeek = $termsClass->currentWeek();

$mealsClass = new meals();

$output = "";
$noMealsCount = 0;

// iterate through the whole week (based on weekStartDate)
for($i = 0; $i < 7; $i++){
  $date = strtotime("+$i day", strtotime($suppliedWeek));
  
  $meals = $mealsClass->allByDate(date('Y-m-d', $date));
  $meals = array_reverse($meals);

  $output .= "<h2 class=\"text-center mt-3\">" . date('l', $date) . " <span class=\"text-muted\">" . date('F jS', $date) . "</span></h2>";

  $output .= "<div class=\"row row-cols-1 row-cols-md-3 justify-content-center\">";
  
  if (count($meals) > 0) {
    foreach ($meals AS $meal) {
      $mealObject = new meal($meal['uid']);
    
      $output .= $mealObject->mealCard();
    }
  } else {
    $noMealsCount++;
  }
  
  $output .= "</div>";
}

if ($noMealsCount >= 7) {
  $output = "<h2 class=\"text-center p-3\">Meals for this week are not yet available</h2>";
}

echo $output;
?>
