<?php
include_once("../inc/autoload.php");

$bookingObject = new booking($_POST['bookingUID']);

$bookingObject->delete();

?>
