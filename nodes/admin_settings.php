<?php
admin_gatekeeper();

//check if creating new setting
if (isset($_POST['name'])) {
  $settingsClass->create($_POST);
  echo $settingsClass->alert("success", $_POST['name'], " setting created");
}

//check if updating existing setting
if (isset($_POST['uid'])) {
  $settingsClass->update($_POST);
  echo $settingsClass->alert("success", "UID: " . $_POST['uid'], " setting updated");
}

$settings = $settingsClass->all();

?>

<?php
$title = "Site Settings";
$subtitle = "Customise the behaviour, display and configuration of this site.";
$icons[] = array("class" => "btn-primary", "name" => $icon_edit. " Add New", "value" => "data-toggle=\"modal\" data-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons);
?>

<div class="alert alert-danger text-center"><strong>Warning!</strong> Making changes to these settings can distrupt the running of the SCR Booking site.  Proceed with caution.</div>

<div class="accordion" id="accordionExample">
  <?php
  foreach ($settings AS $setting) {
    $itemName = "collapse-" . $setting['uid'];

    $output  = "<div class=\"card\">";
    $output .= "<div class=\"card-header\" id=\"" . $setting['uid'] . "\">";
    $output .= "<h2 class=\"mb-0\"><button class=\"btn btn-link btn-block text-left collapsed\" type=\"button\" data-toggle=\"collapse\" data-target=\"#" . $itemName . "\" aria-expanded=\"true\" aria-controls=\"" . $itemName . "\">";
    $output .= "<strong>" . $setting['name'] . "</strong>: " . $setting['description'];
    $output .= "</button></h2></div>";

    $output .= "<div id=\"" . $itemName . "\" class=\"collapse\" aria-labelledby=\"" . $setting['uid'] . "\" data-parent=\"#accordionExample\">";
    $output .= "<div class=\"card-body\">";

    $output .= "<form method=\"post\" id=\"form-" .  $setting['uid'] . "\" action=\"" . $_SERVER['REQUEST_URI'] . "\" class=\"needs-validation\" novalidate>";

    $output .= "<div class=\"input-group\">";
    $output .= "<input type=\"text\" class=\"form-control\" id=\"value\" name=\"value\" value=\"" . $setting['value']. "\">";
    $output .= "<div class=\"input-group-append\">";
    $output .= "<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
    $output .= "</div>";
    $output .= "</div>";
    $output .= "<input type=\"hidden\" id=\"uid\" name=\"uid\" value=\"" . $setting['uid']. "\">";

    $output .= "</form>";

    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";

    echo $output;
  }
  ?>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="termForm" action="index.php?n=admin_settings">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New Setting</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="name">Setting Name</label>
            <input type="text" class="form-control" name="name" id="name" aria-describedby="termNameHelp">
          </div>

          <div class="form-group">
            <label for="date_start">Setting Description</label>
            <input type="text" class="form-control" name="description" id="description" aria-describedby="termStartDate">
          </div>

          <div class="form-group">
            <label for="date_end">Setting Value</label>
            <input type="text" class="form-control" name="value" id="value" aria-describedby="termEndDate">
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Setting</button>
      </div>
      </form>
    </div>
  </div>

<script>
function dismiss(el){
  document.getElementById(el).parentNode.style.display='none';
};
</script>
