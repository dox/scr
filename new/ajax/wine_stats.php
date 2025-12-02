<?php
require_once '../inc/autoload.php';

if (!$user->hasPermission("wine")) {
	die("Permission denied.");
}

$cellarUID = filter_input(INPUT_GET, 'cellar_uid', FILTER_SANITIZE_NUMBER_INT);
$wines = new Wines();

if ($cellarUID) {
	$cellar = new Cellar($cellarUID);
	$totalBottoles = $cellar->bottlesCount();
} else {
	$totalBottoles = $wines->wineBottlesTotal();
}
?>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="subheader text-nowrap text-truncate">Bottles</div>
				<div class="h1 text-truncate"><?= number_format($totalBottoles) ?></div>
			</div>
		</div>
	</div>
	
	<?php
	$categories = array_slice(explode(",", $settings->get('wine_category')), 0, 5, true);
	
	foreach ($categories as $category): 
		if ($cellarUID) {
			$cellar = new Cellar($cellarUID);
			
			$winesByCategory = $wines->wines([
				'cellar_uid' => ['=', $cellar->uid],
				'wine_wines.category' => ['=', $category],
				'wine_wines.status' => ['<>', 'Closed']
			]);
		} else {
			$winesByCategory = $wines->wines([
				'wine_wines.category' => ['=', $category]
			]);
		}
		?>
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate"><?= htmlspecialchars($category) ?></div>
					<div class="h1 text-truncate"><?= number_format(count($winesByCategory)) ?></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>