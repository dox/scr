<?php
include_once("../inc/autoload.php");
$termsClass = new terms();
$currentTerm = new term();

$totalMeals = 0;

$mealsClass = new meals();
$firstDayOfCurrentWeek = firstDayOfWeek();

$subject  = "SEH Menu for week commencing " . $firstDayOfCurrentWeek;

$output = "<h1>" . $subject . "</h1>";
// iterate through the whole week (based on weekStartDate)
for($i = 0; $i < 7; $i++){
  $date = strtotime("+$i day", strtotime($firstDayOfCurrentWeek));

  $output .= "<h2 class=\"text-center mt-3\">" . date('l', $date) . " <span class=\"text-muted\">" . date('F jS', $date) . "</span></h2>";


  $meals = $mealsClass->allByDate(date('Y-m-d', $date));
  $meals = array_reverse($meals);

  foreach ($meals AS $meal) {
    if (!empty($mealObject->menu)) {
      $totalMeals = $totalMeals + 1;
    }
    
  $mealObject = new meal($meal['uid']);

  $output .= "<p><strong>" . $mealObject->type . "</strong> " . $mealObject->menu . "</p>";
  }
}

$headers  = "From: " . smtp_sender_address . "\r\n";
$headers .= "Reply-To: " . smtp_sender_address . "\r\n";

$recipients = explode(",", $settingsClass->value('menu_recipients'));
foreach ($recipients AS $recipient) {
  $headers .= "CC: " . $recipient . "\r\n";
}

$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if ($totalMeals > 0) {
  mail($to, $subject, $output, $headers);
  echo "Email sent for " . $totalMeals . " meals";
} else {
  echo "No menu data to send";
}
?>
