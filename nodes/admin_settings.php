<?php
pageAccessCheck("settings");

$mealsClass = new meals();

if (isset($_POST['purge_old_bookings']) && $_POST['purge_old_bookings'] === '1') {
    // Get the retention period from settings
    $days = (int) $settingsClass->value('bookings_retention');

    // Prepared statement: `?` binds the days value safely
    $db->query("DELETE FROM bookings WHERE `date` < NOW() - INTERVAL ? DAY", $days);

    echo "Bookings purged";

    $meals = $mealsClass->betweenDates('1970-01-01', date('Y-m-d', strtotime("-{$days} days")));
    
    foreach ($meals AS $meal) {
      if ($meal->total_bookings_this_meal <= 0) {
        $meal->delete();
      }
    }
}

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
$subtitle = "Customise the behaviour, display and configuration of this site";
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#sliders\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons, true);
?>

<div class="alert alert-danger text-center"><strong>Warning!</strong> Making changes to these settings can disrupt the running of this site.  Proceed with caution.</div>

<div class="accordion" id="accordionExample">
  <?php
  foreach ($settings AS $setting) {
    if (isset($_GET['settingUID']) && $_GET['settingUID'] == $setting['uid']) {
      $headingShow = "accordion-button show";
      $settingShow = "accordion-collapse show";
    } else {
      $headingShow = "accordion-button collapsed";
      $settingShow = "accordion-collapse collapse";
    }

    $itemName = "collapse-" . $setting['uid'];

    $output  = "<div class=\"accordion-item\">";
      $output .= "<h2 class=\"accordion-header\" id=\"" . $setting['uid'] . "\">";
      $output .= "<button class=\"" . $headingShow . "\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#" . $itemName . "\" aria-expanded=\"true\" aria-controls=\"" . $itemName . "\">";
      $output .= "<strong>" . $setting['name'] . "</strong>: " . $setting['description'] . " <span class=\"badge bg-secondary\">" . $setting['type'] . "</span>";
      $output .= "</button></h2>";

      $output .= "<div id=\"" . $itemName . "\" class=\"" . $settingShow . "\" aria-labelledby=\"" . $setting['uid'] . "\" data-bs-parent=\"#accordionExample\">";
        $output .= "<div class=\"accordion-body\">";

        $output .= "<form method=\"post\" id=\"form-" .  $setting['uid'] . "\" action=\"" . $_SERVER['REQUEST_URI'] . "\">";
        
        if ($setting['type'] == "numeric") {
          $output .= "<div class=\"input-group\">";
            $output .= "<input type=\"number\" class=\"form-control\" id=\"value\" name=\"value\" value=\"" . escape($setting['value']). "\">";
            $output .= "<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
          $output .= "</div>";
        } elseif ($setting['type'] == "boolean") {
          $checked = "";
          if ($setting['value'] == "true") {
            $checked = " checked ";
          }
          $output .= "<div class=\"form-check\">";
          $output .= "<input type=\"hidden\" id=\"value\" name=\"value\" value=\"false\">";
          $output .= "<input type=\"checkbox\" class=\"form-check-input\" id=\"value\" name=\"value\" value=\"true\" " . $checked . ">";
          $output .= "<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
          $output .= "</div>";
        } elseif ($setting['type'] == "json") {
            $output .= "<textarea type=\"text\" rows=\"10\" class=\"form-control\" id=\"value\" name=\"value\">" . json_encode(json_decode($setting['value']), JSON_PRETTY_PRINT) . "</textarea>";
            $output .= "<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
        } elseif ($setting['type'] == "hidden") {
          $output .= "Setting cannot be changed here";
        } else {
          $output .= "<div class=\"input-group\">";
            $output .= "<input type=\"text\" class=\"form-control\" id=\"value\" name=\"value\" value=\"" . escape($setting['value']). "\">";
            $output .= "<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
          $output .= "</div>";
        }
        
        $output .= "<input type=\"hidden\" id=\"uid\" name=\"uid\" value=\"" . $setting['uid']. "\">";
        $output .= "</form>";


        $output .= "</div>";
      $output .= "</div>";
    $output .= "</div>";

    echo $output;
  }
  ?>
</div>

<h2 class="m-3">Icons Available in <code>./img/icon.svg</code></h2>
<?php
$iconsArray = array(
  "chough" => "Chough",
  "apple" => "Apple Logo",
  "microsoft" => "Micosoft Logo",
  "chef-hat" => "Chef Hat",
  "house-door" => "Home",
  "person" => "Member(s)",
  "person-plus" => "Member Add",
  "person-check" => "Member Added",
  "list-stars" => "Meals List",
  "journal-text" => "Information",
  "grip-vertical" => "Gripper",
  "sliders" => "Edit/Settings",
  "trash" => "Delete",
  "calendar-plus" => "Date Add",
  "plus-circle" => "Meal Add",
  "wine-glass" => "Wine Glass",
  "wine-bin" => "Wine Bin",
  "heart-full" => "Heart Full",
  "heart-empty" => "Heart Empty",
  "graduation-cap" => "Domus",
  "cookie" => "Dessert",
  "x-circle" => "Access Denied",
  "search" => "Search",
  "info-circle" => "Information",
  "chat-dots" => "Notifications",
  "calendar-plus" => "Date Add",
  "dark-mode" => "Dark Mode",
  "light-mode" => "Light Mode",
  "auto-mode" => "Auto Mode",
  "basket" => "Shopping Basket",
  "blank" => "Blank"
);


echo "<div class=\"row text-center\">";
foreach ($iconsArray AS $icon => $name) {
  $output  = "<div class=\"col-sm-2 mb-3\">";
  $output .= "<div class=\"card\">";
  $output .= "<div class=\"card-body\">";
  $output .= "<h5 class=\"card-title\"><code>" . $icon . "</code></h5>";
  $output .= "<span><svg width=\"3em\" height=\"3em\">";
  $output .= "<use xlink:href=\"img/icons.svg#" . $icon . "\"/>";
  $output .= "</svg></span>";
  $output .= "<p class=\"card-text pt-3 text-muted\">" . $name . "</p>";
  $output .= "</div>";
  $output .= "</div>";
  $output .= "</div>";

  echo $output;
}

echo "</div>";
?>

<hr />

<div class="alert alert-danger" role="alert">
  <h4 class="alert-heading">Purge old bookings and meals?</h4>
  <p><strong>WARNING!</strong> Purge bookings older than <?php echo $settingsClass->value('bookings_retention'); ?> days? This action is immediate, and cannot be undone!</p>
  <p>This will also delete all meals (without bookings) older than <?php echo $settingsClass->value('bookings_retention'); ?> days.</p>

  <form method="post" onsubmit="return confirm('Are you sure you want to permanently delete all bookings (and unbooked meals) older than <?php echo $settingsClass->value('bookings_retention'); ?> days?  This action cannot be undone!');">
    <input type="hidden" name="purge_old_bookings" value="1">
    <p><button type="submit" class="btn btn-danger">Purge old bookings</button></p>
  </form>
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
          <label for="type">Setting Type</label>
          <select class="form-select" name="type" id="type" aria-label="type">
            <option selected value="numeric">Numeric</option>
            <option value="alphanumeric">Alphanumeric</option>
            <option value="list">List</option>
            <option value="boolean">Boolean</option>
            <option value="json">JSON</option>
            <option value="hidden">Hidden</option>
          </select>
        </div>
        
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


<?php
function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}
?>