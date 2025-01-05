<?php
include_once("../inc/autoload.php");

$wineUID = filter_var($_POST['wine_uid'], FILTER_SANITIZE_NUMBER_INT);
$listUID = filter_var($_POST['list_uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($wineUID);
$list = new wine_list($listUID);

$wineUIDs = $list->wineUIDs();

if (checkpoint_charlie("wine")) {
	// check if wine already in list
	if ($list->isWineInList($wine->uid)) {
		unset($wineUIDs[array_search($wine->uid, $wineUIDs)]);
		
		$updateArray = array("wine_uids" => $wineUIDs);
		$list->update($updateArray);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Removed [wineUID:" . $wine->uid . "] from [listUID:" . $list->uid . "] (" . $list->name . ")";
		$logsClass->create($logArray);
	} else {
		$wineUIDs = array_unique($wineUIDs);
		$wineUIDs[] = $wine->uid;
		
		$updateArray = array("wine_uids" => $wineUIDs);
		$list->update($updateArray);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Added [wineUID:" . $wine->uid . "] to [listUID:" . $listUID . "] (" . $list->name . ")";
		$logsClass->create($logArray);
	}
} else {
  $logArray['category'] = "wine";
  $logArray['result'] = "danger";
  $logArray['description'] = "Something went wrong attempting to add/remove [wineUID:" . $wine->uid . "] to [listUID:" . $list->uid . "] (" . $list->name . ")";
  $logsClass->create($logArray);
}
?>