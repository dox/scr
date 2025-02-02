<?php
//header('Content-type: text/calendar; charset=utf-8');

if (isset($_GET['hash'])) {
	include_once("inc/autoload.php");
	require_once("inc/zapcallib.php");
	
	$bookingsClass = new bookings();
	
	$sql  = "SELECT * FROM members WHERE calendar_hash = '" . filter_var($_GET['hash'], FILTER_SANITIZE_STRING) . "'";
	$sql .= " LIMIT 1";
	$member = $db->query($sql)->fetchAll();
	
	$sql  = "SELECT bookings.uid AS uid FROM bookings";
	$sql .= " LEFT JOIN meals ON bookings.meal_uid = meals.uid";
	$sql .= " WHERE bookings.member_ldap = '" . $member[0]['ldap'] . "'";
	$sql .= " ORDER BY meals.date_meal DESC";
	$sql .= " LIMIT 1000";
	
	$bookings = $db->query($sql)->fetchAll();
	
	foreach ($bookings AS $booking) {
	  $bookingUIDS[] = $booking['uid'];
	}
	
	$icalobj = new ZCiCal();
	$tzid = "Europe/London";
	
	foreach ($bookingUIDS AS $bookingUID) {
		$bookingObject = new booking($bookingUID);
		$mealObject = new meal($bookingObject->meal_uid);
		
		$title = $mealObject->name;
		
		$event_start = date('c', strtotime($mealObject->date_meal));
		$event_end = date('c', strtotime("+1 hour" . $mealObject->date_meal));
		
		// create the event within the ical object
		$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
		
		ZCTimeZoneHelper::getTZNode(substr($event_start,0,4),substr($event_end,0,4),$tzid, $icalobj->curnode);
		
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
		
		// Add description if there are guests
		$guestArray = array();
		if (count($bookingObject->guestsArray()) > 0) {
			foreach ($bookingObject->guestsArray() AS $guest) {
				$guest = json_decode($guest);
				$guestArray[] = $guest->guest_name;
			}
			$eventobj->addNode(new ZCiCalDataNode("DESCRIPTION:" . ZCiCal::formatContent(
				"Guest(s): " . implode(", ", $guestArray)
			)));
		}
		
	}
	
	// write iCalendar feed to stdout
	echo ($icalobj->export());
	
	if ($settingsClass->value('logs_ical-requests') == "true" || debug) {
		$logArray['category'] = "calendar";
		$logArray['result'] = "success";
		$logArray['description'] = "iCal feed generated for " . $member[0]['ldap'];
		$logsClass->create($logArray);
	}
}
?>