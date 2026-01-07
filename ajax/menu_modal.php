<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}
$mealUID = filter_input(INPUT_GET, 'mealUID', FILTER_VALIDATE_INT);
$mealObject = new Meal($mealUID);
?>

<div class="modal-body">
	<h3 class="text-center"><?= $mealObject->name ?></h3>
	<h5 class="text-secondary text-center mb-3">
		<i>
		<?= $mealObject->location ?>,
		<?= formatDate($mealObject->date_meal, 'long') ?>
		<?= formatTime($mealObject->date_meal) ?>
		</i>
	</h5>
	
	<div class="text-center my-3">
		<?= !empty($mealObject->menu)
		  ? nl2br($mealObject->menu)
		  : "Menu not available"
		?>
	</div>
	
	<?php if ($mealObject->domus || $mealObject->allowed_wine || $mealObject->allowed_dessert): ?>
	<div class="d-flex justify-content-around align-items-center border-top pt-3 mt-4 small">
		<div class="text-center <?= ($mealObject->charge_to == "Domus") ? "" : "opacity-0" ?>">
			<div><i class="bi bi-mortarboard fs-4"></i></div>
			<div class="text-uppercase fw-semibold">Domus</div>
		</div>
	
		<div class="text-center <?= ($mealObject->allowed_wine == 1) ? "" : "opacity-0" ?>">
			<div><svg class="bi fs-4" width="1em" height="1em" aria-hidden="true"><use xlink:href="assets/images/icons.svg#wine-glass"></use></svg></div>
			<div class="text-uppercase fw-semibold">Wine</div>
		</div>
	
		<div class="text-center <?= ($mealObject->allowed_dessert == 1) ? "" : "opacity-0" ?>">
			<div><i class="bi bi-cookie fs-4"></i></div>
			<div class="text-uppercase fw-semibold">Dessert</div>
		</div>
	</div>
	<?php endif; ?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
</div>
