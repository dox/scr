<?php
pageAccessCheck("wine");

$searchFilter = filter_var($_GET['filter'], FILTER_SANITIZE_STRING);
$searchValue = filter_var($_GET['value'], FILTER_SANITIZE_STRING);

$wineClass = new wineClass();

if (isset($_GET['cellar_uid'])) {
	$cellarUID = filter_var($_GET['cellar_uid'], FILTER_SANITIZE_STRING);
	$filterArray['cellar_uid'] = $cellarUID;
}

if ($searchFilter == "list") {
	// Handle the special case for "list"
	$wineList = new wine_list($searchValue);

	if ($wineList->type == "private" && $wineList->member_ldap != $_SESSION['username']) {
		die("This list is private!");
	}

	$wines = $wineClass->winesByUIDs($wineList->wine_uids);
} else {
	// Define a mapping of $searchFilter to filter array keys
	$filterKeyMap = [
		"code" => "code",
		"vintage" => "vintage",
		"grape" => "grape",
		"country_of_origin" => "country_of_origin",
		"region_of_origin" => "region_of_origin",
		"category" => "wine_wines.category",
		"supplier" => "wine_wines.supplier",
		"price" => "wine_wines.price_purchase",
	];

	// Check if $searchFilter is recognized
	if (array_key_exists($searchFilter, $filterKeyMap)) {
		$filterArray[$filterKeyMap[$searchFilter]] = $searchValue;
		$wines = $wineClass->allWines($filterArray, true);
	} else {
		die("Search filter not recognised");
	}
}

$title = "Wine Search";
$subtitle = "Searching on '" . $searchFilter . "' for '" . $searchValue . "'";
if (isset($_GET['cellar_uid'])) {
	$cellar = new cellar($cellarUID);
	$subtitle .= " (and limited to " . $cellar->name . " cellar)";
}
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Cellar", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
echo makeTitle($title, $subtitle, $icons, true);


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
	$wine = new wine($wine['uid']);
	
	echo $wine->card();
}

?>
</div>

