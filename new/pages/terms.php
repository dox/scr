<?php
$user->pageCheck('terms');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['deleteTermUID'])) {
		$deleteTermUID = filter_input(INPUT_POST, 'deleteTermUID', FILTER_SANITIZE_NUMBER_INT);
		
		$term = new Term($deleteTermUID);
		$term->delete();
	} else {
		$terms->create($_POST);
	}
}

echo pageTitle(
	"Terms",
	"Term dates from <a href=\"https://www.ox.ac.uk/about/facts-and-figures/dates-of-term\">Oxford's website</a>",
	[
		[
			'permission' => 'everyone',
			'title' => 'Add new',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addTermModal'
			]
		]
	]
);
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Term</th>
			<th>Start</th>
			<th>End</th>
			<th>Meals</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$currentTerm = $terms->currentTerm();
		$meals = new Meals();
		
		foreach ($terms->all() as $term) {
			$mealsInTerm = $term->mealsInTerm();
			$active = ($currentTerm->uid == $term->uid) ? ' class="table-success"' : '';
			$url = "index.php?page=term&uid=" . $term->uid;
			
			if ($currentTerm->uid == 0) {
				$nextTerm = $terms->previousTerm();
				if ($term->date_start == $nextTerm->date_start) {
					echo "<tr>";
					echo "<td colspan=\"4\" class=\"table-success text-center\">Vacation</td>";
					echo "</tr>";
				}
			}
			
			echo "<tr{$active}>";
			echo "<td><a href=\"{$url}\">{$term->name}</a></td>";
			echo "<td>" . formatDate($term->date_start) . "</td>";
			echo "<td>" . formatDate($term->date_end) . "</td>";
			echo "<td>" . count($mealsInTerm) . "</td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table>

<!-- Add Term Modal -->
<div class="modal fade" tabindex="-1" id="addTermModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add New Term</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label for="name">Term Name</label>
					<input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp">
					<small id="nameHelp" class="form-text text-muted">e.g. 'Trinity <?php echo date('Y')+1; ?></small>
				</div>
				
				<div class="mb-3">
					<label for="date_start" class="form-label">Term Start Date</label>
					<div class="input-group">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_start" id="date_start" placeholder="" value="<?php echo ""; ?>" required>
					</div>
					<small id="date_startHelp" class="form-text text-muted">Sunday of 1st week</small>
				</div>
				
				<div class="mb-3">
					<label for="date_end" class="form-label">Term End Date</label>
					<div class="input-group">
						<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
						<input type="text" class="form-control" name="date_end" id="date_end" placeholder="" value="<?php echo ""; ?>" required>
					</div>
					<small id="date_endHelp" class="form-text text-muted">Saturday of 8th week</small>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Add Term</button>
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