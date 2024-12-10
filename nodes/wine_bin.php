<?php
pageAccessCheck("wine");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wineClass = new wineClass();
$bin = new bin($cleanUID);
$cellar = new cellar($bin->cellar_uid);

$title = $bin->name . " Bin";
$subtitle = $bin->category;

if (count($bin->currentWines()) == 1) {
	$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#heart-full\"/></svg> Add To List", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=add&cellar_uid=" . $cellar->uid . "'\"");
}
echo makeTitle($title, $subtitle, $icons);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $bin->name; ?></li>
	</ol>
</nav>

<?php
if (count($bin->currentWines()) > 1) {
	echo "Multiple wines";
	printArray($bin->currentWines());
	
} else {
	$tempWineUID = $bin->currentWines()[0]['uid'];
	require_once("nodes/widgets/_wine.php");
}
?>