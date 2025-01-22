<?php
pageAccessCheck("wine");

$wineClass = new wineClass();

$title = "Transactions";
$subtitle = "All transactions";
$icons = null;

echo makeTitle($title, $subtitle, $icons, true);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Transactions</li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row">
	<div class="col">
		<?php
		  $transaction = new transaction();
		  
		  echo $transaction->transactionsTable($wineClass->allTransactions());
		  ?>
	</div>
</div>