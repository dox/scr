<?php
include_once("../inc/autoload.php");
$termsClass = new terms();
$currentTerm = new term();

$totalMeals = 0;

$mealsClass = new meals();
$firstDayOfCurrentWeek = firstDayOfWeek();

$subject  = "SEH Menu for week commencing " . $firstDayOfCurrentWeek;

$recipients = explode(",", $settingsClass->value('menu_recipients'));

$output = "<h1>" . $subject . "</h1>";
// iterate through the whole week (based on weekStartDate)
for($i = 0; $i < 7; $i++){
  $date = strtotime("+$i day", strtotime($firstDayOfCurrentWeek));

  $output .= "<h2 class=\"text-center mt-3\">" . date('l', $date) . " <span class=\"text-muted\">" . date('F jS', $date) . "</span></h2>";


  $meals = $mealsClass->allByDate(date('Y-m-d', $date));

  foreach ($meals AS $meal) {
    if (!empty($meal->menu)) {
      $totalMeals = $totalMeals + 1;
    }

  $output .= "<p><strong>" . $meal->type . "</strong> " . $meal->menu . "</p>";
  }
}

if ($totalMeals > 0) {
  sendMail($subject, $recipients, $output);
  echo "Email sent for " . $totalMeals . " meals";
} else {
  echo "No menu data to send";
}
?>
