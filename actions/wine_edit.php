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
}

//die("DIE");

$logArray['category'] = "wine";
$logArray['result'] = "warning";
$logArray['description'] = "Attempted to add/edit wine with " . count($_POST) . " fields";
$logsClass->create($logArray);
?>