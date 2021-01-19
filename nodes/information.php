<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>

<?php
//check if updating existing setting
if (isset($_POST['contentInfo'])) {
  $_POST['contentInfo'] = str_replace("'", "\'", $_POST['contentInfo']);

  $sql  = "UPDATE settings";
  $sql .= " SET value = '" . $_POST['contentInfo'] . "'";
  $sql .= " WHERE name = 'scr_information'";
  $sql .= " LIMIT 1";

  $db->query($sql);

  echo $settingsClass->alert("success", "SCR Information Updated");
}

$text = $settingsClass->value('scr_information');

$title = "SCR Information";
$subtitle = "Details on dining rights, meal allowances and procedures";
if ($_SESSION['admin'] == true) {
  $icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#journal-text\"/></svg> Edit Content", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");
}

echo makeTitle($title, $subtitle, $icons);

echo $text;
?>





<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <form method="post" id="scr_information" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit SCR Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <textarea rows="10" class="form-control" name="contentInfo" id="contentInfo"><?php echo htmlspecialchars($text, ENT_QUOTES); ?></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#journal-text"/></svg> Update Information</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
const editor = SUNEDITOR.create(document.getElementById('contentInfo'),{
	"buttonList": [
		[
			"formatBlock",
			"bold",
			"underline",
			"italic",
			"strike",
			"fontColor",
			"hiliteColor",
			"removeFormat",
			"align",
			"horizontalRule",
			"link",
			"fullScreen",
			"codeView",
		]
	]
});

window.addEventListener("click", function(event) {
  document.getElementById('contentInfo').value = editor.getContents();
});
</script>
