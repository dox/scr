<?php
include_once("../inc/autoload.php");

//$_GET['mealUID'] = 7035;

$mealObject = new meal($_GET['mealUID']);

//echo "<div class=\"modal-header text-center\">";
echo "<h5 class=\"modal-title mt-3 text-center\" id=\"exampleModalLabel\">" . $mealObject->type . " Menu</h5>";
//echo "</div>";

echo "<div class=\"modal-body\">";

echo "<p class=\"text-center\"><i>" . $mealObject->location . ", " . dateDisplay($mealObject->date_meal, true) . " " . timeDisplay($mealObject->date_meal, true) . "</i></p>";

echo "<div class=\"text-center\">";
if (!empty($mealObject->menu)) {
  echo $mealObject->menu;
} else {
  echo "Menu not available";
}
echo "</div>";

echo "<hr />";

if ($mealObject->domus == 1) {
  echo "<p><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#graduation-cap\"/></svg> Meal is Domus</p>";
}
if ($mealObject->allowed_wine == 1) {
  echo "<p><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#wine-glass\"/></svg> Wine available</p>";
}
if ($mealObject->allowed_dessert == 1) {
  echo "<p><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#cookie\"/></svg> Dessert available</p>";
}

echo "</div>";
?>
