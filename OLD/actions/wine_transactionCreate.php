<?php
include_once("../inc/autoload.php");

if (checkpoint_charlie("wine")) {
	$transaction = new transaction();
	
	if (!isset($_POST['wine_uid'])) {
		die("No wineUID specified");
	}
	
	if (is_array($_POST['wine_uid'])) {
		$linkedID = uniqid();
		
		for ($i = 0; $i < count($_POST['wine_uid']); $i++) {
			$wineUID = filter_var($_POST['wine_uid'][$i], FILTER_SANITIZE_NUMBER_INT);
			$wine = new wine($wineUID);
			
			$post = [
				'wine_uid' => $wine->uid,
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
	}
}
?>