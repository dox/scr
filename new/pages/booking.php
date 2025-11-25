<?php
$bookingUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$booking = Booking::fromUID($bookingUID);

if (!isset($booking->uid)) {
	die("Unknown or unavailable booking");
}

if (!$user->hasPermission("meals") && $booking->member_ldap != $user->getUsername()) {
	die("Unknown or unavailable booking");
}

$meal = new Meal($booking->meal_uid);

echo pageTitle(
	$meal->name,
	$meal->location . ", " . formatDate($meal->date_meal),
	[
		[
			'permission' => 'everyone',
			'title' => 'Add Guest',
			'class' => '',
			'event' => '',
			'icon' => 'person-plus',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addGuestModal'
			]
		],
		[
			'permission' => 'everyone',
			'title' => 'Delete Booking',
			'class' => 'text-danger',
			'event' => '',
			'icon' => 'calendar-event',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteBookingModal'
			]
		]
	]
);
?>

<div class="row">
	<div class="col-md-7 col-lg-8">
		<h4>Guest List</h4>
		<?php
		$output = '<ul>';
		
		foreach ($meal->bookings() as $guestListBooking) {
			$member = Member::fromLDAP($guestListBooking->member_ldap);
		
			$output .= '<li>';
			
			// Person's name
			$output .= $member->public_displayName() . ' ';
		
			// Person's wine/dessert
			$wineDessert = [];
			if (!empty($guestListBooking->wine)) $wineDessert[] = '<i class="bi bi-cup-straw"></i>';
			if (!empty($guestListBooking->dessert)) $wineDessert[] = '<i class="bi bi-cookie"></i>';
			if (!empty($wineDessert)) {
				$output .=  implode(' ', $wineDessert);
			}
		
			// Guests
			$guests = $guestListBooking->guests();
			if (!empty($guests)) {
				$output .= '<ul>';
				foreach ($guests as $guest) {
					if (!$member->opt_in == 1) $guest['guest_name'] = 'Hidden';
					
					$output .= '<li>';
					$output .= htmlspecialchars($guest['guest_name']) . ' ';
					
					// Guest wine/dessert
					$guestWineDessert = [];
					if (!empty($guest['guest_wine'])) $guestWineDessert[] = '<i class="bi bi-cup-straw"></i>';
					if (!empty($guestListBooking->dessert)) $guestWineDessert[] = '<i class="bi bi-cookie"></i>';
					if (!empty($guestWineDessert)) {
						$output .= implode(' ', $guestWineDessert);
					}
		
					$output .= '</li>';
				}
				$output .= '</ul>';
			}
		
			$output .= '</li>';
		}
		
		$output .= '</ul>';
		
		echo $output;
		?>

		
		
		<div class="card mb-3">
			<div class="card-body text-center">
				<h2 class="card-title">Menu</h2>
				<h5 class="card-title mb-3"><?= $meal->location . ", " . formatDate($meal->date_meal); ?></h5>
				<p class="card-text"><?= $meal->menu; ?></p>
			</div>
		</div>
		
	</div>
	<div class="col-md-5 col-lg-4">
		<h4>Booking Details</h4>
		<?php
		printArray($booking);
		?>
		
		<div class="mb-3">
			<label for="type" class="form-label">Charge-To</label>
			<select class="form-select mb-3" name="charge_to" id="charge_to" required>
				<?php
				$chargeToOptions = explode(',', $settings->get('booking_charge-to'));
				
				foreach ($chargeToOptions as $charge_to) {
					$charge_to = trim($charge_to);
					$selected = ($charge_to === $booking->charge_to) ? ' selected' : '';
					echo "<option value=\"{$charge_to}\"{$selected}>{$charge_to}</option>";
				}
				?>
			</select>
		
			<input class="form-control mb-3 d-none"
				   type="text"
				   id="domus_reason"
				   name="domus_reason"
				   placeholder="Domus Reason (required)"
				   aria-label="Domus Reason (required)"
				   value="">
		
			<div id="charge_toHelp" class="form-text">* wine charged via Battels</div>
			<div class="invalid-feedback">Default Charge-To is required.</div>
		</div>
		
		<div class="mb-3">
			<span class="form-check-label" for="">Wine <small>(charged via Battels)</small></label></span>
			<?php
			$wineOptions = explode(",", $settings->get('booking_wine_options'));
			
			foreach($wineOptions as $wineOption) {
				// Remove all non-alphanumeric characters
				$id = preg_replace('/[^a-z0-9]/', '', strtolower($wineOption));
				
				$checked = ($wineOption == $booking->wine_choice) ? " checked" : "";
				
				$output  = "<div class=\"form-check\">";
				$output .= "<input class=\"form-check-input\" type=\"radio\" name=\"wine_choice\" id=\"" . $id . "\" value=\"" . htmlspecialchars($wineOption, ENT_QUOTES, 'UTF-8') . "\" " . $checked . ">";
				$output .= "<label class=\"form-check-label\" for=\"wine_choice\">" . $wineOption . "</label>";
				$output .= "</div>";
				
				echo $output;
			}
			?>
		</div>
		
		<div class="mb-3">
				<input class="form-check-input" id="dessert" name="dessert" value="1" type="checkbox" checked="">
				<label for="dessert" class="form-label">Dessert <i>(applies to your guests)</i> </label>
		</div>
		
		<div class="mb-3">
		  <button type="submit" class="btn btn-primary w-100">Update Booking Preferences</button>
		  <input type="hidden" name="bookingUID" id="bookingUID" value="<?= $booking->uid; ?>">
		</div>
		
		<hr>
		
		<h4 class="d-flex justify-content-between align-items-center mb-3">
		  <span>Your Guests</span>
		  <span class="badge bg-secondary rounded-pill"><?php echo count($booking->guests()); ?></span>
		</h4>
		
		<?php
		$output  = '<ul class="list-group mb-3">';
		foreach ($booking->guests() as $guest) {
			$output .= '<li class="list-group-item d-flex flex-column">';
		
			// Top row: name on left, edit icon on right
			$output .= '<div class="d-flex justify-content-between align-items-center">';
			$output .= '<h6 class="mb-1">' . htmlspecialchars($guest['guest_name']) . '</h6>';
			
			$output .= '<a href="#" class="load-remote-guest_edit" id="guestUID-' . $guest['guest_uid'] . '" data-url="./ajax/guestEdit_modal.php?booking_uid=' . $booking->uid . '&guest_uid=' . $guest['guest_uid'] . '" data-bs-toggle="modal" data-bs-target="#menuModal"><i class="bi bi-pencil-square"></i></a>';
			
			$output .= '</div>';
		
			// Dietary info (if any)
			if (!empty(array_filter($guest['guest_dietary'] ?? []))) {
				$output .= '<small class="text-muted d-block mb-1">Dietary: ' . 
						   htmlspecialchars(implode(', ', array_filter($guest['guest_dietary']))) . '</small>';
			}
		
			// Wine/Dessert
			$wineDessert = [];
			if (!empty($guest['wine'])) $wineDessert[] = '<i class="bi bi-cup-straw"></i>';
			if (!empty($booking->dessert)) $wineDessert[] = '<i class="bi bi-cookie"></i>';
			if (!empty($wineDessert)) {
				$output .= '<small class="text-muted d-block">' . implode(' ', $wineDessert) . '</small>';
			}
		
			$output .= '</li>';
			
		
		}
		$output .= '</ul>';
		
		echo $output;
		?>
	</div>
</div>



<!-- Add Guest Modal -->
<div class="modal fade" tabindex="-1" id="addGuestModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="index.php">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add Guest</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>Coming soon...</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Add Guest</button>
			</div>
		</div>
	</div>
	</form>
</div>

<!-- Delete Booking Modal -->
<div class="modal fade" tabindex="-1" id="deleteBookingModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="index.php">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Booking</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><span class="text-danger"><strong>WARNING!</strong></span> Are you sure you want to delete this booking?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger">Delete Booking</button>
				<input type="hidden" name="deleteTermUID" value="<?= $booking->uid; ?>">
			</div>
		</div>
	</div>
	</form>
</div>

<!-- Edit Guest Modal -->
<div class="modal fade" id="editGuestModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-body" id="modalBody"></div>
	</div>
  </div>
</div>


<script>
// enforce domus_reason on anything other than 'Dining Entitlement'
toggleReason('charge_to', 'domus_reason', 'Domus');

// enforce guest_domus_reason on anything other than 'Dining Entitlement'
document.addEventListener('ajax-modal-loaded', () => {
	toggleReason('guest_charge_to', 'guest_domus_reason', 'Domus');
});

// Load AJAX menu
remoteModalLoader('.load-remote-guest_edit', '#editGuestModal', '#modalBody');



</script>