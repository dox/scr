<?php
$bookingUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$booking = Booking::fromUID($bookingUID);
$meal = new Meal($booking->meal_uid);

if (!isset($booking->uid)) {
	die("Unknown or unavailable booking");
}

if (!$user->hasPermission("meals") && $booking->member_ldap != $user->getUsername()) {
	die("Unknown or unavailable booking");
}

if (isset($_POST)) {
	//printArray($_POST);
	
	if ($meal->isCutoffValid() || $user->hasPermission("bookings")) {
		// update booking
		if (isset($_POST['bookingUID'])) {
			$booking->update($_POST);
		}
		
		// add new guest
		if (isset($_POST['guest_add'])) {
			$booking->addGuest($_POST);
		}
		
		// edit guest
		if (isset($_POST['guest_uid'])) {
			$booking->editGuest($_POST);
		}
		
		// delete guest
		if (isset($_POST['delete_guest']) && $_POST['delete_guest'] == "1") {
			$booking->deleteGuest($_POST['guest_uid']);
		}
		
		$booking = Booking::fromUID($bookingUID);
	}
}

$icons[] = [
	'permission' => 'meals',
	'title' => 'Guest List',
	'class' => '',
	'event' => 'index.php?page=guestlist&uid=' . $meal->uid,
	'icon' => 'card-list'
];
$icons[] = [
	'permission' => 'meals',
	'title' => 'Edit Meal',
	'class' => '',
	'event' => 'index.php?page=meal&uid=' . $meal->uid,
	'icon' => 'fork-knife'
];

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
	'icon' => 'trash3',
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
	<div class="col-md-7 col-lg-8 order-2 order-md-1">
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
	<div class="col-md-5 col-lg-4 order-1 order-md-2">
		<h4>Booking Details</h4>
		
		<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
		<div class="mb-3">
			<label for="type" class="form-label">Charge-To</label>
			<select class="form-select charge_to" name="charge_to" required>
				<?php
				$chargeToOptions = explode(',', $settings->get('booking_charge-to'));
				
				foreach ($chargeToOptions as $charge_to) {
					$charge_to = trim($charge_to);
					$selected = ($charge_to === $booking->charge_to) ? ' selected' : '';
					echo "<option value=\"{$charge_to}\"{$selected}>{$charge_to}</option>";
				}
				?>
			</select>
		
			<input class="form-control domus_reason mt-3 d-none"
				   type="text"
				   name="domus_reason"
				   placeholder="Domus Reason (required)"
				   aria-label="Domus Reason (required)"
				   value="<?= $booking->domus_reason; ?>">
		
			<div id="charge_toHelp" class="form-text">* wine charged via Battels</div>
			<div class="invalid-feedback">Default Charge-To is required.</div>
		</div>
		
		<div class="mb-3">
		<?php if ($meal->allowed_wine == 1): ?>
			<span class="form-check-label">Wine <small>(charged via Battels)</small></span>
			<?php
			$wineOptions = explode(",", $settings->get('booking_wine_options'));
			$lastIndex   = count($wineOptions) - 1;
			$anyChecked  = false;
		
			foreach ($wineOptions as $i => $wineOption) {
				$id = preg_replace('/[^a-z0-9]/', '', strtolower($wineOption));
		
				// check if this option matches the current booking choice
				$checked = ($wineOption == $booking->wine_choice);
				if ($checked) $anyChecked = true;
		
				$output  = '<div class="form-check">';
				$output .= '<input class="form-check-input" type="radio" name="wine_choice" id="' . $id . '" value="' . htmlspecialchars($wineOption, ENT_QUOTES, 'UTF-8') . '"';
				// temporarily leave checked empty
				$output .= ($checked) ? ' checked' : '';
				$output .= '>';
				$output .= '<label class="form-check-label" for="' . $id . '">' . $wineOption . '</label>';
				$output .= '</div>';
		
				// if this is the last option and nothing matched, mark it checked
				if ($i === $lastIndex && !$anyChecked) {
					$output = str_replace('<input ', '<input checked ', $output);
				}
		
				echo $output;
			}
			?>
		<?php endif; ?>
		</div>
		
		<?php if ($meal->allowed_dessert == 1): ?>
			<div class="mb-3">
				<input type="hidden" name="dessert" value="0">
				<input class="form-check-input"
					   id="dessert"
					   name="dessert"
					   value="1"
					   type="checkbox"
					   <?= (!$meal->hasDessertCapacity()) ? "disabled" : "" ?>
					   <?= $booking->dessert == "1" ? "checked" : "" ?>>
				<label for="dessert" class="form-label">
					Dessert <i>(applies to your guests)</i>
				</label>
			</div>
		<?php endif; ?>
		
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
			$modalTarget = "#editGuestModal_" . $guest['guest_uid'];
			
			$output .= '<li class="list-group-item d-flex flex-column">';
		
			// Top row: name on left, edit icon on right
			$output .= '<div class="d-flex justify-content-between align-items-center">';
			$output .= '<h6 class="mb-1">' . htmlspecialchars($guest['guest_name']) . '</h6>';
			
			$output .= '<a href="#" class="load-remote-guest_edit" id="guestUID-' . $guest['guest_uid'] . '" data-bs-toggle="modal" data-bs-target="' . $modalTarget . '"><i class="bi bi-pencil-square"></i></a>';
			
			$output .= '</div>';
		
			// Dietary info (if any)
			if (!empty(array_filter($guest['guest_dietary'] ?? []))) {
				$output .= '<small class="text-muted d-block mb-1">Dietary: ' . 
						   htmlspecialchars(implode(', ', array_filter($guest['guest_dietary']))) . '</small>';
			}
		
			// Wine/Dessert
			$wineDessert = [];
			if ($guest['guest_charge_to'] == "Domus") {
				$wineDessert[] = '<span><i class="bi bi-mortarboard me-2"></i>' . htmlspecialchars($guest['guest_domus_reason']) . '</span>';
			}
			if (!empty($guest['guest_wine_choice'])) {
				$wineDessert[] = '<span><i class="bi bi-cup-straw me-2"></i>' . $guest['guest_wine_choice'] . '</span>';
			}
			if (!empty($booking->dessert)) {
				$wineDessert[] = '<span><i class="bi bi-cookie me-2"></i>Dessert</span>';
			}
			
			$output .= implode('', $wineDessert);
			
			$output .= '</li>';
		}
		$output .= '</ul>';
		
		echo $output;
		?>
	</div>
</div>



<!-- Add Guest Modal -->
<div class="modal fade" id="addGuestModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php
			  $guestUID = null;      // or ''
			  $guest     = [];       // empty guest
			  include '_guest_modal.php';
			  ?>
		</div>
	</div>
</div>

<!-- Edit Guest Modal(s) -->
<?php
$guestModalOutput = '';
foreach ($booking->guests() as $row) {
	$guestUID = $row['guest_uid'];
	$guest    = $row;

	ob_start(); // begin capture
	?>
	<div class="modal fade" id="editGuestModal_<?= $guestUID; ?>" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<?php include '_guest_modal.php'; ?>
			</div>
		</div>
	</div>
	<!-- christmas -->
	<?php
	$guestModalOutput .= ob_get_clean(); // append captured block
}

echo $guestModalOutput;
?>

<!-- Delete Booking Modal -->
<div class="modal fade" tabindex="-1" id="deleteBookingModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
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
				<button type="submit" class="btn btn-danger booking-delete-btn" data-booking_uid="<?= $booking->uid; ?>">Delete Booking</button>
			</div>
		</div>
	</div>
</div>

<script>
// enforce domus_reason for charge_to
toggleReason('charge_to', 'domus_reason', 'Domus');
toggleReason('charge_to', 'guest_domus_reason', 'Domus');
</script>

<script>
document.addEventListener('click', function(e) {
	const btn = e.target.closest('[data-delete-guest]');
	if (!btn) return;

	e.preventDefault();

	const form  = btn.closest('form');
	const field = form.querySelector('input[name="delete_guest"]');

	field.value = '1';
	form.submit();
});
</script>