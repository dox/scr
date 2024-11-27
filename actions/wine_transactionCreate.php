<?php
include_once("../inc/autoload.php");

$wineUID = filter_var($_POST['wine_uid'], FILTER_SANITIZE_NUMBER_INT);
$wine = new wine($wineUID);

if (checkpoint_charlie("wine")) {
	$transaction = new wine_transactions();
	$transaction->create($_POST);
}
?>