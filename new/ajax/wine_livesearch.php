<?php
require_once '../inc/autoload.php';

if (!$user->hasPermission("wine")) {
	die("Permission denied.");
}

$response = [
	"success" => false,
	"data"    => []
];

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$cellarUID = isset($_GET['cellar_uid']) ? intval($_GET['cellar_uid']) : null;

if ($q === '') {
	echo json_encode($response);
	exit;
}

$sql = "SELECT wine_wines.uid, wine_wines.name 
		FROM wine_wines 
		LEFT JOIN wine_bins ON wine_wines.bin_uid = wine_bins.uid
		WHERE wine_wines.status <> 'Closed' AND
		(
			wine_wines.name LIKE ?
			OR wine_wines.grape LIKE ?
			OR wine_wines.region_of_origin LIKE ?
			OR wine_wines.country_of_origin LIKE ?
		)
		";

$params = ["%$q%", "%$q%", "%$q%", "%$q%"];

if ($cellarUID !== null) {
	$sql .= " AND wine_bins.cellar_uid = ?";
	$params[] = $cellarUID;
}

$sql .= " LIMIT 8";

// Assume $db->fetchAll returns array of rows
$rows = $db->fetchAll($sql, $params);

$wineSearchResults = [];

foreach ($rows as $row) {
	$wine = new Wine($row['uid']);
	
	$wineSearchResults[] = [
		'uid' => $wine->uid,
		'name' => $wine->clean_name(true) . " (Qty. " . $wine->currentQty() . ")"
	];
}

$response["success"] = true;
$response["data"] = $wineSearchResults;

echo json_encode($response);
