<?php
// this isn't used yet!
include_once("../inc/autoload.php");

$uid = filter_var($_POST['bookingUID'], FILTER_SANITIZE_NUMBER_INT);

$bookingObject = new booking($uid);

$mealObject = new meal($bookingObject->meal_uid);
if (checkpoint_charlie("bookings") || date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($mealObject->date_cutoff))) {
  $bookingObject->delete();
} else {
  $logArray['category'] = "booking";
  $logArray['result'] = "danger";
  $logArray['description'] = "Error attempting to delete [bookingUID:" . $bookingObject->uid . "]";
  $logsClass->create($logArray);
}
?>
