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
				<div class="card-title">Diners</div>
				<div class="card-text <?= ($meal->totalDiners() > $meal->scr_capacity) ? 'text-danger' : 'text-muted"'; ?>"><h4><?= $totalDiners . " (" . $totalBookings . " +" . $guests . autoPluralise(" guest)", " guests)", $guests) ?></h4></div>
			</div>
		</div>
	</div>
	<div class="col mb-3">
		<div class="card">
			<div class="card-body">
				<?php
				$totalDessertDiners = $meal->totalDessertDiners();
				?>
				<div class="card-title">Dessert</div>
				<div class="card-text <?= ($meal->totalDessertDiners() > $meal->scr_dessert_capacity) ? 'text-danger' : 'text-muted"'; ?>"><h4><?= $totalDessertDiners ?></h4></div>
			</div>
		</div>
	</div>
</div>

<h4 class="d-flex justify-content-between align-items-center mb-3">Guest List</h4>

<table class="table">
	<thead>
		<tr>
			<th scope="col" width="5%">#</th>
			<th scope="col">Name</th>
			<th scope="col" width="3%">
				<svg class="bi text-muted" width="1em" height="1em" aria-hidden="true"><use xlink:href="assets/images/icons.svg#wine-glass"></use></svg>
			</th>
			<th scope="col" width="3%">
				<i class="bi bi-cookie text-muted"></i>
			</th>
			<th scope="col" width="3%">
				<i class="bi bi-mortarboard text-muted"></i>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 1;
		foreach ($meal->bookings() AS $booking) {
		  $member = Member::fromLDAP($booking->member_ldap);
		  
		  $output  = "<tr>";
		  $output .= "<th scope=\"row\" rowspan=\"" . count($booking->guests()) + 1 . "\">" . $i . "</th>";
		  $output .= "<td>";
			$output .= "<strong>" . $member->name() . "</strong>";
			if (!empty($member->dietary)) {
			  $dietaryArray = explode(",", $member->dietary);
			  
			  $output .= "<br>" . implode(", ", $dietaryArray);
			}
		  $output .= "</td>";
		  
		  $output .= "<td>";
			if ($booking->wineChoice() != "None") {
				$output .= "
				<svg class=\"bi\" width=\"1em\" height=\"1em\" aria-hidden=\"true\"
				data-bs-toggle=\"popover\"
				data-bs-title=\"Wine Choice\"
				data-bs-trigger=\"hover focus\"
				data-bs-content=\"" . htmlspecialchars($booking->wineChoice()) . "\"
				><use xlink:href=\"assets/images/icons.svg#wine-glass\"></use></svg>
				";
			}
		  $output .= "</td>";
		  $output .= "<td>";
			if ($booking->dessertChoice() == "1") {
				$output .= "<i class=\"bi bi-cookie\"></i>";
			}
		  $output .= "</td>";
		  $output .= "<td>";
			if ($booking->charge_to != "Dining Entitlement") {
				$output .= "<i
					class=\"bi bi-mortarboard\"
					data-bs-toggle=\"popover\"
					data-bs-title=\"" . htmlspecialchars($booking->charge_to) . "\"
					data-bs-trigger=\"hover focus\"
					data-bs-content=\"" . htmlspecialchars($booking->domus_reason) . "\"></i>
				";
			}
		  $output .= "</td>";
		  $output .= "</tr>";
		  
		  
		  if (!empty($booking->guests())) {
			foreach ($booking->guests() as $guest) {
			  $output .= "<tr>";
			  
			  $output .= "<td>";
				$output .= " + <strong>" . $guest['guest_name'] . "</strong>";
				
				// Dietary info (if any)
				$guestDietary = $guest['guest_dietary'] ?? '';
				if (is_string($guestDietary)) {
					$guestDietary = array_map('trim', explode(',', $guestDietary));
				}
				if (is_array($guestDietary) && !empty(array_filter($guestDietary))) {
					$output .= '<br>' . htmlspecialchars(implode(', ', array_filter($guestDietary)));
				}
			  $output .= "</td>";
			  $output .= "<td>";
			  	if ($meal->allowed_wine == "1") {
					  if (!empty($guest['guest_wine_choice']) && $guest['guest_wine_choice'] !== 'None') {
						  $output .= "
						  <svg class=\"bi\" width=\"1em\" height=\"1em\" aria-hidden=\"true\"
						  data-bs-toggle=\"popover\"
						  data-bs-title=\"Guest Wine Choice\"
						  data-bs-trigger=\"hover focus\"
						  data-bs-content=\"" . htmlspecialchars($guest['guest_wine_choice']) . "\"
						  ><use xlink:href=\"assets/images/icons.svg#wine-glass\"></use></svg>
						  ";
					  }
				}
			  $output .= "</td>";
			  $output .= "<td>";
				if ($booking->dessert == "1") {
					$output .= "<i class=\"bi bi-cookie\"></i>";
				}
			  $output .= "</td>";
			  $output .= "<td>";
				if ($guest['guest_charge_to'] == "Domus") {
					$output .= "<i
						class=\"bi bi-mortarboard\"
						data-bs-toggle=\"popover\"
						data-bs-title=\"" . htmlspecialchars($guest['guest_charge_to']) . "\"
						data-bs-trigger=\"hover focus\"
						data-bs-content=\"" . htmlspecialchars($guest['domus_reason']) . "\"></i>
					";
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

<script>
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
</script>

<style>
	h2 .bi {
		font-size: 2rem; /* Or whatever size you want */
		vertical-align: middle; /* Keeps them aligned */
	}
</style>