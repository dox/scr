<?php
$user->pageCheck('wine');

$wines = new Wines();
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
$cellar = new cellar($cleanUID);
$meals = new Meals();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['uid'])) {
		$cellar->update($_POST);
		$cellar = new Cellar($cleanUID);
	} elseif (isset($_POST['deleteBinUID'])) {
		$bin = new Bin($_POST['deleteBinUID']);
		$bin->delete();
	} else {
		$bin = new Bin();
		$bin->create($_POST);
	}
}

echo pageTitle(
	$cellar->name . " Wine Cellar",
	htmlspecialchars($cellar->notes),
	[
		[
			'permission' => 'wine',
			'title' => 'Edit Cellar',
			'class' => '',
			'event' => '',
			'icon' => 'pencil',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#editCellarModal'
			]
		],
		[
			'permission' => 'wine',
			'title' => 'Favourites/Lists',
			'class' => '',
			'event' => 'index.php?page=wine_lists',
			'icon' => 'heart'
		],
		[
			'permission' => 'wine',
			'title' => 'Add Bin',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addBinModal'
			]
		],
		[
			'permission' => 'wine',
			'title' => 'Add Wine',
			'class' => '',
			'event' => 'index.php?page=wine_wine_edit&cellar_uid=' . $cellar->uid,
			'icon' => 'plus-circle'
		],
		[
			'permission' => 'wine',
			'title' => 'Add Transaction',
			'class' => '',
			'event' => 'index.php?page=wine_transaction_add',
			'icon' => 'receipt'
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

<div class="wine-search-wrapper position-relative">
  <div class="input-group mb-3">
	<input 
	  type="text"
	  class="form-control form-control-lg"
	  id="wine_search"
	  placeholder="Quick search <?= $cellar->name; ?>"
	  autocomplete="off"
	  spellcheck="false"
	>
	<a href="index.php?page=wine_filter" type="button" class="btn btn-lg btn-outline-secondary">
	  Advanced Filter
	</a>
  </div>

  <ul id="wine_search_results" class="list-group"></ul>
</div>

<a href="#wine_stats_container"
   class="wine_stats_link d-none"
   data-url="./ajax/wine_stats.php?cellar_uid=<?= $cellar->uid ?>"
   data-selected="true">
</a>
<div id="wine_stats_container">
	<div class="spinner-border" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
</div>

<?php
foreach ($cellar->sections() as $cellarSection) {
	$winesByBin[$cellarSection] = $wines->wines([
		'wine_bins.cellar_uid' => ['=', $cellar->uid],
		'wine_bins.section' => ['=', $cellarSection],
		'wine_wines.status' => ['<>', 'Closed']
	]);
}
$isCurrent = true;
?>

<ul class="nav nav-tabs nav-fill mb-3" id="binsTabs" role="tablist">
<?php foreach ($cellar->sections() as $cellarSection): ?>
	<?php
		// Prefixed IDs
		$paneId = 'section-' . htmlspecialchars($cellarSection);
		$tabId  = 'section-tab-' . htmlspecialchars($cellarSection);
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
		   data-url="./ajax/bins_tab.php?cellar_uid=<?= urlencode($cellar->uid) ?>&section=<?= urlencode($cellarSection) ?>">
		   <?= $cellarSection . " (" . count($winesByBin[$cellarSection]) . ")" ?>
		</a>
	</li>
<?php 
$isCurrent = false;
endforeach; 
$isCurrent = true;
?>
</ul>

<div class="tab-content" id="binsContent">
<?php foreach ($cellar->sections() as $cellarSection): ?>
	<?php
		$paneId = 'section-' . htmlspecialchars($cellarSection);
		$tabId  = 'section-tab-' . htmlspecialchars($cellarSection);
		$isCurrent = true;
	?>
	<div class="tab-pane fade <?= $isCurrent ? 'show active' : '' ?> "
		id="<?= $paneId ?>"
		role="tabpanel"
		aria-labelledby="<?= $tabId ?>">
		
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



<!-- Add Bin Modal -->
<div class="modal fade" tabindex="-1" id="addBinModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add Bin</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label for="cellar_uid" class="form-label">Cellar</label>
					<select class="form-select" name="cellar_uid" required>
						<?php
						foreach ($wines->cellars() as $cellarChoice) {
							$title = trim($cellarChoice->name);
							$selected = ($cellarChoice->uid === $cellar->uid) ? ' selected' : '';
							echo "<option value=\"{$cellarChoice->uid}\"{$selected}>{$title}</option>";
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="name" class="form-label">Bin Name</label>
					<input type="text" class="form-control" name="name">
				</div>
				<div class="mb-3">
					<label for="section" class="form-label">Bin Section</label>
					<select class="form-select" name="section" required>
						<?php
						foreach ($cellar->sections() as $section) {
							$title = trim($section);
							echo "<option value=\"{$title}\">{$title}</option>";
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Bin Description</label>
					<textarea class="form-control" name="description" rows="3"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Add Bin</button>
			</div>
		</div>
	</div>
	</form>
</div>

<!-- Edit Cellar Modal -->
<div class="modal fade" tabindex="-1" id="editCellarModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Cellar</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-3 mb-3">
						<label for="short_code" class="form-label text-truncate">Short Code</label>
						<input type="text" class="form-control" name="short_code" value="<?= htmlspecialchars($cellar->short_code) ?>" maxlength="2">
					</div>
					<div class="col-9 mb-3">
						<label for="name" class="form-label text-truncate">Cellar Name</label>
						<input type="text" class="form-control" name="name" value="<?= htmlspecialchars($cellar->name) ?>">
					</div>
				</div>
				<div class="mb-3">
					<div class="accordion">
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
										data-bs-target="#photoSelect">Photograph</button>
							</h2>
							<div class="accordion-collapse collapse">
								<div class="accordion-body">
									<div class="row">
										<?php foreach ($meals->cardImages() as $cardImage): ?>
											<div class="col-6">
												<div class="card mb-3">
													<img src="<?= htmlspecialchars($cardImage) ?>" class="card-img-top">
													<div class="card-body">
														<label class="text-truncate">
															<input type="radio" name="photograph"
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
					<label for="description" class="form-label">Sections</label>
					<textarea class="form-control" name="sections" rows="3"><?= htmlspecialchars($cellar->sections) ?></textarea>
					<div id="bin-typesHelp" class="form-text">Comma,Separated,List</div>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Notes</label>
					<textarea class="form-control" name="notes" rows="3"><?= htmlspecialchars($cellar->notes) ?></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Edit Cellar</button>
				<input type="hidden" name="uid" value="<?= $cellar->uid ?>">
			</div>
		</div>
	</div>
	</form>
</div>

<script>
// Initialize bin tabs
document.addEventListener('DOMContentLoaded', () => {
	initAjaxLoader('#binsTabs .nav-link', '#binsContent');
});

// Initialize stats links
initAjaxLoader('.wine_stats_link', '#wine_stats_container');

// Handle wine live searching
liveSearch(
	'wine_search',
	'wine_search_results',
	'./ajax/wine_livesearch.php',
	{ cellar_uid: <?= $cellar->uid ?> }
);
</script>