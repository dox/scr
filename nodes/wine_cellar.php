<?php
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$cellar = new cellar($cleanUID);

$title = $cellar->name . " Wine Cellar";
$subtitle = "BETA FEATURE!";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#wine-bin\"/></svg> Add Bin", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
echo makeTitle($title, $subtitle, $icons);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?php echo $cellar->name; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
foreach ($cellar->getBins() AS $bin) {
	$bin = new bin($bin['uid']);
	$wines = $bin->getWines();
	
	if (count($wines) == 1) {
		$viewURL = "index.php?n=wine_wine&uid=" . $wines[0]['uid'];
	} else {
		$viewURL = "index.php?n=wine_bin&uid=" . $bin->uid;
	}
	
	$output  = "<div class=\"col\">";
	$output .= "<div class=\"card shadow-sm\">";
	$output .= "<div class=\"card-body\">";
	$output .= "<h5 class=\"card-title\">" . $bin->name . "</h5>";
	$output .= "<p class=\"card-text\">" . $bin->notes . "</p>";
	$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
	$output .= "<div class=\"btn-group\">";
	$output .= "<a href=\"" . $viewURL . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
	$output .= "<a href=\"index.php?n=wine_cellars\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">Edit</a>";
	$output .= "</div>";
	$output .= "<small class=\"text-body-secondary\">" . count($wines) . autoPluralise(" wine", " wines", count($wines)) . " </small>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	
	echo $output;
}
	?>
</div>

