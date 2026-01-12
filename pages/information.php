<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$settingUID = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_NUMBER_INT);
	$settingName = $settings->getName($settingUID);
	$settings->update($_POST, false); // don't log here
	$log->add(
		"Setting {$settingUID} ({$settingName}) updated",
		'setting',
		Log::SUCCESS
	);
}

// Allow only these paths
$allowed = [
	"scr_information" => [
		"title"    => "SCR Information",
		"subtitle" => "Details on dining rights, meal allowances and procedures"
	],
	"dining_arrangements" => [
		"title"    => "SCR Dining Arrangements",
		"subtitle" => "Dining arrangements at a glance"
	],
	"scr_accessibility" => [
		"title"    => "Accessibility",
		"subtitle" => "This accessibility statement applies to the SCR Meal Booking System"
	]
];

$subpage = $_GET['subpage'] ?? "scr_information";
$content = $settings->get($subpage);

// deny anything beyond our walls
if (!array_key_exists($subpage, $allowed)) {
	die("Unknown page requested");
}

echo pageTitle(
	$allowed[$subpage]['title'],
	$allowed[$subpage]['subtitle'],
	[
		[
			'permission' => 'settings',
			'title' => 'Edit',
			'class' => '',
			'event' => '',
			'icon' => 'pencil',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#editContentModal'
			]
		]
	]
);

echo $content;
?>

<!-- Edit Content Modal -->
<div class="modal modal-xl fade" tabindex="-1" id="editContentModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form method="post" id="contentEditForm" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
					<div class="modal-body">
						<div class="mb-3">
							<textarea class="form-control" id="value" name="value" rows="3"><?php echo $content;?></textarea>
							<input type="hidden" name="uid" value="<?php echo $settings->getUID($subpage); ?>">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
let editor;
editor = SUNEDITOR.create(document.getElementById('value'), {
	height: 300,
	buttonList: [
		['undo', 'redo'],
		['font', 'fontSize', 'formatBlock'],
		['bold', 'italic', 'underline', 'strike'],
		['fontColor', 'hiliteColor', 'align', 'list', 'lineHeight'],
		['table', 'link', 'image'],
		['fullScreen', 'codeView']
	]
});

// Sync content back to textarea on submit
document.getElementById('contentEditForm').addEventListener('submit', function(e) {
	document.getElementById('value').value = editor.getContents();
});
</script>