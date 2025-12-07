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
		<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
	</div>

	<div class="modal-body">

		<?php if (!$meal->hasDessertCapacity(true) && $booking->dessert == "1"): ?>
			<p>Dessert capacity has been reached. Please remove yourself from dessert if you wish to add a guest.</p>
		<?php elseif (!$isEdit && !$meal->hasGuestCapacity(count($booking->guests()), true)): ?>
			<p>Capacity has been reached.</p>
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
			</div>

			<!-- Dietary Options -->
			<div class="accordion mb-3" id="accordionDietary">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDietary">
							Dietary Information
							<i>(Maximum: <?= $settings->get('meal_dietary_allowed'); ?>)</i>
						</button>
					</h2>

					<div id="collapseDietary" class="accordion-collapse collapse">
						<div class="accordion-body" data-max="<?= $settings->get('meal_dietary_allowed'); ?>">

							<?php
							$dietaryOptions = array_map('trim', explode(',', $settings->get('meal_dietary')));
							foreach ($dietaryOptions as $i => $option):
								$safe    = htmlspecialchars($option, ENT_QUOTES, 'UTF-8');
								$checked = (is_array($guestDietary) && in_array($option, $guestDietary)) ? ' checked' : '';
								$id      = "dietary_{$i}";
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
				<select class="form-select charge_to" name="guest_charge_to" required>
					<?php foreach (explode(',', $settings->get('booking_charge-to')) as $charge_to):
						$charge_to = trim($charge_to); ?>
						<option value="<?= htmlspecialchars($charge_to); ?>"
							<?= $charge_to === $guestChargeTo ? 'selected' : ''; ?>>
							<?= htmlspecialchars($charge_to); ?>
						</option>
					<?php endforeach; ?>
				</select>

				<input class="form-control guest_domus_reason mt-3 d-none"
					   type="text"
					   name="guest_domus_reason"
					   placeholder="Domus Reason (required)"
					   value="<?= htmlspecialchars($guestDomusReason, ENT_QUOTES); ?>">

				<div class="form-text">* wine charged via Battels</div>
			</div>

			<!-- Wine -->
			<?php if ($meal->allowed_wine == 1): ?>
				<div class="mb-3">
					<span class="form-check-label">Wine <small>(charged via Battels)</small></span>

					<?php
					$options  = explode(",", $settings->get('booking_wine_options'));
					$hasMatch = in_array($guestWineChoice, $options, true);
					?>

					<?php foreach ($options as $i => $wineOption):
						$id = preg_replace('/[^a-z0-9]/', '', strtolower($wineOption));
						$checked = ($wineOption === $guestWineChoice)
								   || (!$hasMatch && $i === array_key_last($options));
					?>
						<div class="form-check">
							<input class="form-check-input"
								   type="radio"
								   name="guest_wine_choice"
								   id="<?= $id; ?>"
								   value="<?= htmlspecialchars($wineOption, ENT_QUOTES); ?>"
								   <?= $checked ? 'checked' : ''; ?>>
							<label class="form-check-label" for="<?= $id; ?>">
								<?= htmlspecialchars($wineOption); ?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		<?php endif; ?>

	</div>

	<!-- Footer -->
	<div class="modal-footer">
		<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>

		<?php if ($meal->canBook(true)): ?>
			<?php if ($isEdit): ?>
				<div class="btn-group">
					<button type="submit" class="btn btn-primary<?= !$meal->canBook(true) ? ' disabled' : ''; ?>">Update Guest</button>
					<button type="button"
							class="btn btn-primary dropdown-toggle dropdown-toggle-split<?= !$meal->canBook(true) ? ' disabled' : ''; ?>"
							data-bs-toggle="dropdown"></button>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item text-danger" href="#" data-delete-guest>Delete Guest</a></li>
					</ul>
				</div>

				<input type="hidden" name="guest_uid" value="<?= $guestUID; ?>">
				<input type="hidden" name="delete_guest" value="false">

			<?php else: ?>
				<button type="submit" class="btn btn-primary<?= !$meal->canBook(true) ? ' disabled' : ''; ?>">Add Guest</button>
				<input type="hidden" name="guest_add" value="1">
			<?php endif; ?>
		<?php endif; ?>
	</div>

</form>
