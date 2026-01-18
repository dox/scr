<?php
require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	die("User not logged in.");
}
$mealUID = filter_input(INPUT_GET, 'mealUID', FILTER_VALIDATE_INT);
$meal = new Meal($mealUID);
?>

<div class="modal-body">
	<h3 class="text-center"><?= $meal->name ?></h3>
	<h5 class="text-secondary text-center mb-3">
		<i>
		<?= $meal->location ?>,
		<?= formatDate($meal->date_meal, 'long') ?>
		<?= formatTime($meal->date_meal) ?>
		</i>
	</h5>
	
	<ul class="nav nav-tabs nav-fill" id="mealTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu-tab-pane" type="button" role="tab" aria-controls="menu-tab-pane" aria-selected="true">Menu</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="diners-tab" data-bs-toggle="tab" data-bs-target="#diners-tab-pane" type="button" role="tab" aria-controls="diners-tab-pane" aria-selected="false">Diners <span class="badge rounded-pill text-bg-secondary"><?= $meal->totalDiners() ?></span></button>
		</li>
	</ul>
	
	<div class="tab-content pt-3" id="mealTabContent">
		<div class="tab-pane fade show active" id="menu-tab-pane" role="tabpanel" aria-labelledby="menu-tab" tabindex="0">
			
			<div class="text-center my-3">
				<?= !empty($meal->menu) ? nl2br($meal->menu) : "Menu not available" ?>
			</div>
		</div>
		
		<div class="tab-pane fade" id="diners-tab-pane" role="tabpanel" aria-labelledby="diners-tab" tabindex="0">
			<?php
			echo $meal->dinersList();
			?>
		</div>
	</div>
	
	<?php
	$features = [];
	
	// Prepare visible features
	if ($meal->charge_to == "Domus") {
		$features[] = 'Domus';
	}
	if ($meal->allowed_wine == 1) {
		$features[] = 'Wine';
	}
	if ($meal->allowed_dessert == 1) {
		$features[] = 'Dessert';
	}
	?>
	
	<?php if (!empty($features)): ?>
	<div class="border-top pt-3 mt-4 small">
		<div class="d-flex flex-wrap justify-content-center gap-2">
			<h3>
			<?php foreach ($features as $feature): ?>
				<span class="badge badge-lg bg-secondary text-uppercase fw-semibold"><i class="bi bi-check2-circle me-2"></i> <?= $feature ?></span>
			<?php endforeach; ?>
			</h3>
		</div>
	</div>
	<?php endif; ?>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
</div>
