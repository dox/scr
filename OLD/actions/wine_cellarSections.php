<?php
include_once("../inc/autoload.php");

header('Content-Type: application/json');

$wineClass = new wineClass();

$cellarUID = filter_var(isset($_GET['c']) ? intval($_GET['c']) : 0, FILTER_VALIDATE_INT);

foreach ($wineClass->allCellars() AS $cellar) {
	$cellar = new cellar($cellar['uid']);
	
	foreach ($cellar->binTypes() AS $binType) {
		$sections[$cellar->uid][] = array('name' => $binType);
	}
}

// Return JSON response
echo json_encode($sections[$cellarUID] ?? []);
?>