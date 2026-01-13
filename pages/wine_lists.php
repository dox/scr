<?php
$user->pageCheck('wine');

$wines = new Wines();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['deleteListUID'])) {
		$listUID = filter_input(INPUT_POST, 'deleteListUID', FILTER_SANITIZE_NUMBER_INT);
		
		$list = new WineList($listUID);
		$list->delete();
	} elseif(isset($_POST['createList'])) {
		unset($_POST['createList']);
		$list = new WineList();
		$_POST['member_ldap'] = $user->getUsername();
		$list->create($_POST);
	}
}

$memberLists = $wines->lists([
	'member_ldap' => ['=', $user->getUsername()]
]);
$publicLists = $wines->lists([
	'member_ldap' => ['!=', $user->getUsername()],
	'type' => ['=', 'public']
]);

echo pageTitle(
	'Wine Favourites/Lists',
	'Add/Edit/Delete wine lists.',
	[
		[
			'permission' => 'wine',
			'title' => 'Add List',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addListModal'
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
	echo $list->listItemEdit();
}
echo '</ul>';
?>

<h2>Public Lists</h2>
<?php
echo '<ul class="list-group">';
foreach ($publicLists as $list) {
	echo $list->listItemEdit();
}
echo '</ul>';
?>

<!-- Add Modal -->
<div class="modal fade" id="addListModal" tabindex="-1" aria-hidden="true">
	<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Wine List</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<!-- List Name -->
					<div class="mb-3">
					  <label for="listName" class="form-label">List Name</label>
					  <input
						type="text"
						class="form-control"
						id="listName"
						name="name"
						placeholder="e.g. My Favourites"
						required
					  >
					</div>
					
					<!-- Wine type -->
					<div class="mb-3">
					  <label for="wineType" class="form-label">Visibility</label>
					  <select
						class="form-select"
						id="wineType"
						name="type"
						required
					  >
						<option value="private" selected>Private</option>
						<option value="public">Public</option>
					  </select>
					  <div class="form-text">
						Public wines are visible to everyone; private wines are for your use only.
					  </div>
					</div>
					
					<!-- Notes -->
					<div class="mb-4">
					  <label for="wineNotes" class="form-label">Notes</label>
					  <textarea
						class="form-control"
						id="wineNotes"
						name="notes"
						rows="3"
					  ></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Add List</button>
				</div>
				<input type="hidden" name="createList">
			</div>
		</div>
	</form>
</div>
