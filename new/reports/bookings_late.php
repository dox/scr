<?php
// Get date range from POST
$start = filter_input(INPUT_POST, 'from_date', FILTER_DEFAULT);
$end   = filter_input(INPUT_POST, 'to_date', FILTER_DEFAULT);

if (!$start || !$end) {
	die('Invalid or missing date range.');
}

// Optional: enforce YYYY-MM-DD format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) ||
	!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
	die('Invalid date format.');
}

// Get meals in date range
$mealsObj = new Meals();
$meals = $mealsObj->betweenDates($start, $end);

echo pageTitle(
	'Late Bookers',
	'Summary of late meal bookings across all meals and members.'
);
?>

<div class="row">
	<div class="col-md-7 col-lg-8">
		<h2 class="h4 mb-3">Late Bookings by Meal</h2>
		<p class="text-muted small mt-2">Late bookings are defined as bookings made after the advertised cutoff time.
		</p>
		
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th scope="col">Meal Date</th>
						<th scope="col">Meal</th>
						<th scope="col" class="text-end">Late Bookings</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$lateBookers = [];
					
					foreach ($meals as $meal) {
						$lateBookings = $meal->late_bookings();
						
						// Only show meals with late bookings
						if (count($lateBookings) === 0) {
							continue; // skip meals with no late bookings
						}
						
						// Tally late bookings per member
						foreach ($lateBookings as $booking) {
							if (!isset($lateBookers[$booking->member_ldap])) {
								$lateBookers[$booking->member_ldap] = 0;
							}
							$lateBookers[$booking->member_ldap]++;
						}
						
						$output  = "<tr>";
						$output .= "<td>" . formatDate($meal->date_meal, 'short') . "</td>";
						$output .= "<td><a href=\"index.php?page=meal&uid=" . $meal->uid . "\">" . $meal->name() . "</a></td>";
						$output .= "<td class=\"text-end\"><span class=\"badge rounded-pill text-bg-warning\">" . count($meal->late_bookings()) . "</span></td>";
						$output .= "</tr>";
						
						echo $output;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-5 col-lg-4">
		<h2 class="h4 mb-3">Members with Late Bookings</h2>
		
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th scope="col">Member</th>
						<th scope="col" class="text-end">Late Bookings</th>
					</tr>
				</thead>
				<tbody>
					<?php
					// Sort late bookers by number of late bookings, descending
					arsort($lateBookers);
					
					foreach ($lateBookers as $lateBookerLDAP => $count) {
						$member = Member::fromLDAP($lateBookerLDAP);
						
						$output  = "<tr>";
						$output .= "<td><a href=\"index.php?page=member&ldap=" . $member->ldap . "\">" . $member->name() . "</a></td>";
						$output .= "<td class=\"text-end\"><span class=\"badge rounded-pill text-bg-warning\">" . $count . "</span></td>";
						$output .= "</tr>";
						
						echo $output;
					}
					?>
				</tbody>
			</table>
		</div>
		
		
	</div>
</div>
