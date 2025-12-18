<?php
$user->pageCheck('wine');

$wines = new Wines();
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
$transaction = new Transaction($cleanUID);

if ($transaction->isLinked()) {
	$transactions = $transaction->linkedTransactions();
} else {
	$transactions[] = $transaction;
}

$transactionMember = Member::fromLDAP($transaction->username);

//printArray($transactions);

echo pageTitle(
	"Wine Transaction",
	htmlspecialchars($transaction->name),
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
?>
<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?page=wine_transactions">Transactions</a></li>
		<li class="breadcrumb-item active">Transaction <?= htmlspecialchars($transaction->uid) ?></li>
	</ol>
</nav>

<div class="card my-3">
	<div class="card-body">
		<div class="invoice-title">
			<h4 class="float-end">Transaction ID #<?= htmlspecialchars($transaction->uid) ?> <span class="badge rounded-pill text-bg-info"><?= htmlspecialchars($transaction->type) ?></span></h4>
			<div class="mb-4">
				<h2 class="mb-1 text-muted">SCR Meal Booking: Wine</h2>
			</div>
			<div class="text-muted">
				<p class="mb-1">Created by <?= htmlspecialchars($transactionMember->name()) ?>, <?= formatDate($transaction->date) . " " . formatTime($transaction->date) ?></p>
			</div>
		</div>
		
		<hr class="my-4">
		
		<div class="text-sm-end">
			<div class="mt-4">
				<h5 class="mb-1">Name</h5>
				<p class="mb-3 text-muted"><?= htmlspecialchars($transaction->name) ?></p>
				<h5 class="mb-1">Date</h5>
				<p class="text-muted"><?= formatDate($transaction->date_posted) ?></p>
				<?php
				if (!empty($transaction->description)) {
					echo "<h5 class=\"mb-1\">Description</h5>";
					echo "<p class=\"text-muted\">" . htmlspecialchars($transaction->description) . "</p>";
				}
				?>
			</div>
		</div>
		
		<div class="py-2">
			<h5 class="font-size-15">Contents Summary</h5>
			<div class="table-responsive">
				<table class="table align-middle table-nowrap table-centered mb-0">
					<thead>
						<tr>
							<th scope="col" style="width: 70px;">Bin</th>
							<th scope="col" >Item</th>
							<th scope="col" >Price</th>
							<th scope="col" >Qty.</th>
							<th scope="col" class="text-end" style="width: 120px;">Total</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$linkedTotal = 0;
						foreach ($transactions as $row) {
							$wine = new Wine($row->wine_uid);
							$bin = new Bin($wine->bin_uid);
							
							$rowTotal = abs($row->bottles) * $row->price_per_bottle;
							$linkedTotal += $rowTotal;
							
							$output  = "<tr>";
							$output .= "<th scope=\"row\" class=\"align-top\">";
							$output .= "<a href=\"index.php?page=wine_bin&uid=" . $bin->uid . "\">" . htmlspecialchars($bin->name) . "</a>";
							$output .= "</th>";
							$output .= "<td><div><h5 class=\"text-truncate mb-1\"><a href=\"index.php?page=wine_wine&uid=" . $wine->uid . "\">" . $wine->clean_name() . "</a></h5><p class=\"text-muted mb-0\">" . htmlspecialchars($wine->category) . "</p></div></td>";
							$output .= "<td>" . formatMoney($row->price_per_bottle) . "</td>";
							$output .= "<td class=\"" . (($row->bottles < 0) ? 'text-danger' : 'text-success') . "\">" . abs($row->bottles) . "</td>";
							$output .= "<td class=\"text-end\">" . formatMoney($rowTotal) . "</td>";
							$output .= "</tr>";
							
							echo $output;
						}
						?>
										
						<tr>
							<th scope="row" colspan="4" class="border-0 text-end">Total</th>
							<td class="border-0 text-end">
								<h4 class="m-0 fw-semibold"><?= formatMoney($linkedTotal) ?></h4>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>