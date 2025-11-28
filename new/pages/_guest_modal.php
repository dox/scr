<?php
$isEdit = !empty($guestUID);

$guestName        = $guest['guest_name']         ?? '';
$guestChargeTo    = $guest['guest_charge_to']    ?? '';
$guestDomusReason = $guest['guest_domus_reason'] ?? '';
$guestWineChoice  = $guest['guest_wine_choice']  ?? '';
$guestDietary     = $guest['guest_dietary']      ?? [];
?>
<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
<div class="modal-header">
	<h5 class="modal-title"><?= $isEdit ? 'Update Guest' : 'Add Guest'; ?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
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
	</div>

	<!-- Dietary Options -->
	<div class="accordion mb-3" id="accordionDietary">
		<div class="accordion-item">
			<h2 class="accordion-header">
				<button class="accordion-button collapsed"
						type="button"
						data-bs-toggle="collapse"
						data-bs-target="#collapseOne"
						aria-expanded="false">
					Dietary Information
					<i>(Maximum: <?= $settings->get('meal_dietary_allowed'); ?>)</i>
				</button>
			</h2>

			<div id="collapseOne" class="accordion-collapse collapse">
				<div class="accordion-body"
					 data-max="<?= $settings->get('meal_dietary_allowed'); ?>">

					<?php
					$dietaryOptions    = array_map('trim', explode(',', $settings->get('meal_dietary')));
					$dietaryOptionsMax = (int) $settings->get('meal_dietary_allowed');

					foreach ($dietaryOptions as $i => $option):
						$safe   = htmlspecialchars($option, ENT_QUOTES, 'UTF-8');
						$checked = (is_array($guestDietary) && in_array($option, $guestDietary)) ? ' checked' : '';
						$id     = "dietary_{$i}";
					?>
						<div class="form-check">
							<input class="form-check-input dietaryOptionsMax"
								   type="checkbox"
								   name="guest_dietary[]"
								   id="<?= $id; ?>"
								   value="<?= $safe; ?>"<?= $checked; ?>>
							<label class="form-check-label" for="<?= $id; ?>">
								<?= $safe; ?>
							</label>
						</div>
					<?php endforeach; ?>

					<small class="form-text text-muted">
						<?= $settings->get('meal_dietary_message'); ?>
					</small>
				</div>
			</div>
		</div>
	</div>

	<!-- Charge-To -->
	<div class="mb-3">
		<label class="form-label">Guest Charge-To</label>
		<select class="form-select charge_to mb-3" name="guest_charge_to" required>
			<?php
			$chargeToOptions = explode(',', $settings->get('booking_charge-to'));
			foreach ($chargeToOptions as $charge_to):
				$charge_to = trim($charge_to);
				$selected = ($charge_to === $guestChargeTo) ? ' selected' : '';
			?>
				<option value="<?= $charge_to; ?>"<?= $selected; ?>>
					<?= $charge_to; ?>
				</option>
			<?php endforeach; ?>
		</select>

		<input class="form-control guest_domus_reason mb-3 d-none"
			   type="text"
			   name="guest_domus_reason"
			   placeholder="Domus Reason (required)"
			   value="<?= htmlspecialchars($guestDomusReason, ENT_QUOTES); ?>">

		<div id="guest_charge_toHelp" class="form-text">* wine charged via Battels</div>
		<div class="invalid-feedback">Charge-To is required.</div>
	</div>

	<!-- Wine -->
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
			$checked = ($wineOption == $guestWineChoice);
			if ($checked) $anyChecked = true;
	
			$output  = '<div class="form-check">';
			$output .= '<input class="form-check-input" type="radio" name="guest_wine_choice" id="' . $id . '" value="' . htmlspecialchars($wineOption, ENT_QUOTES, 'UTF-8') . '"';
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
</div>

<!-- Footer -->
<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>

	<?php if ($isEdit): ?>
		<div class="btn-group">
			<button type="submit" class="btn btn-primary <?php if (!$meal->canBook() || !$user->hasPermission("bookings")) { echo " disabled"; }?>">Update Guest</button>
			<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split <?php if (!$meal->canBook() || !$user->hasPermission("bookings")) { echo " disabled"; }?>"
					data-bs-toggle="dropdown"></button>
			<ul class="dropdown-menu">
				<li><a class="dropdown-item text-danger" href="#" data-delete-guest>Delete Guest</a></li>
			</ul>
		</div>
		<input type="hidden" name="guest_uid" value="<?= $guestUID; ?>">
		<input type="hidden" name="delete_guest" value="false">
	<?php else: ?>
		<button type="submit" class="btn btn-primary <?php if (!$meal->canBook() || !$user->hasPermission("bookings")) { echo " disabled"; }?>">Add Guest</button>
		<input type="hidden" name="guest_add" value="1"">
	<?php endif; ?>
	
</div>
</form>
