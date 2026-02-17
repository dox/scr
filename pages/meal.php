<?php
$mealUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);

$user->pageCheck('meals');

// Detect whether this is a new meal
$isNew = empty($mealUID);

if ($isNew) {
	$formURL = 'index.php?page=meals';
} else {
	$formURL = htmlspecialchars($_SERVER['REQUEST_URI']);
}

// Load existing or empty object
$meal = $isNew ? new Meal() : new Meal($mealUID);
$meals = new Meals();

$memberTypes = array_map('trim', explode(',', $settings->get('member_types')));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Save or update
	if ($isNew) {
		$newUID = $meal->create($_POST);        // Create new record
		//header("Location: index.php?page=meal&uid={$newUID}");
		exit;
	} else {
		$meal->update($_POST);                // Update existing
		$meal = new Meal($mealUID);           // Reload object
	}
}

// Title and action buttons
echo pageTitle(
	$isNew ? "Add New Meal" : $meal->name(),
	$isNew ? "" : formatDate($meal->date_meal) . ", " . $meal->location,
	$isNew ? [] : [
		[
			'permission' => 'meals',
			'title' => 'Guest List',
			'class' => '',
			'event' => 'index.php?page=guestlist&uid=' . $meal->uid,
			'icon' => 'card-list'
		],
		[
			'permission' => 'meals',
			'title' => 'Clone',
			'class' => '',
			'event' => './ajax/meal_template_modal.php?uid=' . $meal->uid,
			'icon' => 'copy',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#mealTemplateModal'
			]
		],
		[
			'divider' => true
		],
		[
			'permission' => 'meals',
			'title' => 'Delete Meal',
			'class' => 'text-danger',
			'event' => '',
			'icon' => 'trash3',
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
		
		<form method="post" id="mealEditForm" action="<?= $formURL ?>">
			<div class="row">
				<div class="col-4 mb-3">
					<label for="type" class="form-label">Type</label>
					<select class="form-select" name="type" id="type" required>
						<?php
						$mealTypes = explode(',', $settings->get('meal_types'));
						foreach ($mealTypes as $type) {
							$type = trim($type);
							$selected = ($meal->type === $type) ? ' selected' : '';
							echo "<option value=\"{$type}\"{$selected}>{$type}</option>";
						}
						?>
					</select>
					<div class="invalid-feedback">Type is required.</div>
				</div>
				
				<div class="col-8 mb-3">
					<label for="name" class="form-label">Name</label>
					<input type="text" class="form-control" name="name" id="name" 
						value="<?= htmlspecialchars($meal->name) ?>" required>
					<div class="invalid-feedback">Valid meal name is required.</div>
				</div>
			</div>
			
			<div class="mb-3">
				<label for="location" class="form-label">Location</label>
				<input type="text" class="form-control" list="locations" name="location" 
					value="<?= htmlspecialchars($meal->location) ?>" required>

				<datalist id="locations">
				  <?php
				  foreach ($meals->locations() as $location){
					  echo "<option value=\"{$location}\">";
				  }
				  ?>
				</datalist>
				<div class="invalid-feedback">Valid meal location is required.</div>
			</div>

			<hr>

			<div class="row">
				<div class="col-6 mb-3">
					<label for="date_meal" class="form-label">Meal Date/Time</label>
					<div class="input-group">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_meal" id="date_meal"
							   value="<?= htmlspecialchars($meal->date_meal) ?>" required>
					</div>
				</div>

				<div class="col-6 mb-3">
					<label for="date_cutoff" class="form-label">Cut-Off</label>
					<div class="input-group">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_cutoff" id="date_cutoff"
							   value="<?= htmlspecialchars($meal->date_cutoff) ?>" required>
					</div>
				</div>
			</div>

			<hr>
			
			<div class="card card-body mb-3">
			
			<?php foreach ($memberTypes as $memberType): ?>
			  <?php $limits = $meal->getCapacityForMemberType($memberType); ?>
			
			  <div class="row mb-3">
				<div class="col-12 mb-2">
				  <strong class="text-muted text-uppercase">
					<?= htmlspecialchars($memberType) ?>
				  </strong>
				</div>
			
				<div class="col">
				  <label class="form-label text-truncate">Capacity</label>
				  <input
					type="number"
					class="form-control"
					name="capacity[<?= htmlspecialchars($memberType) ?>][capacity]"
					value="<?= $limits['main'] ?>"
					min="0"
					required
				  >
				</div>
			
				<div class="col">
				  <label class="form-label text-truncate">Dessert Capacity</label>
				  <input
					type="number"
					class="form-control"
					name="capacity[<?= htmlspecialchars($memberType) ?>][dessert_capacity]"
					value="<?= $limits['dessert'] ?>"
					min="0"
					required
				  >
				</div>
			
				<div class="col">
				  <label class="form-label text-truncate">Guests</label>
				  <input
					type="number"
					class="form-control"
					name="capacity[<?= htmlspecialchars($memberType) ?>][guests]"
					value="<?= $limits['guests'] ?>"
					min="0"
					required
				  >
				</div>
			  </div>
			<?php endforeach; ?>
			</div>
			
			<div class="mb-3">
				<label class="form-label">Default Charge-To</label>
				<select class="form-select" name="charge_to" required>
					<?php
					$options = explode(',', $settings->get('booking_charge-to'));
					foreach ($options as $opt) {
						$opt = trim($opt);
						$selected = ($meal->charge_to === $opt) ? ' selected' : '';
						echo "<option value=\"{$opt}\"{$selected}>{$opt}</option>";
					}
					?>
				</select>
				<div class="form-text">* Wine charged via Battels</div>
			</div>
			
			<div class="form-check form-switch">
				<input type="checkbox" class="form-check-input" name="allowed_wine" value="1"
					<?= $meal->allowed_wine ? 'checked' : '' ?>>
				<label class="form-check-label">Wine Available</label>
			</div>
			
			<div class="form-check form-switch mb-4">
				<input type="checkbox" class="form-check-input" name="allowed_dessert" value="1"
					<?= $meal->allowed_dessert ? 'checked' : '' ?>>
				<label class="form-check-label">Dessert Available</label>
			</div>
			
			<!-- Allowed groups accordion -->
			<div class="accordion" id="accordionAllowed">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
								data-bs-target="#collapseOne">Allowed Groups</button>
					</h2>
					<div id="collapseOne" class="accordion-collapse collapse">
						<div class="accordion-body">
							<strong>Select none for everyone to be allowed</strong><br>
							<?php
							$memberCategories = explode(',', $settings->get('member_categories'));
							$allowed = $meal->allowed ? explode(',', $meal->allowed) : [];
							foreach ($memberCategories as $i => $memberCategory) {
								$checked = in_array($memberCategory, $allowed) ? ' checked' : '';
								echo "
								<div class='form-check'>
								  <input type='checkbox' class='form-check-input' name='allowed[]' 
									value='".htmlspecialchars($memberCategory)."' id='check_{$i}'{$checked}>
								  <label class='form-check-label' for='check_{$i}'>" . htmlspecialchars($memberCategory) . "</label>
								</div>";
							}
							?>
						</div>
					</div>
				</div>
			</div>
			
			<hr>

			<div class="mb-3">
				<label for="menu" class="form-label">Menu</label>
				<textarea class="form-control" rows="4" name="menu" id="menu"><?= htmlspecialchars($meal->menu ?? '') ?></textarea>
			</div>

			<div class="mb-3">
				<label for="notes" class="form-label">Notes <span class="badge rounded-pill text-bg-warning">Private</span></label>
				<textarea class="form-control" rows="4" name="notes"><?= htmlspecialchars($meal->notes) ?></textarea>
			</div>

			<hr>

			<!-- Photo select -->
			<div class="accordion" id="accordionPhotograph">
				<div class="accordion-item">
					<h2 class="accordion-header">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
								data-bs-target="#photoSelect">Photograph</button>
					</h2>
					<div id="photoSelect" class="accordion-collapse collapse">
						<div class="accordion-body">
							<div class="row">
								<?php foreach ($meals->cardImages() as $cardImage): ?>
									<div class="col-6 col-md-4">
										<div class="card mb-3">
											<img src="<?= htmlspecialchars($cardImage) ?>" class="card-img-top">
											<div class="card-body">
												<label class="text-truncate">
													<input type="radio" name="photo"
														value="<?= basename($cardImage) ?>"
														<?= ($meal->photographURL() === $cardImage) ? 'checked' : '' ?>>
													<?= basename($cardImage) ?>
												</label>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<hr>

			

			<button type="submit" class="btn btn-primary w-100 mb-3">
				<?= $isNew ? 'Create Meal' : 'Update Meal' ?>
			</button>
			<?= $isNew ? '<input type="hidden" name="createMeal" value="1">' : '' ?>
		</form>
	</div>

	<?php
	// Render everything into $output (no in/out of PHP)
	$output = '';
	
	// Only render member-type blocks when not a new meal
	if (!$isNew) {
		$memberTypes = array_map('trim', explode(',', $settings->get('member_types')));
		
		$output .= '<div class="col-md-5 col-lg-4 mb-4">';
		foreach ($memberTypes as $memberType) {
			$memberTypeKey = strtolower($memberType);
			$limits = $meal->getCapacityForMemberType($memberTypeKey);
			$bookings = $meal->bookings($memberTypeKey);
			$total = $meal->totalDiners($memberTypeKey);
			$badgeClass = ($total > $limits['main']) ? 'bg-danger' : 'bg-secondary';
			
			if (!empty($bookings)) {
				$output .= '<h4 class="d-flex justify-content-between align-items-center mb-3">';
				$output .= '<span>' . htmlspecialchars(ucfirst($memberType)) . ' Diners</span>';
				$output .= '<span class="badge ' . $badgeClass . ' rounded-pill">' . (int)$total . '</span>';
				$output .= '</h4>';
				
				$output .= '<ul class="list-group mb-3">';
				
				foreach ($bookings as $booking) {
					$output .= $booking->displayMealListGroupItem();
				}
				
				$output .= '</ul>';
			}
		}
		
		// Always render the global export (as in your original snippet) and overall chart if desired
		$output .= '<div class="text-end">';
		$output .= '<a class="btn btn-sm btn-outline-light" href="report.php?page=meal_bookings&uid=' . rawurlencode($meal->uid) . '">';
		$output .= '<i class="bi bi-download"></i> export';
		$output .= '</a>';
		$output .= '</div>';
		
		$output .= '<h4 class="mb-3">Bookings by Day</h4>';
		$output .= '<canvas id="chart_bookingsByDay"></canvas>';
		$output .= '</div>'; // close column
	}
	
	
	// finally echo or return $output where appropriate
	echo $output;
	?>

<?php if (!$isNew): ?>
<!-- Delete Modal -->
<div class="modal fade" id="deleteMealModal" tabindex="-1" aria-hidden="true">
	<form method="post" action="index.php?page=meals">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Delete Meal <span class="text-danger"><strong>WARNING</strong></span></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<p>This will delete any bookings for this meal. It will not notify anyone who may have booked.</p>
					<p><strong class="text-danger">This action cannot be undone.</strong></p>
					<input type="text" class="form-control mb-3"
						placeholder="Type 'DELETE' to confirm"
						id="delete_confirm"
						oninput="enableOnExactMatch('delete_confirm', 'delete_button', 'DELETE')">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger" id="delete_button" disabled>Delete Meal</button>
					<input type="hidden" name="deleteMealUID" value="<?= $meal->uid; ?>">
				</div>
			</div>
		</div>
	</form>
</div>
<?php endif; ?>



<div class="modal fade" id="mealTemplateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
	  <div class="modal-content">
		  <div class="modal-header">
			  <h5 class="modal-title">Clone Meal</h5>
			  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
		  </div>
		  
		  <form id="meal-template-form">
		  <div class="modal-body">
				  <!-- Hidden source meal -->
				  <input type="hidden" name="template_meal_uid" value="<?= $meal->uid ?>">
				  
				  <!-- Week selection -->
				  <div class="mb-3">
					  <label for="date_start" class="form-label">Week commencing</label>
					  <div class="input-group" id="datetimepicker">
						  <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						  <input type="text" class="form-control" name="template_week_start" id="template_week_start" placeholder="" value="<?= $terms->firstDayOfWeek(); ?>" required>
					  </div>
					  <small id="date_startHelp" class="form-text text-muted">Choose the Sunday of the week you want to apply this meal to.</small>
				  </div>
				  
				  <div class="mb-3">
					  <label for="week_count" class="form-label">Apply for how many weeks?</label>
					  <select class="form-select" id="week_count" name="week_count">
						  <option value="1">Just this week</option>
						  <option value="2">2 weeks</option>
						  <option value="3">3 weeks</option>
						  <option value="4">4 weeks</option>
						  <option value="5">5 weeks</option>
						  <option value="6">6 weeks</option>
						  <option value="7">7 weeks</option>
						  <option value="8">8 weeks</option>
						  <option value="9">9 weeks</option>
					  </select>
				  </div>
				  
				  
				  <!-- Day selection -->
				  <label for="template_days" class="form-label">Days to apply</label>		
				  <div class="mb-3">
					  <?php
					  $days = [
						  'sunday'    => 'Sunday',
						  'monday'    => 'Monday',
						  'tuesday'   => 'Tuesday',
						  'wednesday' => 'Wednesday',
						  'thursday'  => 'Thursday',
						  'friday'    => 'Friday',
						  'saturday'  => 'Saturday',
					  ];
				  
					  foreach ($days as $value => $label):
						  $id = substr($value, 0, 3); // sun, mon, tue…
					  ?>
						  <div class="form-check">
							  <input
								  class="form-check-input"
								  type="checkbox"
								  name="template_days[]"
								  value="<?= $value ?>"
								  id="<?= $id ?>">
							  <label class="form-check-label" for="<?= $id ?>">
								  <?= $label ?>
							  </label>
						  </div>
					  <?php endforeach; ?>
				  </div>
				  
				  
				  <div class="form-text mb-3">
					  The meal will be copied onto each selected day (excluding 'Menu').
				  </div>
				  
				  <hr>
				  
				  <div id="template_result" class="mb-3 d-none" role="alert"></div>
		  </div>
		  
		  <!-- Footer -->
		  <div class="modal-footer">
			  <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
			  <button
				  type="button"
				  class="btn btn-primary meal-template-apply-btn"
				  data-meal_uid="<?= $meal->uid ?>">
				  Clone Meal to Selected Dates
			  </button>
		  </div>
		  
		  </form>
	  </div>
  </div>
</div>

<?php if (!$isNew && count($meal->bookings()) > 0):

// Gather raw per-member-type totals grouped by date
$perTypeByDate = [];    // [memberType => [ 'YYYY-MM-DD' => totalOnThatDay, ... ], ...]
$allDates = [];         // collect all dates across member types

foreach ($memberTypes as $memberType) {
	$perTypeByDate[$memberType] = [];

	foreach ($meal->bookings($memberType) as $booking) {
		$date = date('Y-m-d', strtotime($booking->date));

		$guestCount = 0;
		if (!empty($booking->guests_array)) {
			$guestArray = json_decode($booking->guests_array, true);
			$guestCount = is_array($guestArray) ? count($guestArray) : 0;
		}

		$total = 1 + $guestCount;

		if (!isset($perTypeByDate[$memberType][$date])) {
			$perTypeByDate[$memberType][$date] = 0;
		}
		$perTypeByDate[$memberType][$date] += $total;

		$allDates[$date] = true;
	}
}

// If there were bookings but no member-specific bookings (edge-case), fall back to global bookings
if (empty($allDates)) {
	foreach ($meal->bookings() as $booking) {
		$date = date('Y-m-d', strtotime($booking->date));
		$allDates[$date] = true;
	}
}

// If still empty, nothing to chart
if (empty($allDates)):
	?>
	<p>No booking dates found to chart.</p>
	<?php
else:

// Build full date range from earliest to latest date
$datesSorted = array_keys($allDates);
sort($datesSorted, SORT_STRING);
$start = reset($datesSorted);
$end = end($datesSorted);

$startDt = new DateTime($start);
$endDt = new DateTime($end);

// build ordered labels (YYYY-MM-DD) covering missing days too
$labels = [];
$current = clone $startDt;
while ($current <= $endDt) {
	$labels[] = $current->format('Y-m-d');
	$current->modify('+1 day');
}

// Now for each memberType build cumulative series aligned to $labels
$datasets = [];

$colorIndex = 0;
foreach ($memberTypes as $memberType) {
	$daily = [];      // totals per day (not cumulative yet) for this memberType
	foreach ($labels as $day) {
		$daily[$day] = $perTypeByDate[$memberType][$day] ?? 0;
	}

	// cumulative
	$cumulative = 0;
	$cumulativeSeries = [];
	foreach ($daily as $day => $val) {
		$cumulative += $val;
		$cumulativeSeries[] = $cumulative;
	}

	$datasets[] = [
		'label' => $memberType,
		'data' => $cumulativeSeries,
		'borderWidth' => 2,
		'tension' => 0.3,
		'pointRadius' => 0,
		'pointHoverRadius' => 0
	];
}

?>

<script>
const ctx = document.getElementById('chart_bookingsByDay');
const labels = <?= json_encode($labels) ?>;
const datasets = <?= json_encode($datasets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

new Chart(ctx, {
	type: 'line',
	data: {
		labels: labels,
		datasets: datasets
	},
	options: {
		plugins: { legend: { display: true } },
		scales: {
			x: { title: { display: false } },
			y: { beginAtZero: true, title: { display: false } }
		}
	}
});
</script>

<?php
endif; // end if there are dates
endif; // end outer condition
?>

<script>
const icons = {
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
};

const baseDisplay = {
	icons: icons,
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
};

const dateTimeOptions = {
	defaultDate: new Date('<?= date('c') ?>'),
	display: baseDisplay,
	localization: {
		format: 'yyyy-MM-dd HH:mm'
	}
};

const sundayOnlyOptions = {
	display: {
		icons: icons,
		components: {
			calendar: true,
			date: true,
			month: true,
			year: true,
			decades: false,
			clock: false
		}
	},
	localization: {
		format: 'yyyy-MM-dd'
	},
	restrictions: {
		daysOfWeekDisabled: [1,2,3,4,5,6]
	}
};

new tempusDominus.TempusDominus(
	document.getElementById('date_meal'),
	dateTimeOptions
);

new tempusDominus.TempusDominus(
	document.getElementById('date_cutoff'),
	dateTimeOptions
);

new tempusDominus.TempusDominus(
	document.getElementById('template_week_start'),
	sundayOnlyOptions
);
</script>

<script>
let editor = SUNEDITOR.create(document.getElementById('menu'), {
	height: 100,
	buttonList: [
		['undo', 'redo'],
		['font', 'fontSize', 'formatBlock'],
		['bold', 'italic', 'underline', 'strike'],
		['fontColor', 'hiliteColor', 'align', 'list'],
		['table', 'link', 'image'],
		['fullScreen', 'codeView']
	],
	callbacks: {
		onFocus: function () {
			if (this.getText().trim() === '') {
				this.setContents('');
			}
		}
	}
});

// Sync content back to textarea on submit
document.getElementById('mealEditForm').addEventListener('submit', function () {
	let contents = editor.getContents(true).trim();

	if (
		contents === '' ||
		contents === '<p><br></p>' ||
		contents === '<p>&nbsp;</p>'
	) {
		contents = '';
	}

	document.getElementById('menu').value = contents;
});
</script>
