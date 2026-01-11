<?php
$user->pageCheck('wine');

$wines = new Wines();
$memberLists = $wines->lists([
	'member_ldap' => ['=', $user->getUsername()]
]);
$publicLists = $wines->lists([
	'member_ldap' => ['!=', $user->getUsername()],
	'type' => ['=', 'public']
]);

echo pageTitle(
	'Wine Favourites/Lists',
	'Functionality coming soon...',
	[
		[
			'permission' => 'wine',
			'title' => 'Edit List',
			'class' => '',
			'event' => '',
			'icon' => 'pencil',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#editBinModal'
			]
		]
	]
);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Lists</li>
	</ol>
</nav>

<hr/>

<h2>My Lists</h2>
<?php
echo '<ul class="list-group mb-4">';
foreach ($memberLists as $list) {
	echo $list->listItem();
}
echo '</ul>';
?>

<h2>Public Lists</h2>
<?php
echo '<ul class="list-group">';
foreach ($publicLists as $list) {
	echo $list->listItem();
}
echo '</ul>';
?>
