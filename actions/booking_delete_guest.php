<?php
include_once("../inc/autoload.php");

$booking_uid = filter_var($_POST['booking_uid'], FILTER_SANITIZE_NUMBER_INT);
$guest_uid = filter_var($_POST['guest_uid'], FILTER_SANITIZE_STRING);

$bookingObject = new booking($booking_uid);
echo $guest_uid;
$allGuests = $bookingObject->deleteGuest($guest_uid);
?>
