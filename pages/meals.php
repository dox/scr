<?php
$user->pageCheck('meals');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['deleteMealUID'])) {
		$deleteMealUID = filter_input(INPUT_POST, 'deleteMealUID', FILTER_SANITIZE_NUMBER_INT);
		
		$meal = new Meal($deleteMealUID);
		$meal->delete();
	} elseif(isset($_POST['createMeal'])) {
		unset($_POST['createMeal']);
		$meals->create($_POST);
	}
}

echo pageTitle(
	"Meals",
	"All meals both past and present",
	[
		[
			'permission' => 'meals',
			'title' => 'Add new',
			'class' => '',
			'event' => 'index.php?page=meal',
			'icon' => 'plus-circle'
		]
	]
);

// Set default dates
$currentTerm = $terms->currentTerm();
$defaultFrom = $currentTerm->date_start;
$defaultTo   = $currentTerm->date_end;

// Sanitize POST inputs
$dateFrom = isset($_POST['date_from']) ? htmlspecialchars($_POST['date_from']) : $defaultFrom;
$dateTo   = isset($_POST['date_to'])   ? htmlspecialchars($_POST['date_to'])   : $defaultTo;

// Optional: ensure the dates are valid and in the correct order
if (strtotime($dateFrom) > strtotime($dateTo)) {
	// Swap if user entered backwards
	[$dateFrom, $dateTo] = [$dateTo, $dateFrom];
}
?>

<form method="post" id="mealsBetweenDates" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="row align-items-end">
		<div class="col">
			<label for="dateFrom" class="form-label">Date From</label>
			<input type="text" class="form-control" id="dateFrom" name="date_from" value="<?= $dateFrom ?>" required>
		</div>
		<div class="col">
			<label for="dateTo" class="form-label">Date To</label>
			<input type="text" class="form-control" id="dateTo" name="date_to" value="<?= $dateTo ?>" required>
		</div>
		<div class="col">
			<button type="submit" class="btn btn-primary mt-3 w-100">Submit</button>
		</div>
	</div>
</form>

<div class="row mt-3">
	<?php
	$mealsList = $meals->betweenDates($dateFrom, $dateTo);
	krsort($mealsList);
	
	if (!empty($mealsList)) {
		echo '<div class="table-responsive mt-3">';
		echo '<table class="table table-striped">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col">Date</th>';
		echo '<th scope="col">Meal Name</th>';
		echo '<th scope="col">Location</th>';
		echo '<th scope="col">Diners</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
	
		foreach ($mealsList as $meal) {
			$link = "index.php?page=meal&uid=" . $meal->uid;
			
			$menu = !empty($meal->menu)
			? '<a href="#" class="load-remote-menu px-3" id="menuUID-' . $meal->uid . '" data-url="./ajax/menu_modal.php?mealUID=' . $meal->uid . '" data-bs-toggle="modal" data-bs-target="#menuModal"><i class="bi bi-info-circle"></i></a>'
			: null;
			
			
			echo '<tr>';
			echo '<td>' . formatDate($meal->date_meal, 'short') . ' ' . formatTime($meal->date_meal) . '</td>';
			
			echo '<td>';
			echo '<a href="' . $link . '">' . htmlspecialchars($meal->name) . '</a>' . $menu;
			echo '</td>';
			
			echo '<td>' . htmlspecialchars($meal->location) . '</td>';
			
			$capacityClass = ($meal->totalDiners() > $meal->scr_capacity) ? 'text-danger' : '';
			echo '<td class="{$capacityClass}">' . $meal->totalDiners() . ' / ' . $meal->scr_capacity . '</td>';
			
			echo '</tr>';
		}
	
		echo '</tbody>';
		echo '</table>';
		echo '</div>';
	} else {
		echo '<p class="mt-3">No meals found for the selected date range.</p>';
	}
	?>
</div>

<div class="modal fade" id="menuModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content" id="modalContent">
	</div>
  </div>
</div>

<script>
// Load AJAX menu
remoteModalLoader('.load-remote-menu', '#menuModal', '#modalContent');
</script>

<style>
.load-remote-menu {
  float: right;
  margin-left: 0.5rem; /* spacing between text and icon */
}
</style>

<script>
const el = document.getElementById('dateFrom');
const el2 = document.getElementById('dateTo');

const options = {
	defaultDate: new Date('<?= date('c', strtotime($meal->date_meal)) ?>'),
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
			clock: false
		}
	},
	localization: {
		format: 'yyyy-MM-dd',
	  }
};

new tempusDominus.TempusDominus(el, options);
new tempusDominus.TempusDominus(el2, options);
</script>