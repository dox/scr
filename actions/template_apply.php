<?php
include_once("../inc/autoload.php");

admin_gatekeeper();

//$_POST['template_start_date'] = "2021-01-24";
//$_POST['template_name'] = "template_1";

$templateName = $_POST['template_name'];
$givenStartDate = date('Y-m-d', strtotime($_POST['template_start_date'])); //must be a Sunday!

// check if givenStartDate is a Sunday (day 7 of the week);
if (date('N', strtotime($givenStartDate)) == 7) {
  $mealsToCreate = json_decode($settingsClass->value($templateName));

  echo "<h2>Given Start Date: " . date('Y-m-d', strtotime($givenStartDate)) . "</h2>";

  $i = 0;
  do {
    $dateLookup = date('Y-m-d', strtotime($givenStartDate . " + " . $i . " days"));

    echo "<h3>Checking for date " . $dateLookup . " (" . date('l', strtotime($dateLookup)) . ")</h3>";

    echo "<p>Creating " . count($mealsToCreate->$i) . " meals</p>";
    foreach ($mealsToCreate->$i AS $mealUID) {
      $mealObject = new meal($mealUID);

      if ($mealObject->template == 1) {
        $cutoffdeltaSeconds = datediff('s', $mealObject->date_cutoff, $mealObject->date_meal);
        $newMealDateTime = $dateLookup . " " . date('H:i', strtotime($mealObject->date_meal));
        $newCutOff = date('Y-m-d H:i', strtotime($newMealDateTime . "-" . $cutoffdeltaSeconds . " seconds"));

        $newMeal['name'] = $mealObject->name;
        $newMeal['type'] = $mealObject->type;
        $newMeal['date_meal'] = $newMealDateTime;
        $newMeal['date_cutoff'] = $newCutOff;
        $newMeal['location'] = $mealObject->location;
        $newMeal['domus'] = $mealObject->domus;
        $newMeal['allowed_wine'] = $mealObject->allowed_wine;
        $newMeal['allowed_dessert'] = $mealObject->allowed_dessert;
        $newMeal['scr_capacity'] = $mealObject->scr_capacity;
        $newMeal['mcr_capacity'] = $mealObject->mcr_capacity;
        $newMeal['scr_guests'] = $mealObject->scr_guests;
        $newMeal['mcr_guests'] = $mealObject->mcr_guests;
        $newMeal['scr_dessert_capacity'] = $mealObject->scr_dessert_capacity;
        $newMeal['mcr_dessert_capacity'] = $mealObject->mcr_dessert_capacity;
        $newMeal['menu'] = htmlspecialchars($mealObject->menu);
        $newMeal['notes'] = $mealObject->notes;
        $newMeal['photo'] = $mealObject->photo;

        printArray($newMeal);

        $mealObject->create($newMeal);
      } else {
        echo "ERROR!  Meal [mealUID:" . $mealUID . "] isn't a template!";
      }


    }

    echo "<hr />";

    $i++;
  } while ($i < 7);
  $logsClass->create("admin", "Template '" . $templateName . "' applied to week commencing '" . $givenStartDate . "'");
} else {
  echo "Supplied date isn't a Sunday!";
  $logsClass->create("admin", "Error!  Template '" . $templateName . "' failed to apply to week commencing '" . $givenStartDate . "'");
}

?>
