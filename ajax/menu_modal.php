<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}
$mealUID = filter_input(INPUT_GET, 'mealUID', FILTER_VALIDATE_INT);
$meal = new Meal($mealUID);
?>

<div class="modal-body">
	<h3 class="text-center"><?= $meal->name ?></h3>
	<h5 class="text-secondary text-center mb-3">
		<i>
		<?= $meal->location ?>,
		<?= formatDate($meal->date_meal, 'long') ?>
		<?= formatTime($meal->date_meal) ?>
		</i>
	</h5>
	
	<ul class="nav nav-tabs nav-fill" id="mealTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu-tab-pane" type="button" role="tab" aria-controls="menu-tab-pane" aria-selected="true">Menu</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="diners-tab" data-bs-toggle="tab" data-bs-target="#diners-tab-pane" type="button" role="tab" aria-controls="diners-tab-pane" aria-selected="false">Diners <span class="badge rounded-pill text-bg-secondary"><?= $meal->totalDiners() ?></span></button>
		</li>
	</ul>
	
	<div class="tab-content" id="mealTabContent">
		<div class="tab-pane fade show active" id="menu-tab-pane" role="tabpanel" aria-labelledby="menu-tab" tabindex="0">
			
			<div class="text-center my-3">
				<?= !empty($meal->menu) ? nl2br($meal->menu) : "Menu not available" ?>
			</div>
		</div>
		
		<div class="tab-pane fade" id="diners-tab-pane" role="tabpanel" aria-labelledby="diners-tab" tabindex="0">
			<?php
			$output = '<ul class="my-3">';
			
			foreach ($meal->bookings() as $guestListBooking) {
				$member = Member::fromLDAP($guestListBooking->member_ldap);
			
				$output .= '<li>';
				
				// Person's name
				$output .= $member->public_displayName() . ' ';
			
				// Person's wine/dessert
				$wineDessert = [];
				if ($user->hasPermission('bookings') && $guestListBooking->wineChoice() != "None") {
					$wineDessert[] = '<svg class="bi" width="1em" height="1em" aria-hidden="true"><use xlink:href="assets/images/icons.svg#wine-glass"></use></svg>';
				}
				if ($guestListBooking->dessertChoice() == "1") {
					$wineDessert[] = '<i class="bi bi-cookie"></i>';
				}
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
						if ($guestListBooking->wineChoice() != "None" && !empty($guest['guest_wine'])) $guestWineDessert[] = '<svg class="bi" width="1em" height="1em" aria-hidden="true"><use xlink:href="assets/images/icons.svg#wine-glass"></use></svg>';
						if ($guestListBooking->dessertChoice() == "1") $guestWineDessert[] = '<i class="bi bi-cookie"></i>';
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
		</div>
	</div>
	
	<?php if ($meal->domus || $meal->allowed_wine || $meal->allowed_dessert): ?>
	<div class="d-flex justify-content-around align-items-center border-top pt-3 mt-4 small">
		<div class="text-center <?= ($meal->charge_to == "Domus") ? "" : "opacity-0" ?>">
			<div><i class="bi bi-mortarboard fs-4"></i></div>
			<div class="text-uppercase fw-semibold">Domus</div>
		</div>
	
		<div class="text-center <?= ($meal->allowed_wine == 1) ? "" : "opacity-0" ?>">
			<div><svg class="bi fs-4" width="1em" height="1em" aria-hidden="true"><use xlink:href="assets/images/icons.svg#wine-glass"></use></svg></div>
			<div class="text-uppercase fw-semibold">Wine</div>
		</div>
	
		<div class="text-center <?= ($meal->allowed_dessert == 1) ? "" : "opacity-0" ?>">
			<div><i class="bi bi-cookie fs-4"></i></div>
			<div class="text-uppercase fw-semibold">Dessert</div>
		</div>
	</div>
	<?php endif; ?>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
</div>
