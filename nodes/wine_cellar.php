<?php
pageAccessCheck("wine");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

// check if we need to create a new bin
if (isset($_POST['addBin'])) {
	$newBin = new bin();
	$newBin->cellar_uid = $_POST['cellar_uid'];
	$newBin->name = $_POST['name'];
	$newBin->category = $_POST['category'];
	$newBin->description = $_POST['description'];
	
	$newBin->create();
}

// check if we need to update this cellar
if (isset($_POST['editCellar'])) {
	$cellar = new cellar($cleanUID);
	
	$cellar->update($_POST);
}


$wineClass = new wineClass();
$cellar = new cellar($cleanUID);

$title = $cellar->name . " Wine Cellar";
$subtitle = count($cellar->allBins()) . autoPluralise(" bin", " bins", count($cellar->allBins()));
if (!empty($cellar->notes)) {
	$subtitle .= " <i>(" . $cellar->notes . ")</i>";
}
$icons[] = array("class" => "btn-info", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Edit Cellar", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#editCellarModal\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Bin", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#newBinModal\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=add&cellar_uid=" . $cellar->uid . "'\"");
echo makeTitle($title, $subtitle, $icons);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?php echo $cellar->name; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row pb-3">
	<div class="col">
		<input type="text" id="wine_search" class="form-control form-control-lg" placeholder="Quick search" autocomplete="off" spellcheck="false" aria-describedby="wine_searchHelp">
		<ul id="wine_search_results" class="list-group"></ul>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup"><?php echo $cellar->allBottles(); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<?php
	$categories = array_slice(explode(",", $settingsClass->value('wine_category')), 0, 5, true);
	
	foreach ($categories AS $wine_category) {
		$winesByCategory = $cellar->allBottles(array('wine_wines.category' => $wine_category));
		
		if ($winesByCategory > 0) {
			$output  = "<div class=\"col\">";
			$output .= "<div class=\"card mb-3\">";
			$output .= "<div class=\"card-body\">";
			$output .= "<h5 class=\"card-title countup\">" . $winesByCategory . "</h5>";
			$output .= "<h6 class=\"card-subtitle mb-2 text-truncate text-body-secondary\">" . $wine_category . "</h6>";
			$output .= "</div>";
			$output .= "</div>";
			$output .= "</div>";
			
			echo $output;
		}
		
	}
	?>
</div>

<div id="chart"></div>

<?php
echo $cellar->binsTable($cellar->allBins());
?>

<form method="post" id="bin_new" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="modal fade" id="newBinModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add New Bin</h5>
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
					<input type="text" class="form-control" id="name" name="name">
				</div>
				<div class="mb-3">
					<label for="category" class="form-label">Bin Category</label>
					<select class="form-select" id="category" name="category" required>
						<?php
						foreach (explode(",", $settingsClass->value('wine_category')) AS $wine_category) {
							if (isset($wine->category) && $wine->category == $wine_category) {
								echo "<option selected>" . $wine_category . "</option>";
							} else {
								echo "<option>" . $wine_category . "</option>";
							}
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Bin Description</label>
					<textarea class="form-control" id="description" name="description" rows="3"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="addBin" name="editCellar" />
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" name="submit" class="btn btn-primary">Add Bin</button>
			</div>
			
		</div>
	</div>
</div>
</form>

<form method="post" id="cellar_edit" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="modal fade" id="editCellarModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Cellar</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label for="name" class="form-label">Cellar Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?php echo $cellar->name; ?>">
				</div>
				<div class="mb-3">
					<label for="short_code" class="form-label">Cellar Short Code</label>
					<input type="text" class="form-control" id="short_code" name="short_code" value="<?php echo $cellar->short_code; ?>">
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Cellar Notes</label>
					<textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $cellar->notes; ?></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editCellar" name="editCellar" />
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" name="submit" class="btn btn-primary">Edit Cellar</button>
			</div>
			
		</div>
	</div>
</div>
</form>

<script>
document.getElementById('wine_search').addEventListener('keyup', function() {
	let query = this.value;
	let resultsDiv = document.getElementById('wine_search_results');

	// Clear the results if the input is empty
	if (query.trim() === '') {
		resultsDiv.innerHTML = '';
		return;
	}
	
	// Create a new XMLHttpRequest object
	let xhr = new XMLHttpRequest();
	
	// Configure it: GET-request for the URL with the query
	xhr.open('GET', 'actions/wine_search.php?c=<?php echo $cellar->uid;?>&q=' + encodeURIComponent(query), true);
	
	// Set up the callback function
	xhr.onload = function() {
		if (xhr.status === 200) {
			// Parse JSON response
			let response = JSON.parse(xhr.responseText);
			
			// Display the results
			resultsDiv.innerHTML = '';

			if (response.data.length === 0) {
				let listItem = document.createElement('li');
				listItem.className = "list-group-item";
				listItem.textContent = 'No results found';
				
				resultsDiv.appendChild(listItem);
			} else {
				response.data.forEach(function(item) {
					let listItem = document.createElement('li');
					listItem.className = "list-group-item";
					var link = document.createElement("a");
					link.href = "index.php?n=wine_wine&wine_uid=" + item.uid;
					link.textContent = item.name;
					listItem.appendChild(link);
					
					resultsDiv.appendChild(listItem);
				});
			}
		}
	};
	
	// Send the request
	xhr.send();
});
</script>

<?php
foreach ($cellar->allBottlesByGrape() AS $grape => $count) {
	$data[] = "{ x: '" . $grape . "', y: " . $count . "}";
}
?>
<script>
var options = {
  series: [
  {
	data: [<?php echo implode(",", $data); ?>]
  }
],
  legend: {
  show: false
},
chart: {
  height: 350,
  type: 'treemap',
  toolbar: {
	  show: false
  },
  events: {
	  click(event, chartContext, opts) {
		  var clicked_grape = opts.config.series[opts.seriesIndex].data[opts.dataPointIndex].x;
		  window.location.href = 'index.php?n=wine_search&filter=grape&value=' + clicked_grape;
	  }
  }
}
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>