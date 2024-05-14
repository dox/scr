<?php
include_once("../inc/autoload.php");

$wineUID = filter_var($_POST['wine_uid'], FILTER_SANITIZE_NUMBER_INT);
$listUID = filter_var($_POST['list_uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($wineUID);
$list = new wine_list($listUID);

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