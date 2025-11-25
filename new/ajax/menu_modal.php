<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}
$mealUID = filter_input(INPUT_GET, 'mealUID', FILTER_VALIDATE_INT);
$mealObject = new Meal($mealUID);
?>

<h3 class="modal-title mt-3 text-center"><?= $mealObject->type ?> Menu</h3>

<div class="modal-body">
	<p class="text-center">
		<i>
		<?= $mealObject->location ?>,
		<?= formatDate($mealObject->date_meal, 'long') ?>
		<?= formatTime($mealObject->date_meal) ?>
		</i>
	</p>
	
	<div class="text-center">
		<?= !empty($mealObject->menu)
		  ? nl2br($mealObject->menu)
		  : "Menu not available"
		?>
	</div>
	
	<?php if ($mealObject->domus || $mealObject->allowed_wine || $mealObject->allowed_dessert): ?>
	<hr>
	<?php endif; ?>
	
	<div class="row">
		<div class="col">
			<?php if ($mealObject->domus): ?>
			<p style="font-size: 1em"><i class="bi bi-mortarboard" style="font-size: 2em"></i></p>
			<p>Meal is Domus</p>
			<?php endif; ?>
		</div>
		<div class="col">
			<?php if ($mealObject->allowed_wine): ?>
			<p><svg width="2em" height="2em"><use xlink:href="assets/images/icons.svg#wine-glass"/></svg></p>
			<p>Wine available</p>
			<?php endif; ?>
		</div>
		<div class="col">
			<?php if ($mealObject->allowed_dessert): ?>
			<p style="font-size: 1em"><i class="bi bi-cookie" style="font-size: 2em"></i></p>
			<p>Dessert available</p>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
