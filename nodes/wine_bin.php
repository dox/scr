<?php
pageAccessCheck("wine");

$binUID = filter_var($_GET['bin_uid'], FILTER_SANITIZE_NUMBER_INT);

$wineClass = new wineClass();
$bin = new bin($binUID);
$cellar = new cellar($bin->cellar_uid);

if (isset($_GET['deleteBin'])) {
	if (count($bin->currentWines()) == 0 && count($bin->closedWines()) == 0) {
		$bin->delete();
		
		$url = "index.php?n=wine_cellar&uid=" . $cellar->uid;
		echo "<script type=\"text/javascript\">window.location.href = \"" . $url . "\";</script>";
	} else {
		echo "Cannot delete bin - there are wines in it (current and/or closed)";
	}
}

if (isset($_POST['editBin'])) {
	$bin->update($_POST);
	
	$bin = new bin($binUID);
	$cellar = new cellar($bin->cellar_uid);
}


$title = $bin->name . " Bin";
$subtitle = $bin->category;
if (!empty($bin->description)) {
	$subtitle .= " <i>(" . $bin->description . ")</i>";
}
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=add&cellar_uid=" . $cellar->uid . "&bin_uid=" . $bin->uid . "'\"");

if (count($bin->currentWines()) == 0 && count($bin->closedWines()) == 0) {
	$icons[] = array("class" => "text-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#journal-text\"/></svg> Delete Bin", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteBinModal\"");
}

$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#journal-text\"/></svg> Edit Bin", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#editBinModal\"");

if (count($bin->closedWines()) > 0) {
	$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#info-circle\"/></svg> Show closed wines (" . count($bin->closedWines()) . ")", "value" => "onclick=\"location.href='index.php?n=wine_bin&bin_uid=" . $bin->uid . "&closed=true'\"");
}

echo makeTitle($title, $subtitle, $icons, true);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $bin->name; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
	<?php
	if (isset($_GET['closed']) && $_GET['closed'] == "true") {
		$wines = $bin->closedWines();
	} else {
		$wines = $bin->currentWines();
	}
	foreach ($wines AS $wine) {
		$wine = new wine($wine['uid']);
		echo $wine->card();
	}
	?>
</div>

<div class="modal fade" id="deleteBinModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete Bin</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><span class="text-danger"><strong>WARNING!</strong> Are you sure you want to delete this bin?</p>
				<p>This will <strong>not</strong> delete any wines/transactions.<p>
				<p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<a href="index.php?n=wine_bin&bin_uid=<?php echo $bin->uid;?>&deleteBin" class="btn btn-primary">Delete Bin</a>
			</div>
			
		</div>
	</div>
</div>

<form method="post" id="bin_new" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="modal fade" id="editBinModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Bin</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label for="name" class="form-label">Cellar </label>
					<select class="form-select" id="cellar_uid" name="cellar_uid" required>
						<?php
						foreach ($wineClass->allCellars() AS $cellarOption) {
							if ($cellarOption['uid'] == $cellar->uid) {
								echo "<option value=\"" . $cellarOption['uid'] . "\" selected>" . $cellarOption['name'] . "</option>";
							} else {
								echo "<option value=\"" . $cellarOption['uid'] . "\">" . $cellarOption['name'] . "</option>";
							}
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="name" class="form-label">Bin Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?php echo $bin->name; ?>">
				</div>
				<div class="mb-3">
					<label for="category" class="form-label">Bin Category</label>
					<select class="form-select" id="category" name="category" required>
						<?php
						foreach ($cellar->binTypes() AS $binType) {
							if ($bin->category == $binType) {
								echo "<option value=\"" . $binType . "\" selected>" . $binType . "</option>";
							} else {
								echo "<option value=\"" . $binType . "\">" . $binType . "</option>";
							}
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Bin Description</label>
					<textarea class="form-control" id="description" name="description" rows="3"><?php echo $bin->description; ?></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editBin" name="editBin" />
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Edit Bin</button>
			</div>
			
		</div>
	</div>
</div>
</form>


<script>
document.addEventListener("DOMContentLoaded", function () {
	var cellarDropdown = document.getElementById("cellar_uid");
	var sectionDropdown = document.getElementById("category");
	var selectedCellar = cellarDropdown.value;
	var selectedSection = "<?php echo $bin->category ?>"; // Pre-selected section from PHP

	function fetchSections(cellarId, preselect = null) {
		sectionDropdown.innerHTML = '<option value="">Loading...</option>';

		if (cellarId) {
			fetch("actions/wine_cellarSections.php?c=" + cellarId)
				.then(response => response.json())
				.then(data => {
					sectionDropdown.innerHTML = '';
					data.forEach(section => {
						let option = document.createElement("option");
						option.value = section.name;
						option.textContent = section.name;
						if (preselect && section.name == preselect) {
							option.selected = true;
						}
						sectionDropdown.appendChild(option);
					});
				})
				.catch(error => {
					console.error("Error fetching sections:", error);
					sectionDropdown.innerHTML = '<option value="">Error loading sections</option>';
				});
		} else {
			//sectionDropdown.innerHTML = '<option value="">Select a Section</option>';
		}
	}

	// Load sections on page load if a cellar is pre-selected
	if (selectedCellar) {
		fetchSections(selectedCellar, selectedSection);
	}

	// Update sections dynamically when changing the cellar
	cellarDropdown.addEventListener("change", function () {
		fetchSections(this.value);
	});
});
</script>