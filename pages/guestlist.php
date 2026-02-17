<?php
$user->pageCheck('meals');

$mealUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);

$meal = new Meal($mealUID);

$memberTypes = array_map('trim', explode(',', $settings->get('member_types')));

echo pageTitle(
	$meal->name(),
	formatDate($meal->date_meal) . ", " . $meal->location
);
?>

<div class="row mb-3">
	<?php
	$output = '';
	
	foreach ($memberTypes as $memberType) {
		$capacities = $meal->getCapacityForMemberType($memberType);
		
		// use $memberType when calling these methods
		$totalDiners        = (int) $meal->totalDiners($memberType);
		$totalBookings      = (int) count($meal->bookings($memberType));
		$guests             = $totalDiners - $totalBookings;
		
		if ($totalDiners <= 0) {
			continue;
		}
	
		$totalDessertDiners = (int) $meal->totalDessertDiners($memberType);
	
		// determine classes
		$dinersClass   = ($totalDiners > $capacities['main']) ? 'text-danger' : 'text-muted';
		$dessertClass  = ($totalDessertDiners > $capacities['dessert']) ? 'text-danger' : 'text-muted';
	
		// build guest text (uses your autoPluralise helper)
		// autoPluralise(' singular', ' plural', $count)
		$guestSuffix = autoPluralise(' guest', ' guests', $guests);
		$guestText = $totalBookings . ' +' . $guests . $guestSuffix . ')'; // note: add closing ')' to match original format
	
		// escape numeric/text for safety (numbers are cast above but still)
		$safeTotalDiners        = htmlspecialchars((string)$totalDiners, ENT_QUOTES, 'UTF-8');
		$safeTotalBookings      = htmlspecialchars((string)$totalBookings, ENT_QUOTES, 'UTF-8');
		$safeGuests             = htmlspecialchars((string)$guests, ENT_QUOTES, 'UTF-8');
		$safeTotalDessertDiners = htmlspecialchars((string)$totalDessertDiners, ENT_QUOTES, 'UTF-8');
		$safeGuestText          = htmlspecialchars($guestText, ENT_QUOTES, 'UTF-8');
	
		$output .= '<div class="col-6 mb-3">';
		$output .=     '<div class="card">';
		$output .=         '<div class="card-body">';
		$output .=             '<div class="card-title">' . htmlspecialchars($memberType, ENT_QUOTES, 'UTF-8') . ' Diners</div>';
		$output .=             '<div class="card-text ' . $dinersClass . '">';
		$output .=                 '<h4>' . $safeTotalDiners . ' (' . $safeTotalBookings . ' +' . $safeGuests . autoPluralise(' guest', ' guests', $guests) . ')</h4>';
		$output .=             '</div>';
		$output .=         '</div>';
		$output .=     '</div>';
		$output .= '</div>';
	
		$output .= '<div class="col mb-3">';
		$output .=     '<div class="card">';
		$output .=         '<div class="card-body">';
		$output .=             '<div class="card-title">' . htmlspecialchars($memberType, ENT_QUOTES, 'UTF-8') . ' Dessert</div>';
		$output .=             '<div class="card-text ' . $dessertClass . '">';
		$output .=                 '<h4>' . $safeTotalDessertDiners . '</h4>';
		$output .=             '</div>';
		$output .=         '</div>';
		$output .=     '</div>';
		$output .= '</div>';
	}
	
	// finally echo the built HTML
	echo $output;
	?>
</div>

<?php

foreach ($memberTypes as $memberType) {
	$bookings = $meal->bookings($memberType);
	if (count($bookings) <= 0) {
		continue;
	}
	?>
<h4 class="d-flex justify-content-between align-items-center mb-3"><?= $memberType ?> Guest List</h4>

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
		foreach ($meal->bookings($memberType) AS $booking) {
		  $member = Member::fromLDAP($booking->member_ldap);
		  
		  $output  = "<tr>";
		  $output .= "<th scope=\"row\" rowspan=\"" . count($booking->guests()) + 1 . "\">" . $i . "</th>";
		  $output .= "<td>";
			$output .= "<strong>" . $member->name() . "</strong>";
			if (!empty($member->dietary)) {
			  $dietaryArray = explode(",", $member->dietary);
			  
			  $output .= "<br>" . implode(", ", $dietaryArray);
			}
			if (!empty($member->dietary_notes)) {
				$output .= '<br><i>Dietary notes: ' . htmlspecialchars($member->dietary_notes) . '</i>';
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
				$guestDietaryNotes = $guest['guest_dietary_notes'] ?? '';
				if (!empty($guestDietaryNotes)) {
					$output .= '<br><i>Dietary notes: ' . htmlspecialchars($guestDietaryNotes) . '</i>';
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
<?php
}
?>

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