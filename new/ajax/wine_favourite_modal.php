<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}
$mealUID = filter_input(INPUT_GET, 'mealUID', FILTER_VALIDATE_INT);
$mealObject = new Meal($mealUID);
?>

<div class="modal-body">
	<p>Coming soon...</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
</div>
