<?php
$user->pageCheck('settings');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$settings->update($_POST);
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

<div class="accordion" id="accordionExample">
<?php foreach ($settings->getAll() as $setting): 
	$uid   = (int) $setting['uid']; // enforce numeric UID, if that is its nature
	$name  = htmlspecialchars($setting['name'], ENT_QUOTES, 'UTF-8');
	$desc  = htmlspecialchars($setting['description'], ENT_QUOTES, 'UTF-8');
	$type  = htmlspecialchars($setting['type'], ENT_QUOTES, 'UTF-8');
	$value = htmlspecialchars($setting['value'], ENT_QUOTES, 'UTF-8');

	$isActive = isset($_GET['settingUID']) && $_GET['settingUID'] == $uid;

	$headingClass = $isActive ? "accordion-button" : "accordion-button collapsed";
	$collapseClass = $isActive ? "accordion-collapse show" : "accordion-collapse collapse";

	$itemName = "collapse-{$uid}";
	$url = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
?>
	<div class="accordion-item">
		<h2 class="accordion-header" id="heading-<?= $uid ?>">
			<button class="<?= $headingClass ?>" type="button" data-bs-toggle="collapse"
					data-bs-target="#<?= $itemName ?>" aria-expanded="<?= $isActive ? "true" : "false" ?>"
					aria-controls="<?= $itemName ?>">
				<strong><?= $name ?></strong>: <?= $desc ?> <span class="badge bg-secondary"><?= $type ?></span>
			</button>
		</h2>

		<div id="<?= $itemName ?>" class="<?= $collapseClass ?>" aria-labelledby="heading-<?= $uid ?>" data-bs-parent="#accordionExample">
			<div class="accordion-body">
				<form method="post" id="form-<?= $uid ?>" action="<?= $url ?>">
					
					<?php switch ($type):
						case 'numeric': ?>
							<div class="input-group">
								<input type="number" class="form-control" name="value" value="<?= $value ?>">
								<button class="btn btn-primary" type="submit">Update</button>
							</div>
						<?php break; ?>

						<?php case 'boolean':
							$checked = ($setting['value'] === "true") ? "checked" : ""; ?>
							<div class="form-check">
								<input type="hidden" name="value" value="false">
								<input type="checkbox" class="form-check-input" name="value" value="true" <?= $checked ?>>
								<button class="btn btn-primary" type="submit">Update</button>
							</div>
						<?php break; ?>

						<?php case 'html': ?>
							<textarea rows="10" class="form-control" name="value"><?= $value ?></textarea>
							<button class="btn btn-primary" type="submit">Update</button>
						<?php break; ?>

						<?php case 'hidden': ?>
							<div>Setting cannot be changed here</div>
						<?php break; ?>

						<?php default: ?>
							<div class="input-group">
								<input type="text" class="form-control" name="value" value="<?= $value ?>">
								<button class="btn btn-primary" type="submit">Update</button>
							</div>
					<?php endswitch; ?>

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
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Feature Not Yet Available</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><span class="text-danger"><strong>WARNING!</strong> This feature is not yet available</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">VOID</button>
			</div>
		</div>
	</div>
</div>