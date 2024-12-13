<?php
pageAccessCheck("wine");

$title = "Wine Management";
$subtitle = "Manage wine stock and create transactions";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Cellar", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#newCellarModal\"");

echo makeTitle($title, $subtitle, $icons);

$wineClass = new wineClass();

// check if we need to create a new bin
if (isset($_POST['name'])) {
	$newCellar = new cellar();
	$newCellar->name = $_POST['name'];
	$newCellar->short_code = $_POST['short_code'];
	$newCellar->notes = $_POST['notes'];
	
	$newCellar->create();
}
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Index</li>
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
				<h5 class="card-title"><?php echo $wineClass->wineBottlesTotal(); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<?php
	$categories = array_slice(explode(",", $settingsClass->value('wine_category')), 0, 5, true);
	
	foreach ($categories AS $wine_category) {
		$winesByCategory = $wineClass->allWines(array('wine_wines.category' => $wine_category));
		
		if (count($winesByCategory) > 0) {
			$wineBottlesCount = 0;
			foreach($winesByCategory AS $wine) {
				$wineBottlesCount = $wineBottlesCount + $wine['qty'];
			}
			$url = "index.php?n=wine_search&filter=category&value=" . $wine_category;
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

<div class="row">
	<?php
	foreach ($wineClass->allCellars() AS $cellar) {
		$cellar = new cellar($cellar['uid']);
		
		echo $cellar->card();
	}
	?>
</div>

<div class="row">
	<div class="col-sm-12 col-md-6 mb-3">
		<h1>Recent Transactions</h1>
		<div id="chart_transactions_by_day"></div>
		<?php
		$output = "<ul class=\"list-group\">";
		$subsetOfTransactions = array_slice($wineClass->allTransactions(), 0, 10, true);
		
		foreach ($subsetOfTransactions AS $transaction) {
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
		foreach ($wineClass->allLists(array('member_ldap' => $_SESSION['username'])) AS $list) {
			//$list = new wine_list($list['uid']);
			
			$output .= "<li  class=\"list-group-item\">";
			$output .= "<svg width=\"1em\" height=\"1em\" class=\"text-muted\"><use xlink:href=\"img/icons.svg#heart-full\"/></svg> ";
			$output .= "<a href=\"index.php?n=wine_search&filter=list&value=" . $list['uid'] . "\">";
			$output .= $list['name'];
			$output .= "</a>";
			
			$output .= "</li>";
		}
		$output .= "</ul>";
		echo $output;
		
		?>
	</div>
</div>


<form method="post" id="bin_new" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="modal fade" id="newCellarModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add New Cellar</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-3">
						<div class="mb-3">
							<label for="short_code" class="form-label">Short Code</label>
							<input type="text" class="form-control" id="short_code" name="short_code" maxlength="2">
						</div>
					</div>
					<div class="col-9">
						<div class="mb-3">
							<label for="name" class="form-label">Cellar Name</label>
							<input type="text" class="form-control" id="name" name="name">
						</div>
					</div>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Notes</label>
					<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" name="submit" class="btn btn-primary">Add Cellar</button>
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
$daysToInclude = 10;
$dateArray = array();
for ($i = 0; $i < $daysToInclude; $i++) {
  // Generate the date string for $i days ago
  $date = date('Y-m-d', strtotime("-$i days"));

  // Assign the date as a key in the array with a default value
  $dateArray[] = $date;
}

foreach ($dateArray AS $date) {
	$transactions = $wineClass->allTransactions(array('DATE(date)' => $date));
	
	$bottlesTotal = 0;
	foreach ($transactions AS $transaction) {
		if (isset($transaction)) {
			$bottlesTotal= $bottlesTotal + abs($transaction['bottles']);
		}
		
	}
	//printArray($transactions);
	$series[$date] = "'" . $date . "'";
	$transactionsTotal[$date] = count($transactions);
	$transactionsBottlesTotal[$date] = $bottlesTotal[$date] + $bottlesTotal;
}
?>
<script>
var options = {
  series: [{
	  name:'Total Transactions',
	  data: [<?php echo implode(",", $transactionsTotal); ?>]
  }, {
	  name:'Total Bottles',
		data: [<?php echo implode(",", $transactionsBottlesTotal); ?>]
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