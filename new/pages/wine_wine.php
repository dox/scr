<?php
$user->pageCheck('wine');

$wines = new Wines();
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
$wine = new Wine($cleanUID);
$bin = new Bin($wine->bin_uid);
$cellar = new Cellar($bin->cellar_uid);


$fields = ['grape', 'region_of_origin', 'category'];
$subtitleArray = [];

foreach ($fields as $field) {
	if (!empty($wine->$field)) {
		$value = htmlspecialchars($wine->$field, ENT_QUOTES, 'UTF-8');
		$urlValue = urlencode($wine->$field);
		$subtitleArray[] = "<a href=\"index.php?page=wine_search&filter={$field}&value={$urlValue}\">{$value}</a>";
	}
}

$subtitle = implode(", ", $subtitleArray);

echo pageTitle(
	$wine->clean_name(),
	implode(", ", $subtitleArray),
	[
		[
			'permission' => 'wine',
			'title' => 'Edit Wine',
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
			'title' => 'Add To List',
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
			'title' => 'Delete Wine',
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
			'title' => 'Add Transaction',
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
			'title' => 'Add Multi-Transaction',
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
		<li class="breadcrumb-item"><a href="index.php?page=wine_bin&uid=<?php echo $bin->uid?>"><?php echo $bin->name; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $wine->name; ?></li>
	</ol>
</nav>

<hr/>

<?php
printArray($wine);
?>