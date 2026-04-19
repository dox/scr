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
	$allowed[$subpage]['subtitle']
);

if ($user->hasPermission('settings')) {
?>
<form method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" id="contentEditForm">
	<div class="mb-3">
		<input type="hidden" id="value" name="value" value="<?= htmlspecialchars($content, ENT_QUOTES); ?>">
		<div id="value_editor"></div>
		<input type="hidden" name="uid" value="<?php echo $settings->getUID($subpage); ?>">
	</div>
	<div class="d-flex justify-content-end">
		<button type="submit" class="btn btn-primary">Save</button>
	</div>
</form>

<script>
window.scrInformationEditor = SUNEDITOR.create(document.getElementById("value_editor"), {
	plugins: SUNEDITOR.plugins,
	mode: "balloon",
	value: <?= json_encode($content) ?>,
	buttonList: [
	  ["undo", "redo"],
	  "|",
	  ["bold", "italic", "underline"],
	  "|",
	  ["list", "link", "image"]
	]
});

var scrInformationLatestHtml = <?= json_encode($content) ?>;

function getInformationEditables() {
	return Array.from(document.querySelectorAll(".sun-editor-editable"));
}

function pickInformationContents() {
	var editables = getInformationEditables();
	var changed = editables
		.map(function (node) {
			return node.innerHTML;
		})
		.find(function (html) {
			return html && html !== scrInformationLatestHtml;
		});

	if (changed) {
		return changed;
	}

	if (editables.length > 0) {
		return editables[0].innerHTML;
	}

	return window.scrInformationEditor.getContents(true);
}

function syncInformationContent() {
	var editor = window.scrInformationEditor;
	var contentField = document.getElementById("value");
	var contents = pickInformationContents().trim();

	if (
		contents === "" ||
		contents === "<p><br></p>" ||
		contents === "<p>&nbsp;</p>"
	) {
		contents = "";
	}

	contentField.value = contents;
	scrInformationLatestHtml = contents;
	return true;
}

document.getElementById("contentEditForm").setAttribute("onsubmit", "return syncInformationContent()");
getInformationEditables().forEach(function (editable) {
	editable.addEventListener("input", syncInformationContent);
	editable.addEventListener("keyup", syncInformationContent);
});
</script>

<?php
} else {
	echo $content;
}
