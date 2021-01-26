<?php
admin_gatekeeper();

//check if creating new setting
if (isset($_POST['name'])) {
  $settingsClass->create($_POST);
}

//check if updating existing setting
if (isset($_POST['uid'])) {
  $settingsClass->update($_POST);
}

$settings = $settingsClass->all();

?>

<?php
$title = "Site Settings";
$subtitle = "Customise the behaviour, display and configuration of this site.";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#sliders\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons);
?>

<div class="alert alert-danger text-center"><strong>Warning!</strong> Making changes to these settings can disrupt the running of the SCR Booking site.  Proceed with caution.</div>

<div class="accordion" id="accordionExample">
  <?php
  foreach ($settings AS $setting) {
    $itemName = "collapse-" . $setting['uid'];

    $output  = "<div class=\"accordion-item\">";
      $output .= "<h2 class=\"accordion-header\" id=\"" . $setting['uid'] . "\">";
      $output .= "<button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#" . $itemName . "\" aria-expanded=\"true\" aria-controls=\"" . $itemName . "\">";
      $output .= "<strong>" . $setting['name'] . "</strong>: " . $setting['description'];
      $output .= "</button></h2>";

      $output .= "<div id=\"" . $itemName . "\" class=\"accordion-collapse collapse\" aria-labelledby=\"" . $setting['uid'] . "\" data-bs-parent=\"#accordionExample\">";
        $output .= "<div class=\"accordion-body\">";

        $output .= "<form method=\"post\" id=\"form-" .  $setting['uid'] . "\" action=\"" . $_SERVER['REQUEST_URI'] . "\" class=\"needs-validation\" novalidate>";
        $output .= "<div class=\"input-group\">";
          $output .= "<input type=\"text\" class=\"form-control\" id=\"value\" name=\"value\" value=\"" . escape($setting['value']). "\">";
          $output .= "<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
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

<h2 class="m-3">Icons Availabe in <code>./img/icon.svg</code></h2>

<div>
  <ul class="list-group">
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2.4em" height="2em">
        <use xlink:href="img/icons.svg#chough-regular"/>
      </svg> Chough (default logo)</span>
      <small>[chough-regular]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#house-door"/>
      </svg> Home</span>
      <small>[house-door]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#person"/>
      </svg> Member(s)</span>
      <small>[person]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#person-plus"/>
      </svg> Member Add</span>
      <small>[person-plus]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#person-check"/>
      </svg> Member added</span>
      <small>[person-check]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#list-stars"/>
      </svg> Meals List</span>
      <small>[list-stars]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#journal-text"/>
      </svg> Information</span>
      <small>[journal-text]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#grip-vertical"/>
      </svg> Gripper for re-orderable lists</span>
      <small>[grip-vertical]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#sliders"/>
      </svg> Edit/Settings</span>
      <small>[sliders]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#trash"/>
      </svg> Delete</span>
      <small>[trash]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#calendar-plus"/>
      </svg> Date Add</span>
      <small>[calendar-plus]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#plus-circle"/>
      </svg> Meal Add</span>
      <small>[plus-circle]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#wine-glass"/>
      </svg> Wine</span>
      <small>[wine-glass]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#graduation-cap"/>
      </svg> Domus</span>
      <small>[graduation-cap]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#cookie"/>
      </svg> Dessert</span>
      <small>[cookie]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#x-circle"/>
      </svg> Access Denied</span>
      <small>[x-circle]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#search"/>
      </svg> Search</span>
      <small>[search]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#info-circle"/>
      </svg> Information</span>
      <small>[info-circle]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em">
        <use xlink:href="img/icons.svg#chat-dots"/>
      </svg> Notifications</span>
      <small>[chat-dots]</small>
    </div>
    </li>
    <li class="list-group-item list-group-item-action">
      <div class="d-flex w-100 justify-content-between">
      <span><svg width="2em" height="2em" class="spinning">
        <use xlink:href="img/icons.svg#spinner"/>
      </svg> Spinner</span>
      <small>[spinner]</small>
    </div>
    </li>
  </ul>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="termForm" action="index.php?n=admin_settings">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New Setting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
            <label for="name">Setting Name</label>
            <input type="text" class="form-control" name="name" id="name" aria-describedby="termNameHelp">
          </div>

          <div class="mb-3">
            <label for="date_start">Setting Description</label>
            <input type="text" class="form-control" name="description" id="description" aria-describedby="termStartDate">
          </div>

          <div class="mb-3">
            <label for="date_end">Setting Value</label>
            <input type="text" class="form-control" name="value" id="value" aria-describedby="termEndDate">
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><svg width="2em" height="2em"><use xlink:href="img/icons.svg#sliders"/></svg> Add Setting</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
function dismiss(el){
  document.getElementById(el).parentNode.style.display='none';
};
</script>
