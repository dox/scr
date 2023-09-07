<?php

$sql = "SELECT * FROM `meals` ORDER BY date_meal DESC";

$meals = $db->query($sql)->fetchAll();


foreach ($meals AS $meal) {
	$sql = "SELECT member_ldap , COUNT(*) AS total FROM `bookings` WHERE meal_uid = " . $meal['uid'] . " GROUP BY member_ldap";
	
	$bookings = $db->query($sql)->fetchAll();

	foreach ($bookings AS $booking) {
		if ($booking['total'] > 1) {
			$bookingObject = new booking($booking['uid']);
			
			echo "<p>" . $meal['uid'] . " - " . $booking['member_ldap'] . " has " . $booking['total'] . " bookings " . "</p>";
			
			//if () {
				
			//}
		}
	
		
		
	}
	
}

?>