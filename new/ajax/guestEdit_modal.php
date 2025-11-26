<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}

$bookingUID = filter_input(INPUT_GET, 'booking_uid', FILTER_VALIDATE_INT);
$guestUID = filter_input(INPUT_GET, 'guest_uid', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$booking = Booking::fromUID($bookingUID);
$guest = $booking->guests()[$guestUID];

?>

<div class="modal-header">
	<h5 class="modal-title">Update Guest</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<div class="mb-3">
		<label for="name">Guest Name</label>
		<input type="text" class="form-control" name="guest_name" id="guest_name" aria-describedby="guest_nameHelp" value="<?= $guest['guest_name']; ?>" required="">
		<small id="guest_nameHelp" class="form-text text-muted">This name will appear on the sign-up list</small>
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
					$memberDietary     = array_map('trim', array_filter($guest['guest_dietary']) ?? '');
					
					$output = '';
					
					foreach ($dietaryOptions as $index => $dietaryOption) {
						$checked    = in_array($dietaryOption, $memberDietary, true) ? ' checked' : '';
						$safeValue  = htmlspecialchars($dietaryOption, ENT_QUOTES);
						$checkboxId = "dietary_{$index}";
					
						$output .= '<div class="form-check">';
						$output .= '<input class="form-check-input dietaryOptionsMax" '
								 . 'type="checkbox" '
								 . 'onclick="checkMaxCheckboxes(' . $dietaryOptionsMax . ')" '
								 . 'name="dietary[]" '
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
					
					<small class="form-text text-muted"><?php echo $settings->get('meal_dietary_message'); ?></small>
				</div>
			</div>
		</div>
	</div>
	
	<div class="mb-3">
		<label for="type" class="form-label">Guest Charge-To</label>
		<select class="form-select mb-3" name="guest_charge_to" id="guest_charge_to" required>
			<?php
			$chargeToOptions = explode(',', $settings->get('booking_charge-to'));
			
			foreach ($chargeToOptions as $charge_to) {
				$charge_to = trim($charge_to);
				$selected = ($charge_to === $guest['guest_charge_to']) ? ' selected' : '';
				echo "<option value=\"{$charge_to}\"{$selected}>{$charge_to}</option>";
			}
			?>
		</select>
	
		<input class="form-control mb-3 d-none"
			   type="text"
			   id="guest_domus_reason"
			   name="guest_domus_reason"
			   placeholder="Domus Reason (required)"
			   aria-label="Domus Reason (required)"
			   value="<?= $guest['guest_domus_reason']; ?>">
	
		<div id="guest_charge_toHelp" class="form-text">* wine charged via Battels</div>
		<div class="invalid-feedback">Charge-To is required.</div>
	</div>
	
	<div class="mb-3">
		<span class="form-check-label" for="">Guest Wine</span>
		<?php
		$wineOptions = explode(",", $settings->get('booking_wine_options'));
		
		foreach($wineOptions as $wineOption) {
			// Remove all non-alphanumeric characters
			$id = preg_replace('/[^a-z0-9]/', '', strtolower($wineOption));
			
			$checked = ($wineOption == $guest['guest_wine_choice']) ? " checked" : "";
			
			$output  = "<div class=\"form-check\">";
			$output .= "<input class=\"form-check-input\" type=\"radio\" name=\"default_wine_choice\" id=\"" . $id . "\" value=\"" . htmlspecialchars($wineOption, ENT_QUOTES, 'UTF-8') . "\" " . $checked . ">";
			$output .= "<label class=\"form-check-label\" for=\"default_wine_choice\">" . $wineOption . "</label>";
			$output .= "</div>";
			
			echo $output;
		}
		?>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
	
	<div class="btn-group">
	  <button type="button" class="btn btn-primary">Update Guest</button>
	  <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
		<span class="visually-hidden">Toggle Dropdown</span>
	  </button>
	  <ul class="dropdown-menu">
		<li><a class="dropdown-item text-danger" href="#">Delete Guest</a></li>
	  </ul>
	</div>
	
	<input type="hidden" name="booking_uid" value="<?= $booking->uid; ?>">
	<input type="hidden" name="guest_uid" value="<?= $guestUID; ?>">
</div>