<?php
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($cleanUID);
$bin = new bin($wine->bin_uid);
$cellar = new cellar($bin->cellar_uid);

$title = $wine->name . " Wine";
$subtitle = "BETA FEATURE!";
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Cellar", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
echo makeTitle($title, $subtitle, $icons);


?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_bin&uid=<?php echo $bin->uid?>"><?php echo $bin->name; ?></a></li>
		<li class="breadcrumb-item active" aria-current="page"><?php echo $wine->name; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?php echo $wine->qty; ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?php echo currencyDisplay($wine->purchase_price); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Purchase Price</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?php echo currencyDisplay($wine->sale_price); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Sale Price</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?php echo $wine->vintage; ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Vintage</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><a href="index.php?n=wine_search&code=<?php echo $wine->code; ?>"><?php echo $wine->code; ?></a></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Wine Code</h6>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xl-8">
		<div class="card mb-3">
			<div class="card-body">
				<?php
				printArray($wine);
				?>
			</div>
		</div>
		
		<div class="card mb-3">
			<div class="card-body">
				<?php echo $wine->tasting; ?>
			</div>
		</div>
		
		<div class="card mb-3">
			<div class="card-body">
				History
			</div>
		</div>
	</div>
	<div class="col-xl-4">
		<div class="card mb-3">
			<img src="<?php echo $wine->photograph; ?>" class="card-img-top" alt="...">
			<div class="card-body">
				Image
			</div>
		</div>
		
		<div class="card mb-3">
			<div id="map" class="card-img-top" style="height: 200px;" alt="World map"></div>
			<div class="card-body">
				Wine Origin
			</div>
		</div>
	</div>
</div>