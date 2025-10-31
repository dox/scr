<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>

<?php
//check if updating existing setting
if (isset($_POST['contentInfo'])) {
  $db->query(
      "UPDATE settings SET value = ? WHERE name = ?",
      $_POST['contentInfo'],
      'dining_arrangements'
  );
  
  $logArray['category'] = "admin";
  $logArray['result'] = "success";
  $logArray['description'] = "SCR dining arrangements at a glance updated";
  $logsClass->create($logArray);
  
  echo $settingsClass->alert("success", "Success!", "SCR Dining Arangements successfully updated");

}

$text = $settingsClass->value('dining_arrangements');

$title = "Dining Arrangements";
$subtitle = "Dining arrangements at a glance";
if (checkpoint_charlie("settings")) {
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
        <h5 class="modal-title" id="exampleModalLabel">Edit Dining Arrangements</h5>
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

SUNEDITOR.create(document.getElementById('contentInfo'),{
    defaultTag: 'div',
    buttonList: [
        ['codeView', 'bold', 'italic', 'underline', 'table']
    ],
    addTagsWhitelist: 'div|span|table|thead|tbody|tr|th|td|i|br',
    attributesWhitelist: {
        all: 'class style id' // Allows all elements to have 'class', 'style', and 'id'
    },
    pasteTagsWhitelist: 'div|span|table|thead|tbody|tr|th|td|i|br',
    pasteOptions: {
        cleanStyle: false, // Do not remove inline styles
        cleanAttrs: [], // Keep all attributes, including class and style
        removeTagFilter: false // Prevent stripping of tags
    }
});

window.addEventListener("click", function(event) {
  document.getElementById('contentInfo').value = editor.getContents();
});
</script>
