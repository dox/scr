<?php
// Get date end from POST
$end = filter_input(INPUT_POST, 'date_to', FILTER_DEFAULT);

// If not provided or invalid, default to today
if (!$end || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
	$end = date('Y-m-d'); // defaults to today
}

echo pageTitle(
	'Wine Valuation',
	'Summary of wine valuation up to ' . formatDate($end) . '.'
);

$wines = new Wines();

// count bottles and value
$totalBottles = 0;
$totalValue = 0;
$winesTotal = 0;

$winesToDate = $wines->wines();

foreach ($winesToDate as $wine) {
	$qty = $wine->currentQty($end);
	
	if ($qty > 0) {
		$winesTotal ++;
		$totalBottles += $qty;
		$totalValue += ($qty * $wine->price_purchase);
	}
}
?>

<form method="post" id="mealsBetweenDates" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="row align-items-end">
		<div class="col">
			<label for="dateTo" class="form-label">Date To</label>
			<input type="text" class="form-control" id="dateTo" name="date_to" value="<?= $end ?>" required="">
		</div>
		<div class="col">
			<button type="submit" class="btn btn-primary mt-3 w-100">Submit</button>
		</div>
	</div>
</form>

<hr>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="subheader text-nowrap text-truncate">Cellars</div>
				<div class="h1 text-truncate"><?= number_format(count($wines->cellars())) ?></div>
			</div>
		</div>
	</div>
	
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="subheader text-nowrap text-truncate">Bins</div>
				<div class="h1 text-truncate"><?= number_format(count($wines->cellars())) ?></div>
			</div>
		</div>
	</div>
	
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="subheader text-nowrap text-truncate">Wines</div>
				<div class="h1 text-truncate"><?= number_format($winesTotal) ?></div>
			</div>
		</div>
	</div>
	
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="subheader text-nowrap text-truncate">Bottles</div>
				<div class="h1 text-truncate"><?= number_format($totalBottles) ?></div>
			</div>
		</div>
	</div>
	
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="subheader text-nowrap text-truncate">Total Value</div>
				<div class="h1 text-truncate"><?= formatMoney($totalValue) ?></div>
			</div>
		</div>
	</div>
</div>

<?php
foreach ($wines->cellars() as $cellar) {
	echo "<h2>" . $cellar->name . "</h2>";
	
	echo '<table class="table"><thead><tr><th scope="col" style="width: 40%;">Section</th><th scope="col">Wines</th><th scope="col">Bottles</th><th scope="col">Total Value</th></tr></thead><tbody>';
	
	foreach ($cellar->sections() as $section) {
		$sectionWines = $wines->wines([
			'wine_bins.cellar_uid' => ['=', $cellar->uid],
			'wine_bins.section'   => ['=', $section]
		]);
		
		// count bottles and value
		$sectionTotalBottles = 0;
		$sectionTotalValue = 0;
		$sectionWinesTotal = 0;
		foreach ($sectionWines as $wine) {
			$qty = $wine->currentQty($end);
			
			if ($qty > 0) {
				$sectionWinesTotal ++;
				$sectionTotalBottles += $qty;
				$sectionTotalValue += ($qty * $wine->price_purchase);
			}
		}
		
		$output  = "<tr>";
		$output .= "<td scope=\"row\">" . $section . "</td>";
		$output .= "<td>" . number_format($sectionWinesTotal) . "</td>";
		$output .= "<td>" . number_format($sectionTotalBottles) . "</td>";
		$output .= "<td>" . formatMoney($sectionTotalValue) . "</td>";
		$output .= "</tr>";
		
		echo $output;
	}
	echo '</tbody></table>';
}
?>

<style>
.load-remote-menu {
	float: right;
	margin-left: 0.5rem;
	/* spacing between text and icon */
}
</style>

<script>
const el1 = document.getElementById('dateTo');

const options = {
	defaultDate: new Date('<?= date('c') ?>'),
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

new tempusDominus.TempusDominus(el1, options);
</script>