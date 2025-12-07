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
	<hr>
	<?php endif; ?>
	
	<div class="row text-center">
		<div class="col">
			<div class="card mb-3">
				<?php $class = ($mealObject->domus == 1) ? "" : " text-muted"; ?>
				<div class="card-body <?= $class; ?>">
					<h5 class="card-title text-truncate">Domus</h5>
					<h1><i class="bi bi-mortarboard" style="font-size: 2rem;"></i></h1>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-3">
				<?php $class = ($mealObject->allowed_wine == 1) ? "" : " text-muted"; ?>
				<div class="card-body <?= $class; ?>">
					<h5 class="card-title text-truncate">Wine</h5>
					<h1><i class="bi bi-cup-straw" style="font-size: 2rem;"></i></h1>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-3">
				<?php $class = ($mealObject->allowed_dessert == 1) ? "" : " text-muted"; ?>
				<div class="card-body <?= $class; ?>">
					<h5 class="card-title text-truncate">Dessert</h5>
					<h1><i class="bi bi-cookie" style="font-size: 2rem;"></i></h1>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
