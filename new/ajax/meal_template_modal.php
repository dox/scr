<?php
require_once '../inc/autoload.php';
$user->pageCheck('meals');

$mealUID = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$sourceMeal = new Meal($mealUID);
?>

<div class="modal-header">
	<h5 class="modal-title">Apply Meal Using Template</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form id="meal-template-form">
<div class="modal-body">
		<!-- Hidden source meal -->
		<input type="hidden" name="meal_uid" value="<?= $sourceMeal->uid ?>">
		
		<!-- Week selection -->
		<div class="mb-3">
			<label for="date_start" class="form-label">Week commencing</label>
			<div class="input-group" id="datetimepicker">
				<span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
				<input type="text" class="form-control" name="date_start" id="date_start" placeholder="" value="<?= $terms->currentTerm()->date_start; ?>"" required>
			</div>
			<small id="date_startHelp" class="form-text text-muted">Choose the Sunday of the week you want to apply this meal to.</small>
		</div>
		
		<div class="mb-3">
			<label for="week_count" class="form-label">Apply for how many weeks?</label>
			<select class="form-select" id="week_count" name="week_count">
				<option value="1">Just this week</option>
				<option value="2">2 weeks</option>
				<option value="3">3 weeks</option>
				<option value="4">4 weeks</option>
				<option value="5">5 weeks</option>
				<option value="6">6 weeks</option>
				<option value="7">7 weeks</option>
				<option value="8">8 weeks</option>
				<option value="9">9 weeks</option>
			</select>
		</div>
		
		
		<!-- Day selection -->
		<label for="days" class="form-label">Days to Apply</label>		
		<div class="mb-3">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="days[]" value="sunday" id="sun">
				<label class="form-check-label" for="sun">Sunday</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="days[]" value="monday" id="mon">
				<label class="form-check-label" for="mon">Monday</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="days[]" value="tuesday" id="tue">
				<label class="form-check-label" for="tue">Tuesday</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="days[]" value="wednesday" id="wed">
				<label class="form-check-label" for="wed">Wednesday</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="days[]" value="thursday" id="thu">
				<label class="form-check-label" for="thu">Thursday</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="days[]" value="friday" id="fri">
				<label class="form-check-label" for="fri">Friday</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="days[]" value="saturday" id="sat">
				<label class="form-check-label" for="sat">Saturday</label>
			</div>
		</div>
		
		
		<div class="form-text mt-3">
			The meal will be copied onto each selected day.
		</div>
		
		<div id="apply-result" class="alert mb-3 d-none" role="alert"></div>
</div>

<!-- Footer -->
<div class="modal-footer">
	<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
	<button
		type="button"
		class="btn btn-primary meal-template-apply-btn"
		data-meal_uid="<?= $sourceMeal->uid ?>">
		Apply Meal to Selected Dates
	</button>
</div>

</form>
