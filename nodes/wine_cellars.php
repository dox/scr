

<?php
$title = "Wine Cellars";
$subtitle = "BETA FEATURE!";
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Cellar", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");

echo makeTitle($title, $subtitle, $icons);

$wineClass = new wineClass();
$cellars = $wineClass->getAllCellars();
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active" aria-current="page">Cellars</li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
foreach ($cellars AS $cellar) {
	$cellar = new cellar($cellar['uid']);
	
	$output  = "<div class=\"col\">";
	$output .= "<div class=\"card shadow-sm\">";
	$output .= "<img src=\"img/cards/formal.png\" class=\"card-img-top\" alt=\"Cellar photograph\">";
	$output .= "<div class=\"card-body\">";
	$output .= "<h5 class=\"card-title\">" . $cellar->name . "</h5>";
	$output .= "<p class=\"card-text\">" . $cellar->notes . "</p>";
	$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
	$output .= "<div class=\"btn-group\">";
	$output .= "<a href=\"index.php?n=wine_cellar&uid=" . $cellar->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
	$output .= "<a href=\"index.php?n=wine_cellars\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">Edit</a>";
	$output .= "</div>";
	$output .= "<small class=\"text-body-secondary\">" . count($cellar->getBins()) . autoPluralise(" bin", " bins", count($cellar->getBins())) . "</small>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	
	echo $output;
}
	?>
</div>

