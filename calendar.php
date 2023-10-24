<?php
//header('Content-type: text/calendar; charset=utf-8');

include_once("inc/autoload.php");
require_once("inc/zapcallib.php");

$bookingsClass = new bookings();
$memberObject = new member(filter_var($_GET['hash'], FILTER_SANITIZE_STRING));
$bookingUIDS = $memberObject->getAllBookingUIDS();

$icalobj = new ZCiCal();

foreach ($bookingUIDS AS $bookingUID) {
	$bookingObject = new booking($bookingUID);
	$mealObject = new meal($bookingObject->meal_uid);
	
	//$id = $bookingObject->uid;
	
	$title = $mealObject->name;
	
	$event_start = date('c', strtotime($mealObject->date_meal));
	$event_end = date('c', strtotime("+1 hour" . $mealObject->date_meal));
	
	// create the event within the ical object
	$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
	$eventobj->addNode(new ZCiCalDataNode("TRANSP:" . "OPAQUE"));
	
	// add title
	$eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
	
	// add location
	$eventobj->addNode(new ZCiCalDataNode("LOCATION:" . $mealObject->location));
	
	// add start/end date
	$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
	$eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));
	
	// UID is a required item in VEVENT, create unique string for this event
	$uid = date('Y-m-d-H-i-s') . "@scr2.seh.ox.ac.uk";
	$eventobj->addNode(new ZCiCalDataNode("UID:" . $bookingObject->uid));
	
	// DTSTAMP is a required item in VEVENT
	$eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
	
	// Add description
	$eventobj->addNode(new ZCiCalDataNode("DESCRIPTION:" . ZCiCal::formatContent(
		"Charged to: " . $bookingObject->charge_to . ", " . 
		count($bookingObject->guestsArray()) . " guest(s)"
	)));
}

// write iCalendar feed to stdout
echo ($icalobj->export());

?>