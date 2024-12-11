<?php
$wineClass = new wineClass();

$allWines = $wineClass->allWines(null, true);
$allCellars= $wineClass->allCellars();
$allBins = $wineClass->allBins();
$allWines = $wineClass->allWines(null, true);

foreach ($allWines AS $wine) {
	$totalBottles = $totalBottles + $wine['qty'];
	$totalPurchaseValue = $totalPurchaseValue + ($wine['qty'] * $wine['price_purchase']);
}
?>

<h1>Wine Valuation Report <small class="text-body-secondary">Generated: <?php echo dateDisplay(date('c'), true); ?></small></h1>

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
	echo "<h2>" . $cellar->name . "</h2>";

	foreach (explode(",", $settingsClass->value('wine_category')) AS $wine_category) {
		echo "<ul>";
		$filter = array(
			"cellar_uid" => $cellar->uid,
			"wine_wines.category" => $wine_category
		);
		$wines = $wineClass->allWines($filter, true);
		
		$totalBottles = 0;
		$totalPurchaseValue = 0;
		
		foreach ($wines AS $wine) {
			$totalBottles = $totalBottles + $wine['qty'];
			$totalPurchaseValue = $totalPurchaseValue + ($wine['qty'] * $wine['price_purchase']);
		}
		echo "<li>" . $wine_category;
			echo "<ul>";
			echo "<li>Bottles: " . $totalBottles . "</li>";
			echo "<li>Total Value: " . currencyDisplay($totalPurchaseValue) . "</li>";
			echo "</ul>";
		echo "</li>";
		echo "</ul>";
	}
}
?>

<?php
$logArray['category'] = "report";
$logArray['result'] = "success";
$logArray['description'] = "[reportUID:" . $report['uid'] . "] run for [memberUID:" . $memberObject->uid . "]";
//$logsClass->create($logArray);
?>