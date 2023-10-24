<?php
//header('Content-type: text/calendar; charset=utf-8');

require_once("inc/zapcallib.php");
include_once("inc/autoload.php");

$bookingsClass = new bookings();
$memberObject = new member(filter_var($_GET['hash'], FILTER_SANITIZE_STRING));
$bookingUIDS = $memberObject->getAllBookingUIDS();

$membersClass = new members();
foreach ($membersClass->all(999) AS $member) {
	$sql = "UPDATE members SET calendar_hash = '" . crypt(strtolower($member['ldap']), salt) . "' WHERE ldap = '" . $member['ldap'] . "'";
	$db->query($sql);
}

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
	
	// add title
	$eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
	
	// add start/ed date
	$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
	$eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));
	
	// UID is a required item in VEVENT, create unique string for this event
	$uid = date('Y-m-d-H-i-s') . "@scr2.seh.ox.ac.uk";
	$eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
	
	// DTSTAMP is a required item in VEVENT
	$eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
	
	// Add description
	//$eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent(
			"This is a simple event, using the Zap Calendar PHP library.")));
}

// write iCalendar feed to stdout
echo ($icalobj->export());

?>