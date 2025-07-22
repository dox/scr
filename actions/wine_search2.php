<?php
include_once("../inc/autoload.php");

$whereParts = [];

foreach ($_POST['conditions'] as $cond) {
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
echo $sql;

$wines = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);


echo "<div class=\"row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3\">";
foreach ($wines AS $wine) {
	$wine = new wine($wine['uid']);
	
	echo $wine->card();
}
echo "</div>";