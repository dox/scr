<?php
include_once("../inc/autoload.php");

$bookingObject = new booking($_POST['bookingUID']);

$mealObject = new meal($bookingObject->meal_uid);
if (date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($mealObject->date_cutoff))) {
  $bookingObject->delete();
} else {
  if ($_SESSION['admin'] == true) {
    $bookingObject->delete();
  }
}
?>
