<?php
// Get all bookings where member_ldap does not exist in members table
$query = "
	SELECT b.*
	FROM bookings b
	LEFT JOIN members m ON b.member_ldap = m.ldap
	WHERE m.ldap IS NULL
";
$rows = $db->fetchAll($query);

$orphanedBookings = [];
$totalOrphanedBookings = 0;
// Tally late bookings per member
foreach ($rows as $row) {
	if (!isset($orphanedBookings[$row['member_ldap']])) {
		$orphanedBookings[$row['member_ldap']][] = $row;
	}
	$orphanedBookings[$row['member_ldap']][] = $row;
	
	$totalOrphanedBookings++;
}

// Sort array by the number of sub-items (descending)
uasort($orphanedBookings, function($a, $b) {
	return count($b) <=> count($a); // descending order
});

echo pageTitle(
	'Orphaned Bookers',
	$totalOrphanedBookings . ' bookings belonging to ' . count($orphanedBookings) . ' unknown members.'
);
?>

<h2 class="h4 mb-3">Late Bookings by Meal</h2>
	<p class="text-muted small mt-2">Orphaned bookings are defined as bookings that do not correlate to an existing member.
</p>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th scope="col">Booking Name</th>
				<th scope="col" class="text-end"># Bookings</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($orphanedBookings as $member_ldap => $orphanedBookingList) {
				//$meal = new Meal($orphanedBooking['meal_uid']);
				
				$output  = "<tr>";
				$output .= "<td>" . $member_ldap . "</td>";
				$output .= "<td class=\"text-end\"><span class=\"badge rounded-pill text-bg-warning\">" . count($orphanedBookingList) . "</span></td>";
				$output .= "</tr>";
				
				echo $output;
			}
			?>
		</tbody>
	</table>
</div>
