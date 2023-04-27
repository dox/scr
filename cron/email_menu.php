<?php
include_once("../inc/autoload.php");
$termsClass = new terms();
$currentTerm = new term();

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
	$mealObject = new meal($meal['uid']);

	$output .= "<p><strong>" . $mealObject->type . "</strong> " . $mealObject->menu . "</p>";
  }
}

$to = "andrew.breakspear@seh.ox.ac.uk";


$headers  = "From: " . strip_tags($_POST['req-email']) . "\r\n";
$headers .= "Reply-To: " . strip_tags($_POST['req-email']) . "\r\n";
$headers .= "CC: ryan.trehearne@seh.ox.ac.uk\r\n";
$headers .= "CC: stephen.lloyd@seh.ox.ac.uk\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$message = '<p><strong>This is strong text</strong> while this is not.</p>';


mail($to, $subject, $output, $headers);


?>
