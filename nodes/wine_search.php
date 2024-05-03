<?php
$searchArray['code'] = filter_var($_GET['code'], FILTER_SANITIZE_STRING);

$wineClass = new wineClass();
$wines = $wineClass->searchAllWines($searchArray);

$title = "Wine Search";
$subtitle = "BETA FEATURE!";
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Cellar", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
echo makeTitle($title, $subtitle, $icons);


?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active" aria-current="page">Search</li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php

foreach ($wines AS $wine) {
	$wine = new wine($wine['bin']);
	
	echo $wine->binCard();
}

?>
</div>

