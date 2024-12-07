<?php
pageAccessCheck("wine");

$title = "Wine Management";
$subtitle = "BETA FEATURE!";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=add&cellar_uid=1'\"");

echo makeTitle($title, $subtitle, $icons);

$wineClass = new wineClass();
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
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
				<h5 class="card-title"><?php echo $wineClass->getAllWineBottlesTotal(); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<?php
	$categories = array_slice(explode(",", $settingsClass->value('wine_category')), 0, 5, true);
	
	foreach ($categories AS $wine_category) {
		$winesByCategory = $wineClass->getAllWinesByFilter('category', $wine_category);
		
		if (count($winesByCategory) > 0) {
			$output  = "<div class=\"col\">";
			$output .= "<div class=\"card mb-3\">";
			$output .= "<div class=\"card-body\">";
			$output .= "<h5 class=\"card-title\">" . count($winesByCategory) . "</h5>";
			$output .= "<h6 class=\"card-subtitle mb-2 text-truncate text-body-secondary\">" . $wine_category . "</h6>";
			$output .= "</div>";
			$output .= "</div>";
			$output .= "</div>";
			
			echo $output;
		}
		
	}
	?>
</div>

<div class="row">
	<?php
	foreach ($wineClass->getAllCellars() AS $cellar) {
		$cellar = new cellar($cellar['uid']);
		
		$output  = "<div class=\"col-sm-12 col-md-6 mb-3\">";
		$output .= "<div class=\"card shadow-sm\">";
		$output .= "<img src=\"" . $cellar->photograph . "\" class=\"card-img-top\" alt=\"Cellar photograph\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<p class=\"card-text\">" . $cellar->name . "</p>";
		$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
		$output .= "<a href=\"index.php?n=wine_cellar&uid=" . $cellar->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary stretched-link\">View</a>";
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

<div class="row">
	<div class="col-sm-12 col-md-6 mb-3">
		<h1>Recent Transactions</h1>
		<div id="chart_transactions_by_day"></div>
		<?php
		$wineTransactions = new wine_transactions();
		
		$output = "<ul class=\"list-group\">";
		foreach ($wineTransactions->getAllTransactions() AS $transaction) {
			
			
			$output .= "<li  class=\"list-group-item\">";
			$output .= "<a href=\"index.php?n=wine_wine&uid=" . $transaction['wine_uid'] . "\">";
			$output .= dateDisplay($transaction['date']) . " " . $transaction['wine_uid'] . " " . $transaction['cellar_uid'];
			$output .= $transaction['description'];
			$output .= "</a>";
			
			$output .= "</li>";
		}
		$output .= "</ul>";
		echo $output;
		
		?>
	</div>
	<div class="col-sm-12 col-md-6">
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
	xhr.open('GET', 'actions/wine_search.php?q=' + encodeURIComponent(query), true);
	
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
foreach ($wineClass->stats_transactionsByDate(30) AS $date) {
	$transactions[] = $date['dateTransactionTotals'];
	$bottles[] = $date['transactionBottleTotals'];
	$series[] = "'" . $date['date'] . "'";
}
?>
<script>
var options = {
  series: [{
	  name:'Total Transactions',
	  data: [<?php echo implode(",", $transactions); ?>]
  }, {
	  name:'Total Bottles',
		data: [<?php echo implode(",", $bottles); ?>]
  }],
  legend: {
  show: false
},
chart: {
  height: 350,
  type: 'bar',
  stacked: true,
  toolbar: {
	  show: false
  },
  zoom: {
	  enabled: false,
  }
},
dataLabels: {
	enabled: false
},
xaxis: {
  type: 'datetime',
  categories: [<?php echo implode(",", $series); ?>]
},
};

var chart = new ApexCharts(document.querySelector("#chart_transactions_by_day"), options);
chart.render();
</script>