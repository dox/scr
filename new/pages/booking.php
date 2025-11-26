<?php
$bookingUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$booking = Booking::fromUID($bookingUID);

if (!isset($booking->uid)) {
	die("Unknown or unavailable booking");
}

if (!$user->hasPermission("meals") && $booking->member_ldap != $user->getUsername()) {
	die("Unknown or unavailable booking");
}

if (isset($_POST['bookingUID'])) {
	$booking->update($_POST);
	$booking = Booking::fromUID($bookingUID);
}

if (isset($_POST['bookingAddGuest'])) {
	printArray($_POST);
	$booking->addGuest($_POST);
	$booking = Booking::fromUID($bookingUID);
}

$meal = new Meal($booking->meal_uid);

if (count($booking->guests()) < $meal->scr_guests || $user->hasPermission("bookings")) {
	$icons[] = [
		'permission' => 'everyone',
		'title' => 'Add Guest',
		'class' => '',
		'event' => '',
		'icon' => 'person-plus',
		'data' => [
			'bs-toggle' => 'modal',
			'bs-target' => '#addGuestModal'
		]
	];
}

$icons[] = [
	'permission' => 'everyone',
	'title' => 'Delete Booking',
	'class' => 'text-danger',
	'event' => '',
	'icon' => 'calendar-event',
	'data' => [
		'bs-toggle' => 'modal',
		'bs-target' => '#deleteBookingModal'
	]
];

echo pageTitle(
	$meal->name,
	$meal->location . ", " . formatDate($meal->date_meal),
	$icons
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
					if (!$user->hasPermission("members") && $member->opt_in != 1) {
						$guest['guest_name'] = 'Hidden';
					}
					
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
		
		<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
		<div class="mb-3">
			<label for="type" class="form-label">Charge-To</label>
			<select class="form-select" name="charge_to" id="charge_to" required>
				<?php
				$chargeToOptions = explode(',', $settings->get('booking_charge-to'));
				
				foreach ($chargeToOptions as $charge_to) {
					$charge_to = trim($charge_to);
					$selected = ($charge_to === $booking->charge_to) ? ' selected' : '';
					echo "<option value=\"{$charge_to}\"{$selected}>{$charge_to}</option>";
				}
				?>
			</select>
		
			<input class="form-control mt-3 d-none"
				   type="text"
				   id="domus_reason"
				   name="domus_reason"
				   placeholder="Domus Reason (required)"
				   aria-label="Domus Reason (required)"
				   value="<?= $booking->domus_reason; ?>">
		
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
			<input type="hidden" name="dessert" value="0">
			<input class="form-check-input"
				   id="dessert"
				   name="dessert"
				   value="1"
				   type="checkbox"
				   <?= $booking->dessert == "1" ? "checked" : "" ?>>
			<label for="dessert" class="form-label">
				Dessert <i>(applies to your guests)</i>
			</label>
		</div>
		
		<div class="mb-3">
		  <button type="submit" class="btn btn-primary w-100">Update Booking Preferences</button>
		  <input type="hidden" name="bookingUID" id="bookingUID" value="<?= $booking->uid; ?>">
		</div>
		</form>
		
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
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add Guest</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label for="name">Guest Name</label>
					<input type="text" class="form-control" name="guest_name" id="guest_name" value="" required="">
					
					<?php
					if (!$member->opt_in == 1) {
						echo "<small class=\"form-text text-muted\">This name will be hidden on the sign-up list.  You can change your default privacy settings in <a href=\"index.php?page=member\">your profile</a></small>";
					}
					?>
				</div>
				
				<div class="accordion mb-3" id="accordionDietary">
					<div class="accordion-item">
						<h2 class="accordion-header">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> Dietary Information&nbsp;<i>(Maximum: <?php echo $settings->get('meal_dietary_allowed'); ?>)</i></button>
						</h2>
						<div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
							<div class="accordion-body">
								<?php
								$dietaryOptions    = array_map('trim', explode(',', $settings->get('meal_dietary')));
								$dietaryOptionsMax = (int) $settings->get('meal_dietary_allowed');
								
								$output = '';
								
								foreach ($dietaryOptions as $index => $dietaryOption) {
									$safeValue  = htmlspecialchars($dietaryOption, ENT_QUOTES);
									$checkboxId = "dietary_{$index}";
								
									$output .= '<div class="form-check">';
									$output .= '<input class="form-check-input dietaryOptionsMax" '
											 . 'type="checkbox" '
											 . 'onclick="checkMaxCheckboxes(' . $dietaryOptionsMax . ')" '
											 . 'name="guest_dietary[]" '
											 . 'id="' . $checkboxId . '" '
											 . 'value="' . $safeValue . '"'
											 . '>';
									$output .= '<label class="form-check-label" for="' . $checkboxId . '">' 
											 . $safeValue 
											 . '</label>';
									$output .= '</div>';
								}
								
								echo $output;
								?>
								
								<small id="nameHelp" class="form-text text-muted"><?php echo $settings->get('meal_dietary_message'); ?></small>
							</div>
						</div>
					</div>
				</div>
				
				<div class="mb-3">
					<label for="type" class="form-label">Charge-To</label>
					<select class="form-select" name="guest_charge_to" id="guest_charge_to" required>
						<?php
						$chargeToOptions = explode(',', $settings->get('booking_charge-to'));
						
						foreach ($chargeToOptions as $guest_charge_to) {
							$guest_charge_to = trim($guest_charge_to);
							$selected = ($guest_charge_to === 'Battels') ? ' selected' : '';
							echo "<option value=\"{$guest_charge_to}\"{$selected}>{$guest_charge_to}</option>";
						}
						?>
					</select>
				
					<input class="form-control mt-3 d-none"
						   type="text"
						   id="guest_domus_reason"
						   name="guest_domus_reason"
						   placeholder="Domus Reason (required)"
						   aria-label="Domus Reason (required)"
						   value="<?= $booking->domus_reason; ?>">
				
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
						$output .= "<input class=\"form-check-input\" type=\"radio\" name=\"guest_wine_choice\" id=\"" . $id . "\" value=\"" . htmlspecialchars($wineOption, ENT_QUOTES, 'UTF-8') . "\" " . $checked . ">";
						$output .= "<label class=\"form-check-label\" for=\"guest_wine_choice\">" . $wineOption . "</label>";
						$output .= "</div>";
						
						echo $output;
					}
					?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Add Guest</button>
				<input type="hidden" name="bookingAddGuest" id="bookingAddGuest" value="1">
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
  <div class="modal-dialog">
	<div class="modal-content" id="modalContent">
	</div>
  </div>
</div>


<script>
// enforce domus_reason for charge_to
toggleReason('charge_to', 'domus_reason', 'Domus');
toggleReason('guest_charge_to', 'guest_domus_reason', 'Domus');

// enforce guest_domus_reason for charge_to with guest edit modal
document.addEventListener('ajax-modal-loaded', () => {
	
});

// Load AJAX menu
remoteModalLoader('.load-remote-guest_edit', '#editGuestModal', '#modalContent');
</script>