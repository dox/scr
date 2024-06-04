<div class="row">
	<div class="col">
		<h1 class="mb-4">Meals, and those booking late</h1>
		<?php
		$mealsClass = new meals();
		
		$allMeals = $mealsClass->all(100);
		
		$membersLate = array();
		
		foreach ($allMeals AS $meal) {
			$meal = new meal($meal['uid']);
			
			$bookings = $meal->bookings_this_meal();
			
			$bookingLateArray = array();
			
			foreach ($bookings AS $booking) {
				$dateDiff = datediff('n', $meal->date_cutoff, $booking['date']);
				
				if ($booking['date'] > $meal->date_cutoff) {
					$bookingLateArray[] = $booking['member_ldap'] . " booking was " . $dateDiff . " minutes late";
					$membersLate[$booking['member_ldap']] = $membersLate[$booking['member_ldap']] + 1;
				}
			}
			
			if (!empty($bookingLateArray)) {
				$url = "index.php?n=admin_meal&mealUID=" . $meal->uid;
				
				echo "<h3><a href=\"" . $url . "\">" . $meal->name . "</a> <i>(" . dateDisplay($meal->date_meal) . ")</i></h3>";
				
				echo implode("<br />", $bookingLateArray);
				echo "<br /><br />";
			}
		}
		?>
	</div>
	<div class="col">
		<h1 class="mb-4">Most Frequently Late</h1>
		<?php
		arsort($membersLate);
		echo "<ul class=\"list-unstyled\">";
		foreach ($membersLate AS $member => $value) {
			$member = new member($member);
			
			$url = "index.php?n=member&memberUID=" . $member->uid;
			echo "<li><span class=\"badge rounded-pill text-bg-warning\">" . $value . "</span> <a href=\"" . $url . "\">" . $member->displayName() . "</a></li>";
		}
		echo "</ul>";
		?>
	</div>
</div>

<?php
$logArray['category'] = "report";
$logArray['result'] = "success";
$logArray['description'] = "[reportUID:" . $report['uid'] . "] run";
$logsClass->create($logArray);
?>