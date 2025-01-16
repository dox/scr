<?php
include_once("../inc/autoload.php");
printArray($_POST);
echo "test";

if (checkpoint_charlie("wine")) {
	$wineClass = new wineClass();
	$transaction = new transaction();
	
	if (is_array($_POST['wine_uid'])) {
		$linkedID = uniqid();
		for ($i = 0; $i < count($_POST['wine_uid']); $i++) {
			$wine = new wine($_POST['wine_uid'][$i]);
			
			$post = [
				'wine_uid' => $_POST['wine_uid'][$i],
				'bottles' => $_POST['bottles'][$i],
				'price_per_bottle' => $_POST['price_per_bottle'][$i],
				'date_posted' => $_POST['date_posted'],
				'name' => $_POST['name'],
				'type' => 'Transaction',
				'description' => $_POST['description'],
				'linked' => $linkedID
			];
			
			$transaction->create($post);
		}
	} else {
		$wineUID = filter_var($_POST['wine_uid'], FILTER_SANITIZE_NUMBER_INT);
		$wine = new wine($wineUID);
		
		$transaction->create($_POST);
		
		// work out if this is an import, or a deduction
		$transactionTypes = $wineClass->transactionsTypes();
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
}
?>