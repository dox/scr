<?php
include_once("../inc/autoload.php");

header('Content-Type: application/json');

if (checkpoint_charlie("wine")) {
	$wineClass = new wineClass();
	
	$data = array();
	
	if (isset($_GET['q'])) {
		
		$searchTerm = htmlspecialchars($_GET['q']);
		$cellarUID = null;
		$closed = false;
		if (isset($_GET['c'])) {
			$cellarUID = filter_var($_GET['c'], FILTER_VALIDATE_INT);
		}
		if (isset($_GET['include']) && $_GET['include'] == "true") {
			$closed = true;
		}
		
		$resultsLimit = 4;
		
		$wines = $wineClass->weightedSearch($searchTerm, $cellarUID, $closed);
		
		foreach ($wines AS $wine) {
			$wine = new wine($wine['uid']);
			
			$data[] = array(
				"uid" => $wine->uid,
				"name" => html_entity_decode($wine->clean_name(true) . " (Qty. " . $wine->currentQty() . ")", ENT_QUOTES | ENT_HTML5)
			);
		}
		
		echo json_encode(['data' => $data]);
	}
}
?>