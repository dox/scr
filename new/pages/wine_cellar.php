<?php
$user->pageCheck('wine');

$wines = new Wines();
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
$cellar = new cellar($cleanUID);
$meals = new Meals();

echo pageTitle(
	$cellar->name . " Wine Cellar",
	count($cellar->bins()) . autoPluralise(" bin", " bins", count($cellar->bins())),
	[
		[
			'permission' => 'wine',
			'title' => 'Edit Cellar',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#editCellarModal'
			]
		],
		[
			'permission' => 'wine',
			'title' => 'Add Bin',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteTermModal'
			]
		],
		[
			'permission' => 'wine',
			'title' => 'Add Wine',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteTermModal'
			]
		],
		[
			'permission' => 'wine',
			'title' => 'Add Multi-Transaction',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteTermModal'
			]
		]
	]
);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?php echo $cellar->name; ?></li>
	</ol>
</nav>

<hr/>

<div class="row pb-3">
	<div class="col">
		<div class="input-group mb-3">
		  <input type="text" class="form-control form-control-lg" id="wine_search" placeholder="Quick search <?php echo $cellar->name; ?> wine cellar" autocomplete="off" spellcheck="false">
		  <a href="index.php?page=wine_filter" type="button" class="btn btn-lg btn-outline-secondary">Advanced Filter</a>
		</div>
		<ul id="wine_search_results" class="list-group"></ul>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup"><?php echo $cellar->bottlesCount(); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<?php
	$categories = array_slice(explode(",", $settings->get('wine_category')), 0, 5, true);
	
	foreach ($categories as $wine_category) {
		$winesByCategory = $wines->wines([
			'cellar_uid' => ['=', $cellar->uid],
			'wine_bins.category' => ['=', $wine_category],
			'wine_wines.status' => ['<>', 'Closed']
		]);
		
		if (count($winesByCategory) > 0) {
			$wineBottlesCount = 0;
			foreach($winesByCategory as $wine) {
				$wineBottlesCount = $wineBottlesCount + $wine->currentQty();
			}
			$url = "index.php?n=wine_search&filter=category&value=" . $wine_category . "&cellar_uid=" . $cellar->uid;
			
			$output  = "<div class=\"col\">";
			$output .= "<div class=\"card mb-3\">";
			$output .= "<div class=\"card-body\">";
			$output .= "<h5 class=\"card-title\">" . $wineBottlesCount . "</h5>";
			$output .= "<h6 class=\"card-subtitle mb-2 text-truncate text-body-secondary\"><a href=\"" . $url . "\">" . $wine_category . "</a></h6>";
			$output .= "</div>";
			$output .= "</div>";
			$output .= "</div>";
			
			echo $output;
		}
		
	}
	?>
</div>

chart

<?php
$binTypes = explode(',', $cellar->bin_types);
foreach ($binTypes as $binType) {
	$winesByBin[$binType] = $wines->wines([
		'wine_bins.cellar_uid' => ['=', $cellar->uid],
		'wine_bins.category' => ['=', $binType],
		'wine_wines.status' => ['<>', 'Closed']
	]);
}
$isCurrent = true;
?>

<ul class="nav nav-tabs nav-fill mb-3" id="binsTabs" role="tablist">
<?php foreach ($binTypes as $binType): ?>
	<?php
		// Prefixed IDs
		$paneId = 'week-' . htmlspecialchars($binType);
		$tabId  = 'week-tab-' . htmlspecialchars($binType);
	?>
	<li class="nav-item" role="presentation">
		<a class="nav-link <?= $isCurrent ? 'active' : '' ?>"
		   id="<?= $tabId ?>"
		   data-bs-toggle="tab"
		   href="#<?= $paneId ?>"
		   role="tab"
		   aria-controls="<?= $paneId ?>"
		   aria-selected="<?= $isCurrent ? 'true' : 'false' ?>"
		   data-selected="<?= $isCurrent ? 'true' : 'false' ?>"
		   data-url="./ajax/wines.php?cellar_uid=<?= urlencode($cellar->uid) ?>&bin_type=<?= urlencode($binType) ?>">
		   <?= $binType . " (" . count($winesByBin[$binType]) . ")" ?>
		</a>
	</li>
<?php 
$isCurrent = false;
endforeach; 
$isCurrent = true;
?>
</ul>

<div class="tab-content" id="binsContent">
<?php foreach ($binTypes as $binType): ?>
	<?php
		$paneId = 'week-' . htmlspecialchars($binType);
		$tabId  = 'week-tab-' . htmlspecialchars($binType);
		$isCurrent = true;
	?>
	<div class="tab-pane fade <?= $isCurrent ? 'show active' : '' ?> "
		id="<?= $paneId ?>"
		role="tabpanel"
		aria-labelledby="<?= $tabId ?>"
		data-url="./ajax/wines.php?cellar_uid=<?= urlencode($cellar->uid) ?>&bin_type=<?= urlencode($binType) ?>">
		
		<div class="d-flex justify-content-center">
		  <div class="spinner-border" role="status">
			<span class="visually-hidden">Loading...</span>
		  </div>
		</div>
	</div>
<?php
$isCurrent = false;
endforeach; ?>
</div>



<!-- Edit Cellar Modal -->
<div class="modal fade" tabindex="-1" id="editCellarModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="index.php?page=terms">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Cellar</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
				<div class="col-3">
					<div class="mb-3">
						<div class="mb-3">
							<label for="short_code" class="form-label">Short Code</label>
							<input type="text" class="form-control" id="short_code" name="short_code" value="<?= htmlspecialchars($cellar->short_code) ?>" maxlength="2">
						</div>
					</div>
				</div>
				<div class="col-9">
					<div class="mb-3">
						<label for="name" class="form-label">Cellar Name</label>
						<input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($cellar->name) ?>">
					</div>
				</div>
				</div>
				<div class="mb-3">
					<div class="accordion" id="accordionPhotograph">
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
										data-bs-target="#photoSelect">Photograph</button>
							</h2>
							<div id="photoSelect" class="accordion-collapse collapse">
								<div class="accordion-body">
									<div class="row">
										<?php foreach ($meals->cardImages() as $cardImage): ?>
											<div class="col-6 col-md-4">
												<div class="card mb-3">
													<img src="<?= htmlspecialchars($cardImage) ?>" class="card-img-top">
													<div class="card-body">
														<label>
															<input type="radio" name="photo"
																value="<?= basename($cardImage) ?>"
																<?= ($cellar->photographURL() === $cardImage) ? 'checked' : '' ?>>
															<?= basename($cardImage) ?>
														</label>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Bin Types</label>
					<textarea class="form-control" id="bin_types" name="bin_types" rows="3"><?= htmlspecialchars($cellar->bin_types) ?></textarea>
					<div id="bin-typesHelp" class="form-text">Comma,Separated,List</div>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Notes</label>
					<textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($cellar->notes) ?></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Edit Cellar</button>
				<input type="hidden" name="cellarUID" value="<?= $cellar->uid ?>">
			</div>
		</div>
	</div>
	</form>
</div>





<script>
// Initialize week tabs
document.addEventListener('DOMContentLoaded', () => {
	initAjaxLoader('#binsTabs .nav-link', '#binsContent');
});
</script>

