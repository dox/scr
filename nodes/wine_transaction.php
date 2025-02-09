<?php
pageAccessCheck("wine");

$transactionUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wineClass = new wineClass();
$transaction = new transaction($transactionUID);

$title = "Transaction";
$subtitle = $transaction->name;
if (!empty($transaction->description)) {
	$subtitle .= " <i>(" . $transaction->description . ")</i>";
}

$icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Transaction", "value" => "onclick=\"transactionDelete(" . $transaction->uid . ")\"");

echo makeTitle($title, $subtitle, $icons, true);
?>



<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_transactions">Transactions</a></li>
		<li class="breadcrumb-item active"><?php echo $transaction->uid; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="card">
	<div class="card-body">
		<div class="invoice-title">
			<h4 class="float-end">Transaction ID #<?php echo $transaction->uid . " " . $transaction->typeBadge(); ?></h4>
			<div class="mb-4">
				<h2 class="mb-1 text-muted"><?php echo site_name; ?>: Wine</h2>
			</div>
			<div class="text-muted">
				<p class="mb-1">Created: <?php echo $transaction->username; ?>, <?php echo dateDisplay($transaction->date, true) . " " . timeDisplay($transaction->date); ?></p>
			</div>
		</div>
		
		<hr class="my-4">
		
		<div class="row">
			<div class="col-sm-6">
				
			</div>
			<div class="col-sm-6">
				<div class="text-muted text-sm-end">
					<div class="mt-4">
						<h5 class="mb-1">Name</h5>
						<p class="mb-3"><?php echo $transaction->name; ?>
						<?php if (!empty($transaction->description)) {
							echo "<br /><i>" . $transaction->description . "</i>";
						}
						?></p>
						<h5 class="mb-1">Date</h5>
						<p><?php echo dateDisplay($transaction->date_posted, true); ?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="py-2">
			<h5 class="font-size-15">Contents Summary</h5>
			<div class="table-responsive">
				<table class="table align-middle table-nowrap table-centered mb-0">
					<thead>
						<tr>
							<th style="width: 70px;">Bin</th>
							<th>Item</th>
							<th>Price</th>
							<th>Qty.</th>
							<th class="text-end" style="width: 120px;">Total</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$runningTotal = 0;
						
						foreach ($transaction->allLinkedTransactions() AS $lineTransaction) {
							$lineTransaction = new transaction($lineTransaction['uid']);
							$wine = new wine($lineTransaction->wine_uid);
							$bin = new bin($wine->bin_uid);
							$cellar = new cellar($lineTransaction->cellar_uid);
							
							$item = json_decode($lineTransaction->snapshot);
							
							if ($lineTransaction->bottles < 0) {
								$bottlesClass = "text-danger";
							} elseif($lineTransaction->bottles > 0) {
								$bottlesClass = "text-success";
							} else {
								$bottlesClass = "";
							}
							
							$output  = "<tr>";
							$output .= "<th scope=\"row\" class=\"align-top\"><a href=\"index.php?n=wine_bin&bin_uid=" . $bin->uid . "\">" . $bin->name . "</a></th>";
							$output .= "<td>";
							$output .= "<div>";
							if (!empty($item->vintage)) {
								$name = $item->name . " (" . $item->vintage . ")";
							} else {
								$name = $item->name;
							}
							$name = "<a href=\"index.php?n=wine_wine&wine_uid=" . $item->uid . "\">" . $name . "</a>";
							$output .= "<h5 class=\"text-truncate mb-1\">" . $name . "</h5>";
							$output .= "<p class=\"text-muted mb-0\">" . $item->grape . "</p>";
							$output .= "</div>";
							$output .= "</td>";
							$output .= "<td>" . currencyDisplay($lineTransaction->price_per_bottle) . "</td>";
							$output .= "<td class=\"" . $bottlesClass . "\">" . abs($lineTransaction->bottles) . "</td>";
							$output .= "<td class=\"text-end\">" . currencyDisplay($lineTransaction->price_per_bottle * abs($lineTransaction->bottles)) . "</td>";
							$output .= "</tr>";
							
							$runningTotal += $lineTransaction->price_per_bottle * abs($lineTransaction->bottles);
							
							echo $output;
						}
						?>
						
						<tr>
							<th scope="row" colspan="4" class="border-0 text-end">Total</th>
							<td class="border-0 text-end">
								<h4 class="m-0 fw-semibold"><?php echo currencyDisplay($runningTotal); ?></h4>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="d-print-none mt-4">
				<div class="float-end">
					<!--<a href="javascript:window.print()" class="btn btn-info me-1">PRINT</a>-->
				</div>
			</div>
		</div>
	</div>
</div>


<script>
function transactionDelete(transactionUID) {
  var transaction_uid = transactionUID;
  
  if (window.confirm("Are you really sure you want to delete this transaction?  The current stock quantity will be recalcualted")) {
	var xhr = new XMLHttpRequest();

	var formData = new FormData();
	formData.append("transaction_uid", transaction_uid);
	
	xhr.onload = async function() {
	  location.href = 'index.php?n=wine_transactions';
	}

	xhr.onerror = function(){
	  // failure case
	  alert (xhr.responseText);
	}

	xhr.open ("POST", "../actions/wine_transactionDelete.php", true);
	xhr.send (formData);

	return false;
  }
}
</script>