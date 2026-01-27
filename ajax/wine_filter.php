<?php
require_once '../inc/autoload.php';
$user->pageCheck('wine');

$conditions = isset($_POST['conditions']) ? $_POST['conditions'] : '';

if ($conditions === '') {
	die("No conditions given");
}

$wines = new Wines();

$conditionArray = [];
foreach ($conditions as $condition) {
	$field    = $condition['field'];
	$operator = $condition['operator'];
	$value    = $condition['value'];

	$conditionArray[$field] = [$operator, $value];
}

$wineResults = $wines->wines($conditionArray);

$total = 0;
foreach ($wineResults as $wine) {
	$total += $wine->currentQty();
}
?>
<?php
$wineCount   = number_format(count($wineResults));
$bottleCount = number_format($total);
?>

<h2>
	<?= $wineCount . autoPluralise(' wine ', ' wines ', $wineCount) ?>
	<small class="text-muted">
		<?= $bottleCount . autoPluralise(' bottle', ' bottles', $bottleCount) ?>
	</small>
</h2>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 mb-3">
	<?php
	$closedWines = [];
	foreach ($wineResults as $wine) {
		echo "<div class=\"col\">";
		echo $wine->card();
		echo "</div>";
	}
	?>
</div>
