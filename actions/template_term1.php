<?php
include_once("../inc/autoload.php");

admin_gatekeeper();

$templateName = $_GET['templateName'];
$templateName = "template_1";

$givenStartDate = $_GET['startDate']; //must be a Sunday!
$givenStartDate = '2021-01-17'; //must be a Sunday!

$mealsToCreate = json_decode($settingsClass->value($templateName));

echo "<h2>Given Start Date: " . $givenStartDate . "</h2>";

$i = 0;
do {
  $dateLookup = date('Y-m-d', strtotime($givenStartDate . " + " . $i . " days"));

  echo "<h3>Checking for date " . $dateLookup . " (" . date('l', strtotime($dateLookup)) . ")</h3>";

  echo "<p>Creating " . count($mealsToCreate->$i) . " meals</p>";
  foreach ($mealsToCreate->$i AS $mealUID) {
    $mealObject = new meal($mealUID);

    $cutoffdeltaSeconds = datediff('s', $mealObject->date_cutoff, $mealObject->date_meal);
    $newMealDateTime = $dateLookup . " " . date('H:i', strtotime($mealObject->date_meal));
    $newCutOff = date('Y-m-d H:i', strtotime($newMealDateTime . "-" . $cutoffdeltaSeconds . " seconds"));

    $newMeal['name'] = $mealObject->name;
    $newMeal['type'] = $mealObject->type;
    $newMeal['date_meal'] = $newMealDateTime;
    $newMeal['date_cutoff'] = $newCutOff;
    $newMeal['location'] = $mealObject->location;
    $newMeal['allowed_domus'] = $mealObject->allowed_domus;
    $newMeal['allowed_wine'] = $mealObject->allowed_wine;
    $newMeal['allowed_dessert'] = $mealObject->allowed_dessert;
    $newMeal['scr_capacity'] = $mealObject->scr_capacity;
    $newMeal['mcr_capacity'] = $mealObject->mcr_capacity;
    $newMeal['scr_guests'] = $mealObject->scr_guests;
    $newMeal['mcr_guests'] = $mealObject->mcr_guests;
    $newMeal['scr_dessert_capacity'] = $mealObject->scr_dessert_capacity;
    $newMeal['mcr_dessert_capacity'] = $mealObject->mcr_dessert_capacity;
    $newMeal['menu'] = $mealObject->menu;
    $newMeal['notes'] = $mealObject->notes;
    $newMeal['photo'] = $mealObject->photo;

    printArray($newMeal);
  }

  echo "<hr />";

  $i++;
} while ($i < 7);
?>
