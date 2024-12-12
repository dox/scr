<?php
include_once("../inc/autoload.php");

if (isset($_POST['uid'])) {
	$wineUID = filter_var($_POST['uid'], FILTER_SANITIZE_NUMBER_INT);
	
	$wine = new wine($wineUID);
	$wine->update($_POST);
	echo "UPDATE";
} else {
	// creating new wine
	$wine = new wine();
	$wine->create($_POST);
	echo "ADD";

}
?>