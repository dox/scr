<?php
$user->pageCheck('wine');

$listUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$list = new WineList($listUID);

if (!$user->hasPermission('global_admin')) {
	// Not a global_admin → check private list ownership
	if ($list->type === 'private' && $list->member_ldap !== $user->getUsername()) {
		// Block access
		require_once "404.php";
		die("Unknown or unavailable wine list");
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$list->update($_POST);
	$list = new WineList($listUID);
}

echo pageTitle(
	'Wine List: ' . $list->name,
	'Notes: ' . $list->notes,
	[
		[
			'permission' => 'wine',
			'title' => 'Delete List',
			'class' => 'text-danger',
			'event' => '',
			'icon' => 'trash',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteListModal'
			]
		]
	]
);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?page=wine_lists">Lists</a></li>
		<li class="breadcrumb-item active">List Edit</li>
	</ol>
</nav>

<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
	<!-- List Name -->
	<div class="mb-3">
	  <label for="listName" class="form-label">List Name</label>
	  <input
		type="text"
		class="form-control"
		id="listName"
		name="name"
		value="<?= htmlspecialchars($list->name) ?>"
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
		<option value="private" <?= ($list->type === 'private') ? ' selected' : '' ?>>Private</option>
		<option value="public" <?= ($list->type === 'public') ? ' selected' : '' ?>>Public</option>
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
	  ><?= htmlspecialchars($list->notes ?? '', ENT_QUOTES) ?></textarea>
	</div>
	
	<button type="submit" class="btn btn-primary w-100 mb-3">Update List</button>
</form>

<h2>Wines in List</h2>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 mb-3">
	<?php
	foreach ($list->wines() as $wine) {
		echo "<div class=\"col\">";
		echo $wine->card();
		echo "</div>";
	}
	?>
</div>

<!-- Delete List Modal -->
<div class="modal fade" tabindex="-1" id="deleteListModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<form method="post" action="index.php?page=wine_lists">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Delete List</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><span class="text-danger"><strong>WARNING!</strong></span> Are you sure you want to delete this list?</p>
				<p>This will not delete any wines.</p>
				<p><span class="text-danger"><strong>THIS ACTION CANNOT BE UNDONE!</strong></span></p>
				<input type="text" class="form-control mb-3"
				placeholder="Type 'DELETE' to confirm"
				id="delete_confirm"
				oninput="enableOnExactMatch('delete_confirm', 'delete_button', 'DELETE')">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger" id="delete_button" disabled>Delete List</button>
				<input type="hidden" name="deleteListUID" value="<?= $list->uid; ?>">
			</div>
		</div>
	</div>
	</form>
</div>