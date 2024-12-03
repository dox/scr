<?php
include_once("../inc/autoload.php");

$wineUID = filter_var($_POST['wine_uid'], FILTER_SANITIZE_NUMBER_INT);
$wine = new wine($wineUID);

if (checkpoint_charlie("wine")) {
	$transaction = new wine_transactions();
	$transaction->create($_POST);
	
	// work out if this is an import, or a deduction
	$transactionTypes = $wine->transactionsTypes();
	if ($transactionTypes[$_POST['type']] == "import") {
		$wine->import($_POST['bottles']);
	} elseif ($transactionTypes[$_POST['type']] == "deduct") {
		$wine->deduct($_POST['bottles']);
	} else {
		$logArray['category'] = "wine";
		$logArray['result'] = "danger";
		$logArray['description'] = "Attempted to update [wineUID:" . $wine->uid . "] but didn't know what qty bottles: " . $_POST['bottles'];
		$logsClass->create($logArray);
	}
}
?>