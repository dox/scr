<?php
include_once("../../inc/autoload.php");

$cellarUID = filter_var($_GET['cellar_uid'], FILTER_SANITIZE_NUMBER_INT);
$category = filter_var($_GET['category'], FILTER_SANITIZE_SPECIAL_CHARS);

$cellar = new cellar($cellarUID);

$bins = $cellar->allBins(array('category' => $category));

if (count($bins) > 0) {
	echo $cellar->binsTable($bins);
} else {
	echo "0 bins listed for " . $category;
}

?>