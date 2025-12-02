<?php
$user->pageCheck('wine');

$wines = new Wines();
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
$bin = new Bin($cleanUID);
$cellar = new Cellar($bin->cellar_uid);

echo pageTitle(
	$bin->name,
	$bin->category,
	[
		[
			'permission' => 'wine',
			'title' => 'Edit Bin',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addCellarModal'
			]
		],
		[
			'permission' => 'wine',
			'title' => 'Add Wine',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteTermModal'
			]
		],
		[
			'permission' => 'wine',
			'title' => 'Delete Bin',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteTermModal'
			]
		]
	]
);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?page=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $bin->name; ?></li>
	</ol>
</nav>

<hr/>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 mb-3">
	<?php
	$closedWines = [];
	foreach ($bin->wines() as $wine) {
		if ($wine->status != "Closed") {
			echo "<div class=\"col\">";
			echo $wine->card();
			echo "</div>";
		} else {
			$closedWines[] = $wine;
		}
	}
	?>
</div>

<?php
if (!empty($closedWines)) {
	echo "<h4>Closed Wines</h4>";
	echo "<div class=\"row row-cols-1 row-cols-sm-2 row-cols-md-3\">";
		foreach ($closedWines as $wine) {
			echo "<div class=\"col\">";
			echo $wine->card();
			echo "</div>";
		}
	echo "</div>";
}
?>
