<?php
$mealUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);

$user->pageCheck('meals');
$meal = new Meal($mealUID);
$meals = new Meals();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$meal->update($_POST);
	$meal = new Meal($mealUID);
}


echo "<p>totalDiners: " . $meal->totalDiners() . "</p>";
echo "<p>totalDessertDiners: " . $meal->totalDessertDiners() . "</p><hr>";
echo "<p>hasCapacity: " . $meal->hasCapacity() . "</p>";
echo "<p>hasDessertCapacity: " . $meal->hasDessertCapacity() . "</p>";
echo "<p>isCutoffValid: " . $meal->isCutoffValid() . "</p>";
echo "<p>hasGuestCapacity: " . $meal->hasGuestCapacity() . "</p>";
echo "<p>canBook: " . $meal->canBook() . "</p>";


echo pageTitle(
	$meal->name(),
	formatDate($meal->date_meal) . ", " . $meal->location,
	[
		[
			'permission' => 'meals',
			'title' => 'Guest List',
			'class' => '',
			'event' => 'index.php?page=guestlist&uid=' . $meal->uid,
			'icon' => 'calendar2-week'
		],
		[
			'permission' => 'meals',
			'title' => 'Delete Meal',
			'class' => 'text-danger',
			'event' => '',
			'icon' => 'x-octagon',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteMealModal'
			]
		]
	]
);
?>

<div class="row">
	<div class="col-md-7 col-lg-8">
		<h4>Meal Details</h4>
		
		<form method="post" id="mealEditForm" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
			<div class="row">
				<div class="col-4 mb-3">
					<div class="mb-3">
						<label for="type" class="form-label">Type</label>
						<select class="form-select" name="type" id="type" required>
							<?php
							$mealTypes = explode(',', $settings->get('meal_types'));
							
							foreach ($mealTypes as $type) {
								$type = trim($type);
								$selected = ($type === $meal->$type) ? ' selected' : '';
								echo "<option value=\"{$type}\"{$selected}>{$type}</option>";
							}
							?>
						</select>
						<div class="invalid-feedback">
							Title is required.
						</div>
					</div>
				</div>
				<div class="col-8 mb-3">
					<div class="mb-3">
						<label for="name" class="form-label">Name</label>
						<input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $meal->name; ?>" required>
						<div class="invalid-feedback">
							Valid meal name is required.
						</div>
					</div>
				</div>
			</div>
			
			<div class="mb-3">
				<?php
				?>
				
				<label for="location" class="form-label">Location</label>
				<input type="text" class="form-control" list="locations" name="location" id="location" placeholder="Location" value="<?php echo $meal->location; ?>" required>
				<div class="invalid-feedback">
					Valid meal location is required.
				</div>
				
				<datalist id="locations">
				  <?php
				  foreach ($meals->locations() as $location) {
					echo "<option value=\"" . $location . "\">";
				  }
				  ?>
				</datalist>
			</div>
			
			<hr>
			
			<div class="row">
				<div class="col-6 mb-3">
					<label for="date_meal" class="form-label">Meal Date/Time</label>
					<div class="input-group">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_meal" id="date_meal" placeholder="" value="<?php echo $meal->date_meal; ?>" required>
					</div>
					<div class="invalid-feedback">
						Meal date is required.
					</div>
				</div>
				
				<div class="col-6 mb-3">
					<label for="date_cutoff" class="form-label">Meal Date/Time Cut-Off</label>
					<div class="input-group">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_cutoff" id="date_cutoff" placeholder="" value="<?php echo $meal->date_cutoff; ?>" required>
					</div>
					<div class="invalid-feedback">
						Meal cut-off date is required.
					</div>
				</div>
			</div>
			
			<hr>
			
			<div class="row">
				<div class="col">
					<div class="accordion" id="accordionAllowed">
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Allowed Groups</button>
							</h2>
							<div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionAllowed">
								<div class="accordion-body">
									<strong>Select none for everyone to be allowed, otherwise only those member types selected can book this meal</strong>
									
									<?php
									$memberTypes = explode(',', $settings->get('member_categories'));
									$mealTypesAllowed = $meal->allowed ? explode(',', $meal->allowed) : [];
									
									foreach ($memberTypes as $i => $memberType) {
										$checked = in_array($memberType, $mealTypesAllowed) ? ' checked' : '';
										?>
										<div class="form-check">
											<input class="form-check-input" type="checkbox"
												   value="<?= htmlspecialchars($memberType) ?>"
												   name="allowed[]"
												   id="flexCheckDefault_<?= $i ?>"
												   <?= $checked ?>>
											<label class="form-check-label" for="flexCheckDefault_<?= $i ?>">
												<?= htmlspecialchars($memberType) ?>
											</label>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<hr>
			
			<div class="row mb-3">
				<div class="col">
					<label for="scr_capacity" class="form-label">Capacity</label>
					<input type="number" class="form-control" name="scr_capacity" id="scr_capacity" value="<?php echo $meal->scr_capacity; ?>" min="0" required="">
					<div class="invalid-feedback">
						SCR Capacity is required.
					</div>
				</div>
				
				<div class="col mb-3">
					<label for="scr_dessert_capacity" class="form-label">Dessert Capacity</label>
					<input type="number" class="form-control" name="scr_dessert_capacity" id="scr_dessert_capacity" value="<?php echo $meal->scr_dessert_capacity; ?>" min="0" required="">
					<div class="invalid-feedback">
						SCR Dessert Capacity is required.
					</div>
				</div>
				
				<div class="col mb-3">
					<label for="scr_guests" class="form-label">Guests</label>
					<input type="number" class="form-control" name="scr_guests" id="scr_guests" value="<?php echo $meal->scr_guests; ?>" min="0" required="">
					<div id="scr_guestsHelp" class="form-text">Per member</div>
					<div class="invalid-feedback">
						SCR Guests is required.
					</div>
				</div>
			</div>
			
			<hr>
			
			<div class="row mb-3">
				<div class="col">
					<label for="menu" class="form-label">Menu</label>
					<textarea rows="4" class="form-control" name="menu" id="menu"><?php echo $meal->menu;?></textarea>
				</div>
			</div>
			
			<div class="row mb-3">
				<div class="col">
					<label for="menu" class="form-label">Notes (Private)</label>
					<textarea rows="4" class="form-control" name="notes" id="notes"><?php echo $meal->notes;?></textarea>
				</div>
			</div>
			
			<div class="mb-3">
				<div class="accordion" id="accordionPhotograph">
					<div class="accordion-item">
						<h2 class="accordion-header">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">Photograph</button>
						</h2>
						<div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionPhotograph">
							<div class="accordion-body">
								<?php foreach ($meals->cardImages() as $cardImage): ?>
									<div class="col">
										<div class="card mb-3">
											<img src="<?= htmlspecialchars($cardImage) ?>" class="card-img-top" alt="...">
											<div class="card-body">
												<p class="card-text">
													<label class="form-label">
														<input class="form-check-input" type="radio" 
															   name="photo" 
															   value="<?= basename($cardImage) ?>" 
															   <?= ($meal->photographURL() === htmlspecialchars($cardImage)) ? 'checked' : '' ?>>
														<?= basename($cardImage) ?>
													</label>
												</p>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="mb-3">
				<label for="type" class="form-label">Default Charge-To</label>
				<select class="form-select" name="charge_to" id="charge_to" required>
					<?php
					$chargeToOptions = explode(',', $settings->get('booking_charge-to'));
					
					foreach ($chargeToOptions as $charge_to) {
						$charge_to = trim($charge_to);
						$selected = ($charge_to === $meal->charge_to) ? ' selected' : '';
						echo "<option value=\"{$charge_to}\"{$selected}>{$charge_to}</option>";
					}
					?>
				</select>
				<div id="charge_toHelp" class="form-text">* wine charged via Battels</div>
				<div class="invalid-feedback">
					Default Charge-To is required.
				</div>
			</div>
			
			<div class="mb-3">
				<div class="form-check form-switch">
					<?php
					$checked = ($meal->allowed_wine === 1) ? ' checked' : '';
					?>
					<input class="form-check-input" type="checkbox" id="allowed_wine" name="allowed_wine" value="1" <?= $checked ?> role="switch">
					<label class="form-check-label" for="email_reminders">Wine Available</label>
				</div>
				<div class="form-check form-switch">
					<?php
					$checked = ($meal->allowed_dessert === 1) ? ' checked' : '';
					?>
					<input class="form-check-input" type="checkbox" id="allowed_dessert" name="allowed_dessert" value="1" <?= $checked ?> role="switch">
					<label class="form-check-label" for="email_reminders">Dessert Available</label>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Update</button>
		</form>
	</div>
	<div class="col-md-5 col-lg-4">
		<?php
		$Bookings = $meal->bookings();
		?>
		<h4 class="d-flex justify-content-between align-items-center mb-3">
		  <span>Bookings</span>
		  <span class="badge <?= ($meal->totalDiners() > $meal->scr_capacity) ? 'bg-danger' : 'bg-secondary'; ?> rounded-pill"><?php echo $meal->totalDiners(); ?></span>
		</h4>
		<ul class="list-group mb-3">
			<?php
			foreach ($Bookings as $booking) {
				echo $booking->displayMealListGroupItem();
			}
			?>
		</ul>
		
		<div class="text-end">
			<a class="btn btn-sm btn-outline-light" href="report.php" role="button" onkeydown="if(event.key === 'Enter' || event.key === ' ') this.click();"><i class="bi bi-download"></i> export COMING SOON</a>
		</div>
		
		<h4 class="mb-3">Bookings by Day</h4>
		<div>
			<canvas id="chart_bookingsByDay"></canvas>
		</div>
	</div>
</div>


<!-- Delete Member Modal -->
<div class="modal fade" tabindex="-1" id="deleteMealModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="index.php?page=meals">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Meal <span class="text-danger"><strong>WARNING!</strong></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><span class="text-danger"><strong>WARNING!</strong></span> Are you sure you want to delete this meal?</p>
				<p>This will also delete <strong>all</strong> bookings for this meal.</p>
				<p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
				<input type="text" class="form-control mb-3" id="delete_confirm" placeholder="Type 'DELETE' to confirm" oninput="enableOnExactMatch('delete_confirm', 'delete_button', 'DELETE')">
				
				<div class="modal-footer">
					<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger" id="delete_button" disabled>Delete Meal</button>
					<input type="hidden" name="deleteMealUID" value="<?= $meal->uid; ?>">
				</div>
			</div>
		</div>
	</div>
	</form>
</div>

<?php
$points = [];
$cumulative = 0;

// First, extract bookings into temporary array
$temp = [];
foreach ($meal->bookings() as $booking) {

	$date = date('Y-m-d', strtotime($booking->date));

	$guestCount = 0;
	if (!empty($booking->guests_array)) {
		$guestArray = json_decode($booking->guests_array, true);
		$guestCount = is_array($guestArray) ? count($guestArray) : 0;
	}

	$total = 1 + $guestCount;

	// Group totals by date (multiple bookings same day)
	if (!isset($temp[$date])) {
		$temp[$date] = 0;
	}
	$temp[$date] += $total;
}

// Sort dates
ksort($temp);

// Find date range
$start = array_key_first($temp);
$end   = array_key_last($temp);

$current = new DateTime($start);
$endDate = new DateTime($end);

// Build full day-by-day set including missing days
while ($current <= $endDate) {

	$day = $current->format('Y-m-d');

	if (isset($temp[$day])) {
		$cumulative += $temp[$day];
	}

	$points[] = [
		'date' => $day,
		'cumulative' => $cumulative,
	];

	$current->modify('+1 day');
}
?>

<script>
const ctx = document.getElementById('chart_bookingsByDay');
const labels = <?= json_encode(array_column($points, 'date')) ?>;
const data = <?= json_encode(array_column($points, 'cumulative')) ?>;

new Chart(ctx, {
	type: 'line',
	data: {
		labels: labels,
		datasets: [{
			label: 'Bookings',
			data: data,
			borderWidth: 2,
			tension: 0.3,
			pointRadius: 0,
			pointHoverRadius: 0
		}]
	},
	options: {
		plugins: { legend: { display: false } },
		scales: {
			x: { title: { display: false } },
			y: { beginAtZero: true, title: { display: false } }
		}
	}
});
</script>

<script>
const el = document.getElementById('date_meal');
const el2 = document.getElementById('date_cutoff');

const options = {
	defaultDate: '<?php echo $meal->date_meal; ?>',
	display: {
		icons: {
			type: 'icons',
			time: 'bi bi-clock',
			date: 'bi bi-calendar',
			up: 'bi bi-arrow-up',
			down: 'bi bi-arrow-down',
			previous: 'bi bi-chevron-left',
			next: 'bi bi-chevron-right',
			today: 'bi bi-calendar-check',
			clear: 'bi bi-trash',
			close: 'bi bi-close'
		},
		components: {
			calendar: true,
			date: true,
			month: true,
			year: true,
			decades: true,
			clock: true,
			hours: true,
			minutes: true,
			seconds: false
		}
	},
	localization: {
		format: 'yyyy-MM-dd HH:mm',
	  }
};

new tempusDominus.TempusDominus(el, options);
new tempusDominus.TempusDominus(el2, options);
</script>

<script>
let editor;
editor = SUNEDITOR.create(document.getElementById('menu'), {
	height: 100,
	buttonList: [
		['undo', 'redo'],
		['font', 'fontSize', 'formatBlock'],
		['bold', 'italic', 'underline', 'strike'],
		['fontColor', 'hiliteColor', 'align', 'list'],
		['table', 'link', 'image'],
		['fullScreen', 'codeView']
	]
});

// Sync content back to textarea on submit
document.getElementById('mealEditForm').addEventListener('submit', function(e) {
	document.getElementById('menu').value = editor.getContents();
});
</script>