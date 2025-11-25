<?php
$user->pageCheck('meals');

$mealUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);

$meal = new Meal($mealUID);

echo pageTitle(
	$meal->name(),
	formatDate($meal->date_meal) . ", " . $meal->location
);
?>

<div class="row mb-3">
	<div class="col mb-3">
		<div class="card">
			<div class="card-body">
				<?php
				$totalDiners = $meal->totalDiners();
				$totalBookings = count($meal->bookings());
				$guests = $totalDiners - $totalBookings;
				?>
				<div class="card-title"><h2>SCR Diners</h2></div>
				<div class="card-text text-muted"><h4><?= $totalDiners . " (" . $totalBookings . " +" . $guests . " guests)" ?></h4></div>
			</div>
		</div>
	</div>
	<div class="col mb-3">
		<div class="card">
			<div class="card-body">
				<?php
				$totalDessertDiners = $meal->totalDessertDiners();
				?>
				<div class="card-title"><h2>SCR Dessert</h2></div>
				<div class="card-text text-muted"><h4><?= $totalDessertDiners ?></h4></div>
			</div>
		</div>
	</div>
</div>

<h4 class="d-flex justify-content-between align-items-center mb-3">Guest List</h4>

<table class="table">
	<thead>
		<tr>
			<th scope="col" width="2em">#</th>
			<th scope="col">Name</th>
			<th scope="col" width="2em">
				<svg width="2em" height="2em" class="mx-1 text-muted">
					<use xlink:href="img/icons.svg#wine-glass"/>
				</svg>
			</th>
			<th scope="col" width="2em">
				<svg width="2em" height="2em" class="mx-1 text-muted">
					<use xlink:href="img/icons.svg#cookie"/>
				</svg>
			</th>
			<th scope="col" width="2em">
				<svg width="2em" height="2em" class="mx-1 text-muted">
					<use xlink:href="img/icons.svg#graduation-cap"/>
				</svg>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		foreach ($meal->bookings() AS $booking) {
		  $member = Member::fromLDAP($booking->member_ldap);
		  
		  $output  = "<tr>";
		  $output .= "<th scope=\"row\" rowspan=\"" . count($booking->guests()) + 1 . "\"><h5>" . $i . "</h5></th>";
		  $output .= "<td>";
			$output .= "<h4>" . $member->name() . "</h4>";
			if (!empty($member->dietary)) {
			  $dietaryArray = explode(",", $member->dietary);
			  
			  $output .= implode(", ", $dietaryArray);
			}
		  $output .= "</td>";
		  
		  $output .= "<td>";
			if ($booking->wine != "None") {
			  $output .= "<svg width=\"2em\" height=\"2em\"><use xlink:href=\"assets/images/icons.svg#wine-glass\"/></svg>";
			}
		  $output .= "</td>";
		  $output .= "<td>";
			if ($booking->dessert == "1") {
			  $output .= "<i class=\"bi bi-cookie\" style=\"font-size: 2em\"></i>";
			}
		  $output .= "</td>";
		  $output .= "<td>";
			if ($booking->charge_to != "Dining Entitlement") {
			  $output .= "<i class=\"bi bi-mortarboard\" style=\"font-size: 2em\"></i>";
			}
		  $output .= "</td>";
		  $output .= "</tr>";
		  
		  
		  if (!empty($booking->guests())) {
			foreach ($booking->guests() as $guest) {
			  $output .= "<tr>";
			  
			  $output .= "<td>";
				$output .= "<h5> + " . $guest['guest_name'] . "</h5>";
				if (!empty($guest['guest_dietary'])) {
				  $output .= implode(", ", $guest['guest_dietary']);
				}
			  $output .= "</td>";
			  $output .= "<td>";
				if ($guest['guest_wine_choice'] != "None" && $guest['guest_wine_choice'] != "") {
				  $output .= "<svg width=\"2em\" height=\"2em\"><use xlink:href=\"assets/images/icons.svg#wine-glass\"/></svg>";
				}
			  $output .= "</td>";
			  $output .= "<td>";
				if ($booking->dessert == "1") {
				  $output .= "<i class=\"bi bi-cookie\" style=\"font-size: 2em\"></i>";
				}
			  $output .= "</td>";
			  $output .= "<td>";
				if ($guest['guest_charge_to'] != "Dining Entitlement") {
				  $output .= "<i class=\"bi bi-mortarboard\" style=\"font-size: 2em\"></i>";
				}
			  $output .= "</td>";
			  
			  $output .= "</tr>";
			}
		  }
		  
		  echo $output;
		  
		  $i++;
		}
		?>
	</tbody>
</table>

<p><em>Guest List generated on <?= formatDate(date('c')) . " " . formatTime(date('c')); ?> by <?= $user->getUsername(); ?></em></p>