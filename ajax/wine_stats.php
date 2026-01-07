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
		$url = "index.php?page=wine_filter
		&conditions[0][field]=wine_wines.category
		&conditions[0][operator]==
		&conditions[0][value]=" . htmlspecialchars($category) ."
		&conditions[1][field]=wine_wines.status
		&conditions[1][operator]=!=
		&conditions[1][value]=Closed";
		
		if ($cellarUID) {
			$cellar = new Cellar($cellarUID);
			
			$winesByCategory = $wines->wines([
				'cellar_uid' => ['=', $cellar->uid],
				'wine_wines.category' => ['=', $category],
				'wine_wines.status' => ['<>', 'Closed']
			]);
			
			$url .= "&conditions[3][field]=wine_bins.cellar_uid
			&conditions[3][operator]==
			&conditions[3][value]=" . $cellarUID;
		} else {
			$winesByCategory = $wines->wines([
				'wine_wines.category' => ['=', $category],
				'wine_wines.status' => ['<>', 'Closed']
			]);
		}
		
		// get the total number of bottles for each wine
		$total = 0;
		foreach ($winesByCategory as $wine) {
			$total += $wine->currentQty();
		}
		?>
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<?php
					
					?>
					<div class="subheader text-nowrap text-truncate"><a href="<?= $url ?>"><?= htmlspecialchars($category) ?></a></div>
					<div class="h1 text-truncate"><?= number_format($total) ?></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>