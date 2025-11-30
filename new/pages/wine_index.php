<?php
$user->pageCheck('wine');

$wines = new Wines();

echo pageTitle(
	"Wine Management",
	"Manage wine stock and create transactions",
	[
		[
			'permission' => 'wine',
			'title' => 'Add Cellar',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addCellarModal'
			]
		],
		[
			'permission' => 'wine',
			'title' => 'Manage Lists',
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
		],
		[
			'permission' => 'wine',
			'title' => 'Bulk Edit Wine',
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
		<li class="breadcrumb-item active">Index</li>
	</ol>
</nav>

<hr/>

<div class="input-group mb-3">
  <input type="text" class="form-control form-control-lg" id="wine_search" placeholder="Quick search all cellars" autocomplete="off" spellcheck="false">
  <a href="index.php?page=wine_filter" type="button" class="btn btn-lg btn-outline-secondary">Advanced Filter</a>
</div>
<ul id="wine_search_results" class="list-group"></ul>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?php echo $wines->wineBottlesTotal(); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<?php
	$wine_categories = array_slice(explode(",", $settings->get('wine_category')), 0, 5, true);
	
	foreach ($wine_categories as $wine_category) {
		$winesByCategory = $wines->wines([
			'wine_wines.category' => ['=', $wine_category]
		]);
		
		$total = count($winesByCategory);
		
		if ($total > 0) {
			$wineBottlesCount = 0;
			foreach($winesByCategory as $wine) {
				$wineBottlesCount += $wine->currentQty();
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
	foreach ($wines->cellars() as $cellar) {
		echo $cellar->card();
	}
	?>
</div>

<hr class="pb-3" />

<div class="row">
	<div class="col">
		<h1>Recent Transactions</h1>
		<div>
		  <canvas id="myChart"></canvas>
		</div>
		
		<div id="chart_transactions_by_day"></div>
		
		<p><a href="index.php?page=wine_transactions" class="float-end">View all</a></p>
		<?php
		  //$transaction = new transaction();
		  //$subsetOfTransactions = array_slice($wines->allTransactions(), 0, 20, true);
		  //echo $transaction->transactionsTable($subsetOfTransactions);
		  ?>
	</div>
</div>


<!-- Delete Term Modal -->
<div class="modal fade" tabindex="-1" id="addCellarModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add Cellar</h5>
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
					<label for="description" class="form-label">Bin Types</label>
					<textarea class="form-control" id="bin_types" name="bin_types" rows="3"></textarea>
					<div id="bin-typesHelp" class="form-text">Comma,Separated,List</div>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Notes</label>
					<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Add Cellar</button>
			</div>
		</div>
	</div>
	</form>
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
$daysToInclude = $settings->get('logs_display');

$dateArray = array();
for ($i = 0; $i < $daysToInclude; $i++) {
  // Generate the date string for $i days ago
  $date = date('Y-m-d', strtotime("-$i days"));

  // Assign the date as a key in the array with a default value
  $dateArray[$date] = 0;
}

foreach ($dateArray as $date => $value) {
	$transactions = $wines->allTransactions(array('DATE(date)' => $date));
	
	$bottlesTotal = 0;
	foreach ($transactions as $transaction) {
		if (isset($transaction)) {
			$bottlesTotal= $bottlesTotal + abs($transaction['bottles']);
		}
	}
	$transactionsTotal[$date] += $bottlesTotal;
}

ksort($transactionsTotal);
?>

<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
	type: 'bar',
	data: {
	  labels: ['<?php echo implode("','", array_keys($transactionsTotal)); ?>'],
	  datasets: [{
		label: '# of logs',
		data: [<?php echo implode(",", $transactionsTotal); ?>],
		borderWidth: 1
	  }]
	},
	options: {
	  plugins: {
	  legend: {
		  display: false
		}
	  },
	  maintainAspectRatio: false,
	  scales: {
		y: {
		  beginAtZero: true
		}
	  }
	}
  });
</script>