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
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#journal-text\"/></svg> Edit Cellar", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#editCellarModal\"");
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Bin", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#newBinModal\"");
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=add&cellar_uid=" . $cellar->uid . "'\"");
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Multi-Transaction", "value" => "onclick=\"location.href='index.php?n=wine_transaction_multi'\"");
echo makeTitle($title, $subtitle, $icons, true);
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
		<div class="input-group mb-3">
		  <input type="text" class="form-control form-control-lg" id="wine_search" placeholder="Quick search <?php echo $cellar->name; ?> wine cellar" autocomplete="off" spellcheck="false">
		  <span class="input-group-text" id="basic-addon2"><input class="form-check-input mt-0 me-2" type="checkbox" id="wine_search_include_closed" value="true">include closed</span>
		</div>
		<ul id="wine_search_results" class="list-group"></ul>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup"><?php echo $cellar->allBottlesCount(); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<?php
	$categories = array_slice(explode(",", $settingsClass->value('wine_category')), 0, 5, true);
	
	foreach ($categories AS $wine_category) {
		$winesByCategory = $wineClass->allWines(array('cellar_uid' => $cellar->uid, 'wine_wines.category' => $wine_category), true);
		
		if (count($winesByCategory) > 0) {
			$wineBottlesCount = 0;
			foreach($winesByCategory AS $wine) {
				$wine = new wine($wine['uid']);
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

<div id="chart"></div>

<ul class="nav nav-tabs nav-fill" id="remoteTabs">
	<?php
	$i = 1;
	foreach ($cellar->binTypes() AS $wine_category) {
		$active = "";
		$url = "/nodes/widgets/_cellarBinCategory.php?cellar_uid=" . $cellar->uid . "&category=" . $wine_category;
		$href = "#tab" . $i;
		
		if ($i == 1) {
			$active = "active";
		}
		
		
		$output  = "<li class=\"nav-item\">";
		$output .= "<a class=\"nav-link " . $active . "\" aria-current=\"page\" data-url=\"" . $url . "\" href=\"" . $href . "\" data-bs-toggle=\"tab\">" . $wine_category . "</a>";
		$output .= "</li>";
		
		echo $output;
		
		$i++;
	}
	?>
</ul>

<div class="tab-content mt-3" id="tabContent">
	<?php
	$i = 1;
	foreach ($cellar->binTypes() AS $wine_category) {
		$active = "";
		if ($i == 1) {
			$active = "show active";
		}
		
		$output  = "<div class=\"tab-pane fade " . $active . "\" id=\"tab" . $i . "\">";
		$output .= "Loading content for " . $wine_category . "...";
		$output .= "</div>";
		
		echo $output;
		
		$i++;
	}
	?>
</div>

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
					<select disabled class="form-select" id="cellar_uid" name="cellar_uid" required>
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
					<input type="hidden" id="cellar_uid" name="cellar_uid" value="<?php echo $cellar->uid; ?>">
				</div>
				<div class="mb-3">
					<label for="name" class="form-label">Bin Name</label>
					<input type="text" class="form-control" id="name" name="name">
				</div>
				<div class="mb-3">
					<label for="category" class="form-label">Bin Category</label>
					<select class="form-select" id="category" name="category" required>
						<?php
						foreach ($cellar->binTypes() AS $wine_category) {
							echo "<option>" . $wine_category . "</option>";
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
				<input type="hidden" id="addBin" name="addBin" />
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
				<div class="row">
				<div class="col-3">
					<div class="mb-3">
						<div class="mb-3">
							<label for="short_code" class="form-label">Short Code</label>
							<input type="text" class="form-control" id="short_code" name="short_code" value="<?php echo $cellar->short_code; ?>" maxlength="2">
						</div>
					</div>
				</div>
				<div class="col-9">
					<div class="mb-3">
						<label for="name" class="form-label">Cellar Name</label>
						<input type="text" class="form-control" id="name" name="name" value="<?php echo $cellar->name; ?>">
					</div>
				</div>
				</div>
				<div class="mb-3">
					<div class="accordion accordion-flush" id="accordionFlushExample">
					  <div class="accordion-item">
						<h2 class="accordion-header">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
							Photograph
						  </button>
						</h2>
						<div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
						  <div class="accordion-body">
							  <?php
							  $mealsClass = new meals();
							  foreach ($mealsClass->mealCardImages() AS $photo) {
								if ($photo == $cellar->photograph) {
								  $selected = " checked ";
								} else {
								  $selected = "";
								}
								
								$output  = "<div class=\"col\">";
								$output .= "<div class=\"card mb-3\">";
								$output .= "<img src=\"img/cards/" . $photo . "\" class=\"card-img-top\" alt=\"...\">";
								$output .= "<div class=\"card-body\">";
								$output .= "<p class=\"card-text\"><label for=\"photo-" . $photo . "\" class=\"form-label\">";
								$output .= "<input class=\"form-check-input\" type=\"radio\" name=\"photograph\" id=\"photo-" . $photo . "\" value=\"" . $photo . "\"" . $selected . "> ";
								$output .= $photo . "</label></p>";
								$output .= "</div>";
								$output .= "</div>";
								$output .= "</div>";
								
								echo $output;
							  }
							  ?>
						  </div>
						</div>
					  </div>
					  
					</div>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Bin Types</label>
					<textarea class="form-control" id="bin_types" name="bin_types" rows="3"><?php echo $cellar->bin_types; ?></textarea>
					<div id="bin-typesHelp" class="form-text">Comma,Separated,List</div>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Notes</label>
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
	
	let include_closed = document.getElementById('wine_search_include_closed').checked;

	// Clear the results if the input is empty
	if (query.trim() === '') {
		resultsDiv.innerHTML = '';
		return;
	}
	
	// Create a new XMLHttpRequest object
	let xhr = new XMLHttpRequest();
	
	// Configure it: GET-request for the URL with the query
	xhr.open('GET', 'actions/wine_search.php?c=<?php echo $cellar->uid;?>&q=' + encodeURIComponent(query) + '&include=' + include_closed, true);
	
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
$data = array();
foreach ($cellar->allBottlesByGrape() AS $grape => $count) {
	if (!empty($grape)) {
		$data[] = "{ x: '" . $grape . "', y: " . $count . "}";
	}
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
		  window.location.href = 'index.php?n=wine_search&filter=grape&cellar_uid=' + <?php echo $cellar->uid; ?> + '&value=' + clicked_grape;
	  }
  }
}
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>

<script>
// Function to load content into a specific tab pane
function loadTabContent(tabLink) {
	const targetId = tabLink.getAttribute('href').substring(1); // Get tab pane ID
	const url = tabLink.getAttribute('data-url'); // Get URL
	const targetPane = document.getElementById(targetId); // Get the associated tab pane
	
	// Fetch and load content only if not already loaded
	if (targetPane.innerHTML.trim() === '' || targetPane.innerHTML.startsWith('Loading')) {
		fetch(url)
			.then(response => {
				if (!response.ok) {
					throw new Error(`HTTP error! Status: ${response.status}`);
				}
				return response.text();
			})
			.then(data => {
				targetPane.innerHTML = data; // Populate the tab pane
			})
			.catch(error => {
				targetPane.innerHTML = `Error loading content: ${error.message}`; // Handle errors
			});
	}
}

// Main script
document.addEventListener('DOMContentLoaded', function () {
	const tabs = document.querySelectorAll('#remoteTabs .nav-link');

	// Load content for the first active tab when the page loads
	const activeTab = document.querySelector('#remoteTabs .nav-link.active');
	if (activeTab) {
		loadTabContent(activeTab);
	}

	// Add event listeners for tab switching
	tabs.forEach(tab => {
		tab.addEventListener('shown.bs.tab', function (event) {
			loadTabContent(event.target); // Load content when a tab is activated
		});
	});
});
</script>