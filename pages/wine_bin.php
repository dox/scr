<?php
$user->pageCheck('wine');

$wines = new Wines();
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
$bin = new Bin($cleanUID);
$cellar = new Cellar($bin->cellar_uid);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['uid'])) {
		$bin->update($_POST);
		$bin = new Bin($cleanUID);
	}
}

$icons = [
	[
		'permission' => 'wine',
		'title' => 'Edit Bin',
		'class' => '',
		'event' => '',
		'icon' => 'pencil',
		'data' => [
			'bs-toggle' => 'modal',
			'bs-target' => '#editBinModal'
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
		'title' => 'Add Wine',
		'class' => '',
		'event' => 'index.php?page=wine_wine_edit&cellar_uid=' . $cellar->uid . '&bin_uid=' . $bin->uid,
		'icon' => 'plus-circle'
	],
	
];

if (count($bin->wines()) == 0) {
	$icons[] = [
		'permission' => 'wine',
		'title' => 'Delete Bin',
		'class' => 'text-danger',
		'event' => '',
		'icon' => 'trash3',
		'data' => [
			'bs-toggle' => 'modal',
			'bs-target' => '#deleteBinModal'
		]
	];
}
echo pageTitle(
	$bin->name,
	$bin->section,
	$icons
);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?page=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $bin->name; ?></li>
	</ol>
</nav>

<hr/>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 mb-3">
	<?php
	$closedWines = [];
	foreach ($bin->wines() as $wine) {
		if ($wine->status != "Closed") {
			echo "<div class=\"col\">";
			echo $wine->card();
			echo "</div>";
		} else {
			$closedWines[] = $wine;
		}
	}
	?>
</div>

<?php
if (!empty($closedWines)) {
	echo "<hr>";
	echo "<h4>Closed Wines</h4>";
	echo "<div class=\"row row-cols-1 row-cols-sm-2 row-cols-md-3\">";
		foreach ($closedWines as $wine) {
			echo "<div class=\"col\">";
			echo $wine->card();
			echo "</div>";
		}
	echo "</div>";
}
?>

<!-- Edit Bin Modal -->
<div class="modal fade" tabindex="-1" id="editBinModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Bin</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label for="cellar_uid" class="form-label">Cellar</label>
					<select class="form-select" name="cellar_uid" id="cellar_uid" required>
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
					<input type="text" class="form-control" id="name" name="name" value="<?= $bin->name ?>">
				</div>
				<div class="mb-3">
					<label for="section" class="form-label">Bin Section</label>
					<select class="form-select" name="section" id="section" required>
						<?php
						foreach ($cellar->sections() as $section) {
							$title = trim($section);
							$selected = ($title === $bin->section) ? ' selected' : '';
							echo "<option value=\"{$title}\"{$selected}>{$title}</option>";
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Bin Description</label>
					<textarea class="form-control" id="description" name="description" rows="3"><?= $bin->description ?></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Update Bin</button>
				<input type="hidden" name="uid" value="<?= $bin->uid ?>">
			</div>
		</div>
	</div>
	</form>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteBinModal" tabindex="-1" aria-hidden="true">
	<form method="post" action="index.php?page=wine_cellar&uid=<?= $cellar->uid ?>">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Delete Bin <span class="text-danger"><strong>WARNING</strong></span></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<p>This will <strong>not</strong> delete any wine(s).</p>
					<p><strong class="text-danger">This action cannot be undone.</strong></p>
					<input type="text" class="form-control mb-3"
						placeholder="Type 'DELETE' to confirm"
						id="delete_confirm"
						oninput="enableOnExactMatch('delete_confirm', 'delete_button', 'DELETE')">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger" id="delete_button" disabled>Delete Bin</button>
					<input type="hidden" name="deleteBinUID" value="<?= $bin->uid; ?>">
				</div>
			</div>
		</div>
	</form>
</div>
