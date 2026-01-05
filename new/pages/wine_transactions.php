<?php
$user->pageCheck('wine');

$wines = new Wines();

echo pageTitle(
	"Wine Transactions",
	"All transactions",
	[
		[
			'permission' => 'wine',
			'title' => 'Add Transaction',
			'class' => '',
			'event' => 'index.php?page=wine_transaction_add',
			'icon' => 'receipt'
		]
	]
);

// Set default dates
$currentTerm = $terms->currentTerm();
$defaultFrom = $currentTerm->date_start;
$defaultTo   = $currentTerm->date_end;

// Sanitize POST inputs
$dateFrom = isset($_POST['date_from']) ? htmlspecialchars($_POST['date_from']) : $defaultFrom;
$dateTo   = isset($_POST['date_to'])   ? htmlspecialchars($_POST['date_to'])   : $defaultTo;

// Optional: ensure the dates are valid and in the correct order
if (strtotime($dateFrom) > strtotime($dateTo)) {
	// Swap if user entered backwards
	[$dateFrom, $dateTo] = [$dateTo, $dateFrom];
}

$transactions = $wines->transactionsGrouped([
	'DATE(date)' => [
		['>=', $dateFrom],
		['<=', $dateTo]
	]
]);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Transactions</li>
	</ol>
</nav>

<form method="post" id="transactionsBetweenDates" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<div class="row align-items-end">
		<div class="col">
			<label for="dateFrom" class="form-label">Date From</label>
			<input type="text" class="form-control" id="dateFrom" name="date_from" value="<?= $dateFrom ?>" required>
		</div>
		<div class="col">
			<label for="dateTo" class="form-label">Date To</label>
			<input type="text" class="form-control" id="dateTo" name="date_to" value="<?= $dateTo ?>" required>
		</div>
		<div class="col">
			<button type="submit" class="btn btn-primary mt-3 w-100">Submit</button>
		</div>
	</div>
</form>

<table class="table mt-3">
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


<script>
const el = document.getElementById('dateFrom');
const el2 = document.getElementById('dateTo');

const options = {
	defaultDate: new Date('<?= date('c', strtotime($currentTerm->date_start)) ?>'),
	display: {
		icons: {
			type: 'icons',
			time: 'bi bi-clock',
			date: 'bi bi-calendar',
			up: 'bi bi-arrow-up',
			down: 'bi bi-arrow-down',
			previous: 'bi bi-chevron-left',
			next: 'bi bi-chevron-right',
			today: 'bi bi-calendar-check',
			clear: 'bi bi-trash',
			close: 'bi bi-close'
		},
		components: {
			calendar: true,
			date: true,
			month: true,
			year: true,
			decades: true,
			clock: false
		}
	},
	localization: {
		format: 'yyyy-MM-dd',
	  }
};

new tempusDominus.TempusDominus(el, options);
new tempusDominus.TempusDominus(el2, options);
</script>