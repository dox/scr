<?php
// this isn't used yet!
include_once("../inc/autoload.php");

$bookingObject = new booking($_POST['bookingUID']);

$mealObject = new meal($bookingObject->meal_uid);
if (date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($mealObject->date_cutoff)) || $_SESSION['admin'] == true) {
  $bookingObject->delete();
} else {
  $logArray['category'] = "booking";
  $logArray['result'] = "danger";
  $logArray['description'] = "Error attempting to delete [bookingUID:" . $bookingObject->uid . "]";
  $logsClass->create($logArray);
}
?>
