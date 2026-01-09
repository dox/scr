<?php
$user->pageCheck('settings');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['uid'])) {
		$settings->update($_POST);
	} else {
		$settings->create($_POST);
		toast("Setting Created", "Setting sucessfully created", "text-success");
	}
}

echo pageTitle(
	"Site Settings",
	"Customise the behaviour, display and configuration of this site",
	[
		[
			'permission' => 'settings',
			'title' => 'Add new',
			'class' => '',
			'event' => '',
			'icon' => 'plus-circle',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#addSettingModal'
			]
		]
	]
);
?>

<div class="alert alert-danger text-center"><strong>Warning!</strong> Making changes to these settings can disrupt the running of this site.  Proceed with caution.</div>

<div class="accordion mb-3" id="accordionExample">
	<?php foreach ($settings->getAll() as $setting):
		$uid   = (int) $setting['uid'];
		$name  = htmlspecialchars($setting['name'], ENT_QUOTES, 'UTF-8');
		$desc  = htmlspecialchars($setting['description'], ENT_QUOTES, 'UTF-8');
		$type  = htmlspecialchars($setting['type'], ENT_QUOTES, 'UTF-8');
		$value = htmlspecialchars($setting['value'], ENT_QUOTES, 'UTF-8');
	
		$isActive      = isset($_GET['settingUID']) && $_GET['settingUID'] == $uid;
		$headingClass  = $isActive ? "accordion-button" : "accordion-button collapsed";
		$collapseClass = $isActive ? "accordion-collapse show" : "accordion-collapse collapse";
		$itemName      = "collapse-{$uid}";
		$url           = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
	?>
	
	<div class="accordion-item">
		<h2 class="accordion-header" id="heading-<?= $uid ?>">
			<button class="<?= $headingClass ?>" type="button"
					data-bs-toggle="collapse"
					data-bs-target="#<?= $itemName ?>"
					aria-expanded="<?= $isActive ? "true" : "false" ?>"
					aria-controls="<?= $itemName ?>">
				<strong><?= $name ?></strong> — <?= $desc ?>
				<span class="badge bg-secondary ms-2"><?= $type ?></span>
			</button>
		</h2>

		<div id="<?= $itemName ?>" class="<?= $collapseClass ?>"
			 aria-labelledby="heading-<?= $uid ?>" data-bs-parent="#accordionExample">

			<div class="accordion-body">
				<form method="post" action="<?= $url ?>" class="d-flex align-items-center gap-3">
					<?= renderSettingField($type, $value, $setting) ?>

					<?php if ($type !== 'hidden'): ?>
						<button class="btn btn-primary" type="submit">Update</button>
					<?php endif; ?>

					<input type="hidden" name="uid" value="<?= $uid ?>">

				</form>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>



<!-- Add Setting Modal -->
<div class="modal fade" tabindex="-1" id="addSettingModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<div class="modal-dialog">
		<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add Setting</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label for="type">Setting Type</label>
					<select class="form-select" name="type" aria-label="type">
						<option selected="" value="numeric">Numeric</option>
						<option value="alphanumeric">Alphanumeric</option>
						<option value="list">List</option>
						<option value="boolean">Boolean</option>
						<option value="json">JSON</option>
						<option value="hidden">Hidden</option>
					</select>
				</div>
				
				<div class="mb-3">
					<label for="name">Setting Name</label>
					<input type="text" class="form-control" name="name" aria-describedby="termNameHelp">
				</div>
				
				<div class="mb-3">
					<label for="date_start">Setting Description</label>
					<input type="text" class="form-control" name="description" aria-describedby="termStartDate">
				</div>
				
				<div class="mb-3">
					<label for="date_end">Setting Value</label>
					<input type="text" class="form-control" name="value" aria-describedby="termEndDate">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Add Setting</button>
			</div>
		</div>
		</form>
	</div>
</div>

<?php
function renderSettingField(string $type, string $value, array $setting): string {
	switch ($type) {
		case 'numeric':
			return '<input type="number" class="form-control" name="value" value="'. $value .'">';

		case 'boolean':
			$checked = ($setting['value'] === "true") ? "checked" : "";
			return '<div class="form-check m-0"><input type="hidden" name="value" value="false">
					<input type="checkbox" class="form-check-input" name="value" value="true" '. $checked .'></div>';

		case 'html':
		case 'json':
			return '<textarea rows="10" class="form-control font-monospace" name="value">'. $value .'</textarea>';

		case 'hidden':
			return '<p class="text-muted">Setting cannot be changed here</p>';

		default:
			return '<input type="text" class="form-control" name="value" value="'. $value .'">';
	}
}
?>