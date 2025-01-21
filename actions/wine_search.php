<?php
include_once("../inc/autoload.php");

header('Content-Type: application/json');

if (checkpoint_charlie("wine")) {
	$wineClass = new wineClass();
	
	$data = array();
	
	if (isset($_GET['q'])) {
		$queryArray["wine_wines.name"] = $_GET['q'];
		$queryArray["wine_wines.code"] = $_GET['q'];
		
		$cellarUID = null;
		if (isset($_GET['c'])) {
			$cellarUID = filter_var($_GET['c'], FILTER_VALIDATE_INT);
		}
		
		$resultsLimit = 4;
		
		// include closed wines or no
		if (isset($_GET['include']) && $_GET['include'] == "true") {
			$wines = $wineClass->searchAllWines($queryArray, $cellarUID, $resultsLimit, true);
		} else {
			$wines = $wineClass->searchAllWines($queryArray, $cellarUID, $resultsLimit);
		}
		
		foreach ($wines AS $wine) {
			$wine = new wine($wine['uid']);
			
			$data[] = array(
				"uid" => $wine->uid,
				"name" => $wine->clean_name(true)
			);
		}
		
		echo json_encode(['data' => $data]);
	}
}
?>