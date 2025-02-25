<?php
$wineClass = new wineClass();

$allCellars= $wineClass->allCellars();
$allBins = $wineClass->allBins();
$allWines = $wineClass->allWines(null, true);

if (isset($_POST['filter_date'])) {
	
} else {
	$_POST['filter_date'] = date('Y-m-d');
}
foreach ($allWines AS $wine) {
	$wine = new wine($wine['uid']);
	$totalBottles = $totalBottles + $wine->currentQty($_POST['filter_date']);
	$totalPurchaseValue += $wine->stockValue($_POST['filter_date']);
}
?>

<h1>Wine Valuation Report <small class="text-body-secondary">Generated: <?php echo dateDisplay(date('c'), true); ?></small></h1>

<form method="post" id="bin_new" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="row">
	<div class="col">
		<div class="mb-3">
		  <label for="transaction_date_posted" class="form-label">'Up To' Date (transactions after this date will be excluded from report)</label>
		  <div class="input-group">
				<span class="input-group-text" id="filter_date-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
				<input type="date" class="form-control" name="filter_date" id="filter_date" value="<?php echo $_POST['filter_date']; ?>">
				<button class="btn btn-outline-secondary" type="submit">Submit</button>
			</div>
		</div>
	</div>
</div>
</form>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup"><?php echo count($allCellars); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Cellars</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup"><?php echo count($allBins); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bins</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup"><?php echo $totalBottles; ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup"><?php echo currencyDisplay($totalPurchaseValue); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Total Value</h6>
			</div>
		</div>
	</div>
</div>

<?php
foreach ($allCellars AS $cellar) {
	$cellar = new cellar($cellar['uid']);
	$cellarTotal = 0;
	$totalBottlesByCellar = 0;
	
	echo "<h2>" . $cellar->name . "</h2>";
	
	echo "<table class=\"table\">";
	echo "<thead>";
	echo "<tr>";
	echo "<th scope=\"col\" style=\"width: 40%;\">Category</th>";
	echo "<th scope=\"col\">Bottles</th>";
	echo "<th scope=\"col\">Total Value</th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	
	foreach (explode(",", $settingsClass->value('wine_category')) AS $wine_category) {
		$filter = array(
			"cellar_uid" => $cellar->uid,
			"wine_wines.category" => $wine_category
		);
		$wines = $wineClass->allWines($filter, true);
		
		$totalBottlesByCategory = 0;
		$totalPurchaseValue = 0;
		
		foreach ($wines AS $wine) {
			$wine = new wine($wine['uid']);
			
			$qty = $wine->currentQty($_POST['filter_date']);
			$totalBottlesByCategory += $qty;
			$totalPurchaseValue += $wine->stockValue($_POST['filter_date']);
			
			$totalBottlesByCellar += $qty;
		}
		
		
		echo "<tr>";
		echo "<td scope=\"row\">" . $wine_category . "</td>";
		echo "<td>" . $totalBottlesByCategory . "</td>";
		echo "<td>" . currencyDisplay($totalPurchaseValue) . "</td>";
		echo "</tr>";
		
		$cellarTotal = $cellarTotal + $totalPurchaseValue;
	}
	
	echo "<tr>";
	echo "<td></td>";
	echo "<td><strong>" . $totalBottlesByCellar . "</strong></td>";
	echo "<td><strong>" . currencyDisplay($cellarTotal) . "</strong></td>";
	echo "</tr>";
	
	echo "</tbody>";
	echo "</table>";
}
?>

<?php
$logArray['category'] = "report";
$logArray['result'] = "success";
$logArray['description'] = "[reportUID:" . $report['uid'] . "] run";
$logsClass->create($logArray);
?>
