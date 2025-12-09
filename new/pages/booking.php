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

$icons = [];
if ($user->hasPermission("meals")) {
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
}


if ($meal->canBook(true)) {
	$icons[] = [
		'permission' => 'everyone',
		'title' => 'Add Guest',
		'class' => '',
		'event' => '',
		'icon' => 'person-plus',
		'data' => [
			'bs-toggle' => 'modal',
			'bs-target' => '#addEditGuestModal'
		]
	];
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
}

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
					$output .= htmlspecialchars($guest['guest_name'] ?? '') . ' ';
					
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
			<label for="charge_to" class="form-label">Charge-To</label>
			<select class="form-select" id="charge_to" <?= $meal->canBook(true) ? '' : 'disabled' ?> required>
				<?php
				$chargeToOptions = explode(',', $settings->get('booking_charge-to'));
				
				foreach ($chargeToOptions as $charge_to) {
					$charge_to = trim($charge_to);
					$selected = ($charge_to === $booking->charge_to) ? ' selected' : '';
					echo "<option value=\"{$charge_to}\"{$selected}>{$charge_to}</option>";
				}
				?>
			</select>
		
			<input class="form-control mt-3 <?= ($booking->charge_to != 'Domus') ? 'd-none' : '' ?>"
				   type="text"
				   id="domus_reason"
				   placeholder="Domus Reason (required)"
				   aria-label="Domus Reason (required)"
				   value="<?= $booking->domus_reason; ?>">
			<div class="invalid-feedback">Default Charge-To is required.</div>
		</div>
		
		<?php if ($meal->allowed_wine == 1): ?>
			<div class="mb-3">
				<label for="wine_choice" class="form-label">Wine <small>(charged via Battels)</small></label>
				<select class="form-select" id="wine_choice" <?= $meal->canBook(true) ? '' : 'disabled' ?> required>
					<?php
					$wineOptions = explode(",", $settings->get('booking_wine_options'));
					
					foreach ($wineOptions as $i => $wineOption) {
						$wineOption = trim($wineOption);
						$selected = ($wineOption === $booking->wine_choice) ? ' selected' : '';
						echo "<option value=\"{$wineOption}\"{$selected}>{$wineOption}</option>";
					}
					?>
				</select>
			</div>
		<?php endif; ?>
		
		<?php if ($meal->allowed_dessert == 1): ?>
			<div class="mb-3">
				<input class="form-check-input"
				   id="dessert"
				   value="1"
				   type="checkbox"
				   <?= (
					   !$meal->isCutoffValid(true) // cutoff reached → disable
					   || (
						   !$booking->dessert // only care if box not already checked
						   && !$meal->hasGuestDessertCapacity(count($booking->guests(), true) // not enough slots for dessert
						   )
					   )
				   ) ? 'disabled' : '' ?>
				   <?= $booking->dessert == "1" ? "checked" : "" ?>>
				<label for="dessert" class="form-label">
					Dessert <i><?= ($meal->hasGuestDessertCapacity(count($booking->guests()), false) ? '(applies to your guests)' : 'Unavailable capacity') ?></i>
					<?= ($meal->hasDessertCapacity(false) ? '' : '<span class="badge rounded-pill text-bg-danger">Capacity Reached</span>') ?>
					
				</label>
			</div>
		<?php endif; ?>
		
		<div class="mb-3">
		  <button type="submit" class="btn booking-update-btn <?= $meal->canBook(true) ? 'btn-primary' : 'btn-secondary disabled' ?> w-100" data-booking_uid="<?= $booking->uid ?>"><?= $meal->canBook(true) ? 'Update Booking Preferences' : 'Deadline Passed' ?></button>
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
			$output .= '<h6 class="mb-1">' . htmlspecialchars($guest['guest_name'] ?? '') . '</h6>';
			
			$output .= '<a href="#" class="open-guest-modal" 
					data-bs-toggle="modal" 
					data-bs-target="#addEditGuestModal"
					data-action="edit" 
					data-guest_uid="' . $guest['guest_uid'] . '">
			  <i class="bi bi-pencil-square"></i>
			</a>';
			
			$output .= '</div>';
		
			// Dietary info (if any)
			if (!empty(array_filter($guest['guest_dietary'] ?? []))) {
				$output .= '<small class="text-muted d-block mb-1">Dietary: ' . 
						   htmlspecialchars(implode(', ', array_filter($guest['guest_dietary']))) . '</small>';
			}
		
			// Wine/Dessert
			$wineDessert = [];
			if (($guest['guest_charge_to'] ?? null) === "Domus") {
				$wineDessert[] = '<span><i class="bi bi-mortarboard me-2"></i>' . htmlspecialchars($guest['guest_domus_reason'] ?? '') . '</span>';
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
		
		if ($meal->hasGuestCapacity(count($booking->guests()), true) || $meal->canBook(true)) {
			echo "<button type=\"button\" class=\"btn btn-primary w-100 mb-3\" data-bs-toggle=\"modal\" data-bs-target=\"#addEditGuestModal\">Add Guest</button>";
		}
		?>
		
		<hr>
	</div>
</div>



<!-- Add Guest Modal -->
<div class="modal fade" id="addEditGuestModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- AJAX content will go here -->
			<?php
			  //$guestUID = null;      // or ''
			  //$guest     = [];       // empty guest
			  //include '_guest_modal.php';
			  ?>
		</div>
	</div>
</div>

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
				<button type="submit" class="btn <?= $meal->canBook(true) ? 'btn-danger' : 'btn-secondary disabled' ?> booking-delete-btn" data-booking_uid="<?= $booking->uid; ?>"><?= $meal->canBook(true) ? 'Delete Booking' : 'Deadline Passed' ?></button>
			</div>
		</div>
	</div>
</div>

<script>
const guestModalEl = document.getElementById('addEditGuestModal');

guestModalEl.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;

  // Default to 'add'
  let action = 'add';
  let guestUID = '';

  if (button) {
	action = button.getAttribute('data-action') || 'add';
	guestUID = button.getAttribute('data-guest_uid') || '';
  }

  // Build URL
  const url = new URL('new/ajax/guest_modal.php', window.location.origin);
  url.search = new URLSearchParams({
	action: action,
	guest_uid: guestUID,
	booking_uid: <?= json_encode($booking->uid) ?>
  });

  // Fetch and insert modal content
  fetch(url)
	.then(res => res.text())
	.then(html => {
	  guestModalEl.querySelector('.modal-content').innerHTML = html;

	  // --- DOMUS Reason Logic ---
	  const chargeEl = guestModalEl.querySelector('#guest_charge_to');
	  const reasonEl = guestModalEl.querySelector('#guest_domus_reason');
	  if (chargeEl && reasonEl) {
		// Initial visibility
		reasonEl.classList.toggle('d-none', chargeEl.value !== 'Domus');
		reasonEl.required = chargeEl.value === 'Domus';

		// Listen for changes
		chargeEl.addEventListener('change', () => {
		  if (chargeEl.value === 'Domus') {
			reasonEl.classList.remove('d-none');
			reasonEl.required = true;
		  } else {
			reasonEl.classList.add('d-none');
			reasonEl.required = false;
			reasonEl.value = '';
		  }
		});
	  }

	  // --- Dietary Options Max Logic (optional, if you have limits) ---
	  const dietaryContainer = guestModalEl.querySelector('.accordion-body[data-max]');
	  if (dietaryContainer) {
		const max = parseInt(dietaryContainer.dataset.max, 10) || 0;
		const checkboxes = dietaryContainer.querySelectorAll('input[type="checkbox"]');
		checkboxes.forEach(cb => {
		  cb.addEventListener('change', () => {
			const checkedCount = Array.from(checkboxes).filter(i => i.checked).length;
			checkboxes.forEach(i => {
			  if (!i.checked) i.disabled = checkedCount >= max;
			  else i.disabled = false;
			});
		  });
		});
	  }

	  // --- Wine Choice Logic (optional) ---
	  // Example: enforce a default if none selected
	  const wineRadios = guestModalEl.querySelectorAll('input[name="guest_wine_choice"]');
	  if (wineRadios.length) {
		const hasChecked = Array.from(wineRadios).some(r => r.checked);
		if (!hasChecked) wineRadios[0].checked = true;
	  }
	})
	.catch(err => {
	  console.error('Error loading modal:', err);
	  guestModalEl.querySelector('.modal-content').innerHTML =
		'<p class="p-3 text-danger">Failed to load content.</p>';
	});
});









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

<script>
  const chargeEl = document.getElementById('charge_to');
  const reasonEl = document.getElementById('domus_reason');

  // Initial visibility
  if (chargeEl.value === 'Domus') {
	reasonEl.classList.remove('d-none');
	reasonEl.required = true;
  }

  // Change listener
  chargeEl.addEventListener('change', () => {
	if (chargeEl.value === 'Domus') {
	  reasonEl.classList.remove('d-none');
	  reasonEl.required = true;
	} else {
	  reasonEl.classList.add('d-none');
	  reasonEl.required = false;
	  reasonEl.value = '';
	}
  });
  

</script>