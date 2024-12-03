<?php
pageAccessCheck("wine");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$cellar = new cellar($cleanUID);

$title = $cellar->name . " Wine Cellar";
$subtitle = "BETA FEATURE!";
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
				<h5 class="card-title countup"><?php echo $cellar->getAllWineBottlesTotal(); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<?php
	$categories = array_slice(explode(",", $settingsClass->value('wine_category')), 0, 5, true);
	
	foreach ($categories AS $wine_category) {
		$winesByCategory = $cellar->getAllWineBottlesByCategoryTotal($wine_category);
		
		if (count($winesByCategory) > 0) {
			$output  = "<div class=\"col\">";
			$output .= "<div class=\"card mb-3\">";
			$output .= "<div class=\"card-body\">";
			$output .= "<h5 class=\"card-title countup\">" . count($winesByCategory) . "</h5>";
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

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
foreach ($cellar->getBins() AS $bin) {
	$wine = new wine($bin['uid']);
	
	echo $wine->binCard();
}
	?>
</div>

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
					link.href = "index.php?n=wine_wine&uid=" + item.uid;
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
$wineClass = new wineClass;
foreach ($wineClass->stats_winesByGrape($cellar->uid
) AS $grape => $count) {
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