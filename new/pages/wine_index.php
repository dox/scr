<?php
$user->pageCheck('wine');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['deleteCellarUID'])) {
		$deleteCellarUID = filter_input(INPUT_POST, 'deleteCellarUID', FILTER_SANITIZE_NUMBER_INT);
		
		//$term = new Term($deleteTermUID);
		//$term->delete();
	} else {
		$cellar = new Cellar();
		$cellar->create($_POST);
	}
}

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
			'title' => 'Add Transaction',
			'class' => '',
			'event' => '',
			'icon' => 'receipt',
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

<div class="wine-search-wrapper position-relative">
  <div class="input-group mb-3">
	<input 
	  type="text"
	  class="form-control form-control-lg"
	  id="wine_search"
	  placeholder="Quick search all cellars"
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
   data-url="./ajax/wine_stats.php"
   data-selected="true">
</a>
<div id="wine_stats_container">
	<div class="spinner-border" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
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
		
		<table class="table">
		  <thead>
			<tr>
			  <th scope="col">Date</th>
			  <th scope="col">Username</th>
			  <th scope="col">Bottles</th>
			  <th scope="col">£</th>
			  <th scope="col">Name</th>
			</tr>
		  </thead>
		  <tbody>
			  <?php
			  $daysToInclude = $settings->get('logs_display');
			  $transactions = $wines->transactionsGrouped([
				  'DATE(date)' => ['>', date('Y-m-d', strtotime('- ' . $daysToInclude . ' days '))]
			  ]);
			  
			  foreach ($transactions as $transaction) {
				  $url = "index.php?page=wine_transaction&uid=" . $transaction->uid;
				  
				  $output  = "<tr>";
				  $output .= "<th scope=\"row\">" . formatDate($transaction->date, 'short') . "</th>";
				  $output .= "<td>" . ($transaction->username) . "</td>";
				  $output .= "<td>" . $transaction->totalBottles() . "</td>";
				  $output .= "<td>" . formatMoney($transaction->totalValue()) . "</td>";
				  $output .= "<td><a href=\"" . $url . "\">" . htmlspecialchars($transaction->name) . "</a></td>";
				  $output .= "</tr>";
				  
				  echo $output;
			  }
			  ?>
			  
			
		  </tbody>
		</table>
	</div>
</div>


<!-- Add Cellar Modal -->
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
							<label for="name" class="form-label">Name</label>
							<input type="text" class="form-control" id="name" name="name">
						</div>
					</div>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Sections</label>
					<textarea class="form-control" id="sections" name="sections" rows="3"></textarea>
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

<?php
$dateArray = array();
for ($i = 0; $i < $daysToInclude; $i++) {
  // Generate the date string for $i days ago
  $date = date('Y-m-d', strtotime("-$i days"));

  // Assign the date as a key in the array with a default value
  $dateArray[$date] = 0;
}

foreach ($dateArray as $date => $value) {
	$transactions = $wines->transactions(array('DATE(date)' => $date));
	
	$bottlesTotal = 0;
	foreach ($transactions as $transaction) {
		if (isset($transaction)) {
			$bottlesTotal += abs($transaction->bottles);
		}
	}
	$dateArray[$date] += $bottlesTotal;
}

ksort($dateArray);
?>

<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
	type: 'bar',
	data: {
	  labels: ['<?php echo implode("','", array_keys($dateArray)); ?>'],
	  datasets: [{
		label: '# of bottles in transactions',
		data: [<?php echo implode(",", $dateArray); ?>],
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

<script>
// Initialize stats links
initAjaxLoader('.wine_stats_link', '#wine_stats_container');

// Handle wine live searching
liveSearch(
	'wine_search',
	'wine_search_results',
	'./ajax/wine_livesearch.php'
);
</script>