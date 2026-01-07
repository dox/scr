<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}

$wines = new Wines();
$currentWineUid = $_GET['wine_uid'] ?? null;

$memberLists = $wines->lists([
	'member_ldap' => ['=', $user->getUsername()]
]);
$publicLists = $wines->lists([
	'member_ldap' => ['!=', $user->getUsername()],
	'type' => ['=', 'public']
]);
?>

<div class="modal-body">
	<h2>My Lists</h2>
	<?php
	echo '<ul class="list-group mb-4">';
	foreach ($memberLists as $list) {
		echo $list->listItem($currentWineUid);
	}
	echo '</ul>';
	?>
	
	<h2>Public Lists</h2>
	<?php
	echo '<ul class="list-group">';
	foreach ($publicLists as $list) {
		echo $list->listItem($currentWineUid);
	}
	echo '</ul>';
	?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
</div>
