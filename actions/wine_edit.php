<?php
include_once("../inc/autoload.php");

if (isset($_POST['uid'])) {
	$wineUID = filter_var($_POST['uid'], FILTER_SANITIZE_NUMBER_INT);
	
	$wine = new wine($wineUID);
	
	$wine->update($_POST);
} else {
	// creating new wine
	echo "creating new wine ";
	
	$wine = new wine();
	
	$wine->create($_POST);
}

die("DIE");


$logArray['category'] = "wine";
$logArray['result'] = "warning";
$logArray['description'] = "Attempted to add/edit wine with " . count($_POST) . " fields";
$logsClass->create($logArray);



if (checkpoint_charlie("wine")) {
	foreach (explode(",", $list->wine_uids) AS $wine_uid) {
		$array[] = $wine_uid;
	}
	
	// check if wine already in list
	if (in_array($wineUID, $array)) {
		if (($key = array_search($wineUID, $array)) !== false) {
			unset($array[$key]);
		}
		$list->updateList($array);
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Removed [wineUID:" . $wineUID . "] from [listUID:" . $listUID . "] (" . $list->name . ")";
		$logsClass->create($logArray);
	} else {
		$array[] = $wineUID;
		
		$array = array_unique($array);
		$array = array_filter($array);
		
		$list->updateList($array);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Added [wineUID:" . $wineUID . "] to [listUID:" . $listUID . "] (" . $list->name . ")";
		$logsClass->create($logArray);
	}
	
} else {
  $logArray['category'] = "wine";
  $logArray['result'] = "danger";
  $logArray['description'] = "Something went wrong attempting to add/remove [wineUID:" . $wineUID . "] to [listUID:" . $listUID . "] (" . $list->name . ")";
  $logsClass->create($logArray);
}
?>