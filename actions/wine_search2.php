<?php
include_once("../inc/autoload.php");

$whereParts = [];

if (empty($_POST['conditions'])) {
	exit("No conditions supplied");
}

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

$sql = "SELECT wine_wines.uid AS uid FROM wine_wines ";
if (in_array('cellar_uid', array_column($_POST['conditions'], 'field')) || in_array('bin_uid', array_column($_POST['conditions'], 'field'))) {
	$sql .= "JOIN wine_bins ON wine_wines.bin_uid = wine_bins.uid ";
	$sql .= "JOIN wine_cellars ON wine_bins.cellar_uid = wine_cellars.uid ";
}
$sql .= $whereClause;

$wines = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);


echo "<div class=\"row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3\">";
foreach ($wines AS $wine) {
	$wine = new wine($wine['uid']);
	
	echo $wine->card();
}
echo "</div>";