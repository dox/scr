<?php
$user->pageCheck('wine');

$wines = new Wines();

echo pageTitle(
	"Wine Transactions",
	"All transactions",
	[
		[
			'permission' => 'wine',
			'title' => 'Edit Transaction',
			'class' => '',
			'event' => '',
			'icon' => 'pencil',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addCellarModal'
			]
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
	'date' => [
		['>', $dateFrom],
		['<', $dateTo]
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
			<input type="date" class="form-control" id="dateFrom" name="date_from" value="<?= $dateFrom ?>">
		</div>
		<div class="col">
			<label for="dateTo" class="form-label">Date To</label>
			<input type="date" class="form-control" id="dateTo" name="date_to" value="<?= $dateTo ?>">
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
