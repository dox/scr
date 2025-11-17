<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/css/tempus-dominus.min.css" crossorigin="anonymous">
<!-- Popperjs -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<!-- Tempus Dominus JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/js/tempus-dominus.min.js" crossorigin="anonymous"></script>



<?php
$user->pageCheck('meals');
$meal = new Meal($_GET['uid']);
$meals = new Meals();

echo pageTitle(
	$meal->name(),
	formatDate($meal->date_meal) . ", " . $meal->location,
	[
		[
			'permission' => 'meals',
			'title' => 'Guest List',
			'class' => '',
			'event' => 'guestlist.php',
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
	<div class="col-8">
		<h4>Meal Details</h4>
		
		<form>
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
					<div class="input-group" id="datetimepicker">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_meal" id="date_meal" placeholder="" value="<?php echo formatDate($meal->date_meal, 'short') . " " . formatTime($meal->date_meal); ?>" required>
					</div>
					<div class="invalid-feedback">
						Meal date is required.
					</div>
				</div>
				
				<div class="col-6 mb-3">
					<label for="date_cutoff" class="form-label">Meal Date/Time Cut-Off</label>
					<div class="input-group" id="datetimepicker">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_cutoff" id="date_cutoff" placeholder="" value="<?php echo formatDate($meal->date_cutoff, 'short') . " " . formatTime($meal->date_cutoff); ?>" required>
					</div>
					<div class="invalid-feedback">
						Meal cut-off date is required.
					</div>
				</div>
			</div>
			
		</form>
	</div>
	<div class="col-4">
		<?php
		$Bookings = $meal->bookings();
		?>
		<h4 class="d-flex justify-content-between align-items-center mb-3">
		  <span>Bookings</span>
		  <span class="badge bg-secondary rounded-pill"><?php echo count($Bookings); ?></span>
		</h4>
		<ul class="list-group mb-3">
			<?php
			foreach ($Bookings as $booking) {
				echo $booking->displayMealListGroupItem();
			}
			?>
		</ul>
		
		<div class="text-end">
			<a class="btn btn-sm btn-outline-light" href="#" role="button"><i class="bi bi-download"></i> export COMING SOON</a>
		</div>
		
		<h4 class="mb-3">Bookings by Day</h4>
		<div>
			chart
			<canvas id="chart_bookingsByDay"></canvas>
		</div>
	</div>
</div>











<!-- Delete Meal Modal -->
<div class="modal fade" tabindex="-1" id="deleteMealModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Test Modal <span class="text-danger"><strong>WARNING!</strong></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				Test Modal
				<p><span class="text-danger"><strong>WARNING!</strong> Are you sure you want to delete this member?</p>
				<p>This will also delete <strong>all</strong> bookings (past and present) for this member.<p>
				<p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
			</div>
		</div>
	</div>
</div>


<script>
const el = document.getElementById('date_meal');
const el2 = document.getElementById('date_cutoff');

const options = {
	defaultDate: '<?php echo formatDate($meal->date_meal, 'short') . " " . formatTime($meal->date_meal); ?>',
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
		hourCycle: 'h24',
		format: '<?php echo strtolower($settings->get('datetime_format_short')); ?> HH:mm'
	}
};

new tempusDominus.TempusDominus(el, options);
new tempusDominus.TempusDominus(el2, options);
</script>