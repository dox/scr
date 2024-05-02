<?php
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$bin = new bin($cleanUID);
$cellar = new cellar($bin->cellar_uid);

$title = $bin->name . " Bin";
$subtitle = "BETA FEATURE!";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
echo makeTitle($title, $subtitle, $icons);


?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item active" aria-current="page"><?php echo $bin->name; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
foreach ($bin->getWines() AS $wine) {
	$wine = new wine($wine['uid']);
	
	$output  = "<div class=\"col\">";
	$output .= "<div class=\"card shadow-sm\">";
	$output .= "<div class=\"card-body\">";
	$output .= "<h5 class=\"card-title\">" . $wine->name . "</h5>";
	$output .= "<p class=\"card-text\">" . $wine->notes . "</p>";
	$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
	$output .= "<div class=\"btn-group\">";
	$output .= "<a href=\"index.php?n=wine_wine&uid=" . $wine->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
	$output .= "<a href=\"index.php?n=wine_cellars\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">Edit</a>";
	$output .= "</div>";
	$output .= "<small class=\"text-body-secondary\">" . $wine->qty  . autoPluralise(" bottle", " bottles", $wine->qty). "</small>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	
	echo $output;
}

?>
</div>

