<?php
include_once("../inc/autoload.php");

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$whereParts = [];

foreach ($input['conditions'] as $cond) {
	$field = $cond['field'] ?? '';
	$operator = $cond['operator'] ?? '';
	$value = $cond['value'] ?? '';

	if ($field && $operator) {
		if ($operator === 'LIKE') {
			$whereParts[] = "$field LIKE '%" . addslashes($value) . "%'";
		} else {
			$whereParts[] = "$field $operator '" . addslashes($value) . "'";
		}
	}
}

$whereClause = '';
if (!empty($whereParts)) {
	$whereClause = 'WHERE ' . implode(' AND ', $whereParts);
}

$sql = "SELECT * FROM wine_wines $whereClause";

$wines = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($wines);