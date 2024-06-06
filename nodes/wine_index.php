<?php
pageAccessCheck("wine");

$title = "Wine Management";
$subtitle = "BETA FEATURE!";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#journal-text\"/></svg> Add Order", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");

echo makeTitle($title, $subtitle, $icons);

$wineClass = new wineClass();
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row">
	<?php
	foreach ($wineClass->getAllCellars() AS $cellar) {
		$cellar = new cellar($cellar['uid']);
		
		$output  = "<div class=\"col mb-3\">";
		$output .= "<div class=\"card shadow-sm\">";
		$output .= "<img src=\"" . $cellar->photograph . "\" class=\"card-img-top\" alt=\"Cellar photograph\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<p class=\"card-text\">" . $cellar->name . "</p>";
		$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
		$output .= "<div class=\"btn-group\">";
		$output .= "<a href=\"index.php?n=wine_cellar&uid=" . $cellar->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
		$output .= "<a href=\"index.php?n=wine_cellar&uid=" . $cellar->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">Edit</a>";
		$output .= "</div>";
		$output .= "<small class=\"text-body-secondary\">";
		$output .= count($cellar->getBins()) . autoPluralise(" bin", " bins", count($cellar->getBins()));
		$output .= " / ";
		$output .= $cellar->getAllWineBottlesTotal() . autoPluralise(" wine", " wines", $cellar->getAllWineBottlesTotal());
		$output .= "</small>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		echo $output;
	}
	?>
</div>

<style>
	#wine_search_results {
		position: absolute;
		z-index: 1;
		max-height: 200px;
		overflow-y: auto;
	}
</style>

<div class="row">
	<div class="col">
		<input type="text" id="wine_search" class="form-control form-control-lg" placeholder="Quick search" autocomplete="off" spellcheck="false" aria-describedby="wine_searchHelp">
		<ul id="wine_search_results" class="list-group"></ul>
		
	</div>
</div>

<div id="chart"></div>

<div class="row">
	<h1>My Lists</h1>
	<?php
	$output = "<ul class=\"list-group\">";
	foreach ($wineClass->getAllLists() AS $list) {
		$list = new wine_list($list['uid']);
		
		$output .= "<li  class=\"list-group-item\">";
		$output .= "<svg width=\"1em\" height=\"1em\" class=\"text-muted\"><use xlink:href=\"img/icons.svg#heart-full\"/></svg> ";
		$output .= "<a href=\"index.php?n=wine_search&filter=list&value=" . $list->uid . "\">";
		$output .= $list->name_full();
		$output .= "</a>";
		
		$output .= "</li>";
	}
	$output .= "</ul>";
	echo $output;

	?>
</div>

<script>
	document.getElementById('wine_search').addEventListener('keyup', function() {
		let query = this.value;
		
		// Create a new XMLHttpRequest object
		let xhr = new XMLHttpRequest();
		
		// Configure it: GET-request for the URL with the query
		xhr.open('GET', 'actions/wine_search.php?q=' + encodeURIComponent(query), true);
		
		// Set up the callback function
		xhr.onload = function() {
			if (xhr.status === 200) {
				// Parse JSON response
				let response = JSON.parse(xhr.responseText);
				
				// Display the results
				let resultsDiv = document.getElementById('wine_search_results');
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
						link.href = "index.php?n=wine_wine&uid=" + item.uid; // Replace "https://example.com" with your actual URL
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
foreach ($wineClass->stats_winesByGrape() AS $grape => $count) {
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
  type: 'treemap'
},
title: {
  text: 'Basic Treemap'
}
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>