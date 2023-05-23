<?php

$sql = "SELECT * FROM `bookings` WHERE dessert = 0 AND guests_array IS NOT NULL ORDER BY uid DESC";

$bookings = $db->query($sql)->fetchAll();


foreach ($bookings AS $booking) {
	$booking = new booking($booking['uid']);
		
	foreach ($booking->guestsArray() AS $guest) {
		$guest = json_decode($guest);
		
		if ($guest->guest_dessert == "on") {
			printArray($guest);
			
			$sql = "UPDATE bookings SET dessert = '1' WHERE uid = '" . $booking->uid . "' LIMIT 1;";
			echo $sql;
			$db->query($sql);
		}
	
		
		
	}
	
}

?>