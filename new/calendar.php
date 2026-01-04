<?php
//header('Content-type: text/calendar; charset=utf-8');

require_once "inc/autoload.php";
require_once "inc/zapcallib.php";

if (isset($_GET['hash'])) {
	$bookingsClass = new bookings();
	
	$hash = filter_input(
		INPUT_GET,
		'hash',
		FILTER_VALIDATE_REGEXP,
		[
			'options' => [
				'regexp' => '/^[a-zA-Z0-9]+$/'
			]
		]
	);
	
	$member = Member::fromHash($hash);
	if (!isset($member->uid)) {
		$log->add("iCal feed failed for hash: {$hash}", 'ical', Log::WARNING);
		die("Invalid calendar hash.");
	}
	
	$bookings = $member->bookingsBetweenDates(date('Y-m-d', strtotime('-1 year')), date('Y-m-d', strtotime('+1 year')));
	
	$icalobj = new ZCiCal();
	$tzid = "Europe/London";
	
	foreach ($bookings AS $booking) {
		$meal = new Meal($booking->meal_uid);
		
		$title = $meal->name;
		
		$event_start = date('c', strtotime($meal->date_meal));
		$event_end = date('c', strtotime("+1 hour" . $meal->date_meal));
		
		// create the event within the ical object
		$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
		
		ZCTimeZoneHelper::getTZNode(substr($event_start,0,4),substr($event_end,0,4),$tzid, $icalobj->curnode);
		
		$eventobj->addNode(new ZCiCalDataNode("TRANSP:" . "OPAQUE"));
		
		// add title
		$eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
		
		// add location
		$eventobj->addNode(new ZCiCalDataNode("LOCATION:" . $meal->location));
		
		// add start/end date
		$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
		$eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));
		
		// UID is a required item in VEVENT, create unique string for this event
		$uid = date('Y-m-d-H-i-s') . "@scr2.seh.ox.ac.uk";
		$eventobj->addNode(new ZCiCalDataNode("UID:" . $booking->uid));
		
		// DTSTAMP is a required item in VEVENT
		$eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
		
		// Add description if there are guests
		$guestArray = array();
		if (count($booking->guests()) > 0) {
			foreach ($booking->guests() AS $guest) {
				$guestArray[] = $guest['guest_name'];
			}
			$eventobj->addNode(new ZCiCalDataNode("DESCRIPTION:" . ZCiCal::formatContent(
				"Guest(s): " . implode(", ", $guestArray)
			)));
		}
		
	}
	
	// write iCalendar feed to stdout
	echo $icalobj->export();
	
	if ($settings->get('logs_ical-requests') == "true" || APP_DEBUG) {
		$log->add("iCal feed generated for: {$member->ldap}", 'ical', Log::INFO);
	}
}
?>