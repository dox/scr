<?php
pageAccessCheck("wine");

$transactionUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wineClass = new wineClass();
$transaction = new transaction($transactionUID);
$wine = new wine($transaction->wine_uid);
$bin = new bin($wine->bin_uid);
$cellar = new cellar($transaction->cellar_uid);


$title = "Transaction";
$subtitle = $transaction->name;
if (!empty($transaction->description)) {
	$subtitle .= " <i>(" . $transaction->description . ")</i>";
}

echo makeTitle($title, $subtitle, $icons, true);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Transactions</a></li>
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
				<p class="mb-1">Posted by: <?php echo $transaction->username; ?>, <?php echo dateDisplay($transaction->date, true) . " " . timeDisplay($transaction->date); ?></p>
			</div>
		</div>
		
		<hr class="my-4">
		
		<div class="row">
			<div class="col-sm-6">
				<div class="text-muted">
					<h5 class="mb-3">Name/Description:</h5>
					<h5 class="mb-3"><?php echo $transaction->name; ?></h5>
					<?php if (!empty($transaction->description)) {
						echo "<p>" . $transaction->description . "</p>";
					}
					?>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="text-muted text-sm-end">
					<div class="mt-4">
						<h5 class="font-size-15 mb-1">Date:</h5>
						<p><?php echo dateDisplay($transaction->date, true); ?></p>
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
						$item = json_decode($transaction->snapshot);
						
						if ($transaction->bottles < 0) {
							$bottlesClass = "text-danger";
						} elseif($transaction->bottles > 0) {
							$bottlesClass = "text-success";
						} else {
							$bottlesClass = "";
						}
						
						$output  = "<tr>";
						$output .= "<th scope=\"row\"><a href=\"index.php?n=wine_bin&bin_uid=" . $bin->uid . "\">" . $bin->name . "</a></th>";
						$output .= "<td>";
						$output .= "<div>";
						$output .= "<h5 class=\"text-truncate mb-1\">" . $item->name . "</h5>";
						$output .= "<p class=\"text-muted mb-0\">" . $item->grape . "</p>";
						$output .= "</div>";
						$output .= "</td>";
						$output .= "<td>" . currencyDisplay($transaction->price_per_bottle) . "</td>";
						$output .= "<td class=\"" . $bottlesClass . "\">" . abs($transaction->bottles) . "</td>";
						$output .= "<td class=\"text-end\">" . currencyDisplay($transaction->price_per_bottle * abs($transaction->bottles)) . "</td>";
						$output .= "</tr>";
						$output .= "";
						$output .= "";
						$output .= "";
						$output .= "";
						$output .= "";
						$output .= "";
						$output .= "";
						
						echo $output;
						?>
						
						<tr>
							<th scope="row" colspan="4" class="border-0 text-end">Total</th>
							<td class="border-0 text-end">
								<h4 class="m-0 fw-semibold"><?php echo currencyDisplay($transaction->price_per_bottle * abs($transaction->bottles)); ?></h4>
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
