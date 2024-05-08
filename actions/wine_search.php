<?php
include_once("../inc/autoload.php");

if (checkpoint_charlie("wine")) {
	$personSearchArray = array();
	
	$personsClass = new wineClass();
	
	$persons = $personsClass->getAllWines();
	
	foreach ($persons AS $person) {
		$personArray = array();
		$personArray['uid'] = $person['uid'];
		$personArray['name'] = $person['name'];
		
		$personSearchArray[$person['uid']] = $personArray;
	}
	
	$uniquePersons = array();
	
	foreach($personSearchArray as $personSearchResult) {
		$needle = $personSearchResult['uid'];
		if(array_key_exists($needle, $uniquePersons)) continue;
		$uniquePersons[] = $personSearchResult;
	  }
	
	echo json_encode($uniquePersons);
}
?>