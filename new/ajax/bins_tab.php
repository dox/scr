<?php
require_once '../inc/autoload.php';
$user->pageCheck('wine');

$wines = new Wines();

$cellar_uid = filter_var($_GET['cellar_uid'], FILTER_SANITIZE_NUMBER_INT);
$cellar_section = filter_var($_GET['section'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cellar = new Cellar($cellar_uid);

$bins = $cellar->bins(['section' => $cellar_section]);
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
		<?php foreach ($bins as $bin):
			
			$wines = $bin->wines(['wine_wines.status' => ['!=', 'Closed']]);
			
			if (count($wines) == 1) {
				$url = "index.php?page=wine_wine&uid=" . $wines[0]->uid;
				$wineName = htmlspecialchars($wines[0]->name);
				$wineVintage = htmlspecialchars($wines[0]->vintage());
			} else {
				$url = "index.php?page=wine_bin&uid=" . $bin->uid;
				
				$wineNames = [];
				$wineVintage = [];
				foreach ($wines as $wine) {
					$wineNames[] = htmlspecialchars($wine->name);
					$wineVintage[] = htmlspecialchars($wine->vintage());
				}
				
				$wineName = implode(", ", array_unique($wineNames));
				if (count($wineNames) > 1) {
					$wineName = "<span class=\"badge rounded-pill text-bg-warning me-2\">Multiple</span>" . $wineName;
				}
				$wineVintage = implode(", ", array_unique($wineVintage));
			}
		?>
		<tr>
			<th scope="row">
				<a href="<?= $url ?>"><?= htmlspecialchars($bin->name); ?></a>
			</th>
			<td><?= $wineName ?></td>
			<td><?= $wineVintage ?></td>
			<td><?= htmlspecialchars($bin->uid) ?></td>
		</tr>
		<?php endforeach; ?>
		
	</tbody>
</table>