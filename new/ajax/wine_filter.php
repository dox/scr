<?php
require_once '../inc/autoload.php';

if (!$user->hasPermission("wine")) {
	die("Permission denied.");
}

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
?>

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
