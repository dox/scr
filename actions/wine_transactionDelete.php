<?php
include_once("../inc/autoload.php");

$transactionUID = filter_var($_POST['transaction_uid'], FILTER_SANITIZE_NUMBER_INT);

if (checkpoint_charlie("wine")) {
	$transaction = new transaction($transactionUID);
	
	$transaction->delete();
}
?>