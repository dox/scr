<?php
pageAccessCheck("wine");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$transaction = new wine_transactions;
$transaction = $transaction->getTransaction($_GET['uid']);

$title = "Wine Invoice: " . $transaction['uid'];
//$subtitle = $wine->grape . ", " . $wine->country_of_origin;
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#favListModal\"");
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Edit Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=edit&uid=" . $wine->uid . "'\"");
echo makeTitle($title, $subtitle, $icons);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_posting">Posting</a></li>
		<li class="breadcrumb-item active">Invoice</li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="card">
	<div class="card-body">
		<div class="invoice-title">
			<h4 class="float-end">Invoice #<?php echo $transaction['uid']; ?> <span class="badge bg-success ms-2">Posted</span></h4>
			<div class="mb-4">
				<h2 class="mb-1 text-muted"><?php echo site_name; ?></h2>
			</div>
			<div class="text-muted">
				<p class="mb-1">Posted by: <?php echo $transaction['username']; ?></p>
			</div>
		</div>
		
		<hr class="my-4">
		
		<div class="row">
			<div class="col-sm-6">
				<div class="text-muted">
					<h5 class="mb-3">Billed To:</h5>
					<h5 class="mb-3"><?php echo $transaction['customer']; ?></h5>
					<?php if (!empty($transaction['notes'])) {
						echo "<p>" . $transaction['notes'] . "</p>";
					}
					?>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="text-muted text-sm-end">
					<div class="mt-4">
						<h5 class="font-size-15 mb-1">Invoice Date:</h5>
						<p><?php echo dateDisplay($transaction['date'], true); ?></p>
					</div>
					<?php if (!empty($transaction['reference'])) {
						echo "<div class=\"mt-4\">";
						echo "<h5 class=\"mb-1\">Customer Reference No:</h5>";
						echo "<p>" . $transaction['reference'] . "</p>";
						echo "</div>";
					}
					?>
				</div>
			</div>
		</div>
		<div class="py-2">
			<h5 class="font-size-15">Order Summary</h5>
			<div class="table-responsive">
				<table class="table align-middle table-nowrap table-centered mb-0">
					<thead>
						<tr>
							<th style="width: 70px;">No.</th>
							<th>Item</th>
							<th>Price</th>
							<th>Qty.</th>
							<th class="text-end" style="width: 120px;">Total</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$items = json_decode($transaction['contents']);
						
						$i = 1;
						foreach ($items AS $item) {
							$wine = new wine($item->uid);
							
							$total = $item->price * $item->qty;
							$runningTotal = $runningTotal + $total;
							
							$output  = "<tr>";
							$output .= "<th scope=\"row\">" . $i . "</th>";
							$output .= "<td>";
							$output .= "<div>";
							$output .= "<h5 class=\"text-truncate mb-1\">" . $item->name . "</h5>";
							$output .= "<p class=\"text-muted mb-0\">" . $item->grape . "</p>";
							$output .= "</div>";
							$output .= "</td>";
							$output .= "<td>" . currencyDisplay($item->price) . "</td>";
							$output .= "<td>" . $item->qty . "</td>";
							$output .= "<td class=\"text-end\">" . currencyDisplay($total) . "</td>";
							$output .= "</tr>";
							$output .= "";
							$output .= "";
							$output .= "";
							$output .= "";
							$output .= "";
							$output .= "";
							$output .= "";
							
							echo $output;
							
							$i++;
						}
						?>
						
						<tr>
							<th scope="row" colspan="4" class="border-0 text-end">Total ex VAT</th>
							<td class="border-0 text-end">
								<h4 class="m-0 fw-semibold"><?php echo currencyDisplay($runningTotal); ?></h4>
							</td>
						</tr>
						
						<tr>
							<?php
							$vat_rate = $transaction['vat_rate'];
							$vat_total = $runningTotal * ($vat_rate/100);
							$incVATtotal = $runningTotal + $vat_total;
							?>
							<th scope="row" colspan="4" class="border-0 text-end">VAT (<?php echo $vat_rate;?>%)</th>
							<td class="border-0 text-end">
								<h4 class="m-0 fw-semibold"><?php echo currencyDisplay($vat_total); ?></h4>
							</td>
						</tr>
						
						<tr>
							<th scope="row" colspan="4" class="border-0 text-end">Total inc VAT</th>
							<td class="border-0 text-end">
								<h4 class="m-0 fw-semibold"><?php echo currencyDisplay($incVATtotal); ?></h4>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="d-print-none mt-4">
				<div class="float-end">
					<a href="javascript:window.print()" class="btn btn-info me-1">PRINT</a>
				</div>
			</div>
		</div>
	</div>
</div>



