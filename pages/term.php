<?php
$user->pageCheck('terms');

$termUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$term = new Term($termUID);
$mealsInTerm = $term->mealsInTerm();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$term->update($_POST);
	$term = new Term($termUID);
}

echo pageTitle(
	"Term: " . $term->name,
	"Term dates from <a href=\"https://www.ox.ac.uk/about/facts-and-figures/dates-of-term\">Oxford's website</a>",
	[
		[
			'permission' => 'everyone',
			'title' => 'Delete',
			'class' => '',
			'event' => '',
			'icon' => 'trash3',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteTermModal'
			]
		]
	]
);
?>

<div class="row">
	<div class="col-md-7 col-lg-8">
		<h4>Term Information</h4>
		
		<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
			<div class="modal-body">
				<div class="mb-3">
					<label for="name">Term Name</label>
					<input type="text" class="form-control" name="name" id="name" value="<?= $term->name; ?>" aria-describedby="nameHelp">
					<small id="nameHelp" class="form-text text-muted">e.g. 'Trinity <?php echo date('Y')+1; ?></small>
				</div>
				
				<div class="mb-3">
					<label for="date_start" class="form-label">Term Start Date</label>
					<div class="input-group" id="datetimepicker">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_start" id="date_start" placeholder="" value="<?= $term->date_start; ?>"" required>
					</div>
					<small id="date_startHelp" class="form-text text-muted">Sunday of 1st week</small>
				</div>
				
				<div class="mb-3">
					<label for="date_end" class="form-label">Term End Date</label>
					<div class="input-group" id="datetimepicker">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_end" id="date_end" placeholder="" value="<?= $term->date_end; ?>" required>
					</div>
					<small id="date_endHelp" class="form-text text-muted">Saturday of 8th week</small>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary w-100">Update Term</button>
		</form>
	</div>
	<div class="col-md-5 col-lg-4">
		<h4 class="d-flex justify-content-between align-items-center mb-3">
		  <span>Meals</span>
		  <span class="badge bg-secondary rounded-pill"><?php echo count($mealsInTerm); ?></span>
		</h4>
		<ul class="list-group mb-3">
			<?php
			foreach ($mealsInTerm as $meal) {
				echo $meal->displayListGroupItem();
			}
			?>
		</ul>
	</div>
</div>




<!-- Delete Term Modal -->
<div class="modal fade" tabindex="-1" id="deleteTermModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="index.php?page=terms">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Term</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><span class="text-danger"><strong>WARNING!</strong></span> Are you sure you want to delete this term?</p>
				<p>This will not delete any meals/bookings.</p>
				<p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger">Delete Term</button>
				<input type="hidden" name="deleteTermUID" value="<?= $term->uid; ?>">
			</div>
		</div>
	</div>
	</form>
</div>

<script>
const el = document.getElementById('date_start');
const el2 = document.getElementById('date_end');

const sundayOptions = {
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
			decades: false,
			clock: false
		}
	},
	localization: {
		format: 'yyyy-MM-dd',
	},
	restrictions: {
		daysOfWeekDisabled: [1,2,3,4,5,6] // leave Sunday (0)
	}
};

const saturdayOptions = {
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
			decades: false,
			clock: false
		}
	},
	localization: {
		format: 'yyyy-MM-dd',
	},
	restrictions: {
		daysOfWeekDisabled: [0,1,2,3,4,5] // leave Saturday (6)
	}
};

new tempusDominus.TempusDominus(el, sundayOptions);
new tempusDominus.TempusDominus(el2, saturdayOptions);
</script>