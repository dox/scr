<?php
include_once("../inc/autoload.php");

$booking_uid = $_POST['booking_uid'];
$guest_uid = $_POST['guest_uid'];

$bookingObject = new booking($booking_uid);

$allGuests = $bookingObject->deleteGuest($guest_uid);
?>
