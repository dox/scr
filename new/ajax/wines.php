<?php
require_once '../inc/autoload.php';
$user->pageCheck('wine');

$wines = new Wines();

$cellar_uid = filter_var($_GET['cellar_uid'], FILTER_SANITIZE_NUMBER_INT);
$bin_type = filter_var($_GET['bin_type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);


$winesByType = $wines->wines([
	'cellar_uid' => ['=', $cellar_uid],
	'wine_bins.category' => ['=', $bin_type],
	'wine_wines.status' => ['<>', 'Closed']
]);

?>


<table class="table table-striped">
	<thead>
		<tr>
			<th style="width: 10%;" scope="col">Bin</th>
			<th style="width: 70%;" scope="col">Wine</th>
			<th style="width: 10%;" scope="col">Vintage</th>
			<th style="width: 10%;" scope="col">Bottles</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($winesByType as $wine): ?>
		<tr>
			<th scope="row">
				<a href="index.php?page=wine_wine&uid=<?= htmlspecialchars($wine->uid) ?>"><?= htmlspecialchars($wine->binName()); ?></a>
			</th>
			<td><?= htmlspecialchars($wine->clean_name()) ?></td>
			<td><?= htmlspecialchars($wine->vintage()) ?></td>
			<td><?= htmlspecialchars($wine->currentQty()) ?></td>
		</tr>
		<?php endforeach; ?>
		
	</tbody>
</table>