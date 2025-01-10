<?php
include_once("../../inc/autoload.php");

$cellarUID = filter_var($_GET['cellar_uid'], FILTER_SANITIZE_NUMBER_INT);
$category = filter_var($_GET['category'], FILTER_SANITIZE_SPECIAL_CHARS);

$cellar = new cellar($cellarUID);

$bins = $cellar->allBins(array('category' => $category));

echo $cellar->binsTable($bins);
?>