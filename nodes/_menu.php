<?php
include_once("../inc/autoload.php");

//$_GET['mealUID'] = 7035;

$mealObject = new meal($_GET['mealUID']);

if (isset($mealObject->menu)) {
  echo $mealObject->menu;
} else {
  echo "Menu not available";
}

echo "<hr />";

if ($mealObject->allowed_domus == 1) {
  echo "<p><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#graduation-cap\"/></svg> Domus available</p>";
}
if ($mealObject->allowed_wine == 1) {
  echo "<p><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#wine-glass\"/></svg> Wine available</p>";
}
if ($mealObject->allowed_dessert == 1) {
  echo "<p><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#cookie\"/></svg> Dessert available</p>";
}
?>
