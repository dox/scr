<?php
include_once("../inc/autoload.php");

header('Content-Type: application/json');

if (checkpoint_charlie("wine")) {
	$wineClass = new wineClass();
	
	$data = array();
	
	if (isset($_GET['q'])) {
		$queryArray["name"] = $_GET['q'];
		
		$resultsLimit = 4;
		
		$wines = $wineClass->searchAllWines($queryArray, $resultsLimit);
		
		foreach ($wines AS $wine) {
			$data[] = array("uid" => $wine['uid'], "name" => $wine['name']);
		}
		
		echo json_encode(['data' => $data]);
	}
}
?>