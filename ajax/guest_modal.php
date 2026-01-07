<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}

$bookingUID = filter_input(INPUT_GET, 'booking_uid', FILTER_VALIDATE_INT);
$guestUID = filter_input(INPUT_GET, 'guest_uid', FILTER_VALIDATE_REGEXP,[
	'options' => [
		'regexp' => '/^[A-Za-z0-9_-]+$/'
	]
]
);

$action = ($_GET['action'] ?? 'add') === 'edit' ? 'edit' : 'add';
$guestUID = $guestUID ?? null;
$bookingUID = $bookingUID ?? null;
$booking = Booking::fromUID($bookingUID);
$meal = new Meal($booking->meal_uid);

// what if no guest UID, or invalid?

$guestName        = $booking->guests()[$guestUID]['guest_name']         ?? '';
$guestChargeTo    = $booking->guests()[$guestUID]['guest_charge_to']    ?? '';
$guestDomusReason = $booking->guests()[$guestUID]['guest_domus_reason'] ?? '';
$guestWineChoice  = $booking->guests()[$guestUID]['guest_wine_choice']  ?? '';
$guestDietary     = $booking->guests()[$guestUID]['guest_dietary']      ?? [];
?>

<div class="modal-header">
	<h5 class="modal-title"><?= ($action === 'add') ? 'Add Guest' : 'Update Guest'; ?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
	<?php if (!$meal->hasDessertCapacity(true) && $booking->dessert == "1"): ?>
		<p>Dessert capacity has been reached. Please remove yourself from dessert if you wish to add a guest.</p>
	<?php elseif ($action === 'add' && !$meal->hasCapacity(true)): ?>
		<p>Meal capacity has been reached.</p>
	<?php elseif ($action === 'add' && !$meal->hasGuestCapacity(count($booking->guests()), true)): ?>
		<p>You have added the maximum number of guests allowed for this meal.</p>
	<?php elseif (!$meal->canBook(true)): ?>
		<p>Deadline Passed</p>
	<?php else: ?>
		<!-- Guest Name -->
		<div class="mb-3">
			<label for="guest_name">Guest Name</label>
			<input type="text"
				   class="form-control"
				   name="guest_name"
				   id="guest_name"
				   value="<?= htmlspecialchars($guestName, ENT_QUOTES); ?>"
				   required>
			<small class="form-text text-muted">This name will appear on the sign-up list</small>
			<div class="invalid-feedback">Guest Name is required.</div>
		</div>
		
		<!-- Dietary Options -->
		<div class="accordion mb-3" id="accordionDietary">
			<div class="accordion-item">
				<h2 class="accordion-header">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDietary">
						Dietary Information<i class="ms-1">(Maximum: <?= $settings->get('meal_dietary_allowed'); ?>)</i>
					</button>
				</h2>
				<div id="collapseDietary" class="accordion-collapse collapse">
					<div class="accordion-body" data-max="<?php echo $settings->get('meal_dietary_allowed'); ?>">
						<?php
						$dietaryOptions    = array_map('trim', explode(',', $settings->get('meal_dietary')));
						$memberDietary     = array_map('trim', explode(',', $member->dietary ?? ''));
						$memberDietary = $guestDietary;
						$output = '';
						
						foreach ($dietaryOptions as $index => $dietaryOption) {
							$checked    = in_array($dietaryOption, $memberDietary, true) ? ' checked' : '';
							$safeValue  = htmlspecialchars($dietaryOption, ENT_QUOTES);
							$checkboxId = "guest_dietary_{$index}";
						
							$output .= '<div class="form-check">';
							$output .= '<input class="form-check-input dietaryOptionsMax" '
									 . 'type="checkbox" '
									 . 'name="guest_dietary[]" '
									 . 'id="' . $checkboxId . '" '
									 . 'value="' . $safeValue . '"' 
									 . $checked 
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
		
		<!-- Charge-To -->
		<div class="mb-3">
			<label class="form-label">Guest Charge-To</label>
			<select class="form-select" id="guest_charge_to" required>
				<?php foreach (explode(',', $settings->get('booking_charge-to')) as $charge_to):
					$charge_to = trim($charge_to); ?>
					<option value="<?= htmlspecialchars($charge_to); ?>"
						<?= $charge_to === $guestChargeTo ? 'selected' : ''; ?>>
						<?= htmlspecialchars($charge_to); ?>
					</option>
				<?php endforeach; ?>
			</select>
			
			<div id="guest_domus_reason_container" class="<?= ($guestChargeTo != 'Domus') ? 'd-none' : '' ?>">
				<input class="form-control mt-3"
				   type="text"
				   id="guest_domus_reason"
				   placeholder="Domus Reason (required)"
				   aria-label="Domus Reason (required)"
				   value="<?= $guestDomusReason; ?>">
				<div class="invalid-feedback">Guest Charge-To is required.</div>
			</div>
			
			<!-- Wine -->
			<?php if ($meal->allowed_wine == 1): ?>
				<div class="mb-3">
					<label for="guest_wine_choice" class="form-label">Wine <small>(charged via Battels)</small></label>
					<select class="form-select" id="guest_wine_choice" <?= $meal->canBook(true) ? '' : 'disabled' ?> required>
						<?php
						$wineOptions = explode(",", $settings->get('booking_wine_options'));
						
						foreach ($wineOptions as $i => $wineOption) {
							$wineOption = trim($wineOption);
							$selected = ($wineOption === $guestWineChoice) ? ' selected' : '';
							echo "<option value=\"{$wineOption}\"{$selected}>{$wineOption}</option>";
						}
						?>
					</select>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<!-- Footer -->
<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
	
	<?php if ($meal->canBook(true)): ?>
		<?php if ($action === 'edit'): ?>
			<div class="btn-group">
				<button type="submit" class="btn guest-update-btn btn-primary <?= !$meal->canBook(true) ? ' disabled' : ''; ?>" data-booking_uid="<?= $booking->uid ?>">Update Guest</button>
				
				<button type="button"
						class="btn btn-primary dropdown-toggle dropdown-toggle-split<?= !$meal->canBook(true) ? ' disabled' : ''; ?>"
						data-bs-toggle="dropdown"></button>
				<ul class="dropdown-menu">
					<li><a class="dropdown-item guest-delete-btn text-danger" href="#" data-booking_uid="<?= $booking->uid ?>" data-delete-guest>Delete Guest</a></li>
				</ul>
			</div>
	
			<input type="hidden" id="guest_uid" value="<?= $guestUID; ?>">
	
		<?php elseif($meal->canBook(true) && $meal->hasGuestCapacity(count($booking->guests()), true)): ?>
			<button type="submit" class="btn guest-add-btn btn-primary" data-booking_uid="<?= $booking->uid ?>">Add Guest</button>
		<?php endif; ?>
	<?php endif; ?>
</div>
