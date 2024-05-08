<?php
pageAccessCheck("wine");

$cleanCellarUID = filter_var($_GET['cellar_uid'], FILTER_SANITIZE_NUMBER_INT);
//$cleanBin = filter_var($_GET['bin'], FILTER_SANITIZE_STRING);
$cleanBin = $_GET['bin'];

$cellar = new cellar($cleanCellarUID);
$wines = $cellar->getAllWinesByBin($cleanBin);

$title = $cleanBin . " Wine Bin";
$subtitle = "BETA FEATURE!";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
echo makeTitle($title, $subtitle, $icons);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item" aria-current="page"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid; ?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item active" aria-current="page"><?php echo $cleanBin; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
foreach ($cellar->getAllWinesByBin($cleanBin) AS $wine) {
	$wine = new wine($wine['uid']);
	
	echo $wine->binCard();
}
	?>
</div>

