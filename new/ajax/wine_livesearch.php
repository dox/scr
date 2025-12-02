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
$cellar = isset($_GET['cellar_uid']) ? intval($_GET['cellar_uid']) : null;

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
		)";

$params = ["%$q%", "%$q%", "%$q%", "%$q%"];

if ($cellar !== null) {
	$sql .= " AND wine_bins.cellar_uid = ?";
	$params[] = $cellar;
}

// Assume $db->fetchAll returns array of rows
$rows = $db->fetchAll($sql, $params);

$response["success"] = true;
$response["data"] = $rows;

echo json_encode($response);
