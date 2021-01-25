<link rel="stylesheet" href="css/flatpickr.min.css">
<script src="js/flatpickr.js"></script>

<?php
admin_gatekeeper();
$mealsClass = new meals();

if (isset($_POST['mealNEW'])) {
 echo "ADD NEW!";
 $mealObject = new meal();
 $mealObject->create($_POST);
 $mealObject = new meal($_GET['mealUID']);
 $_GET['add'] = false;
}

if (isset($_GET['mealDELETE'])) {
 echo "DELETE!";
 $mealObject = new meal($_GET['mealDELETE']);
 $mealObject->delete();
}

$meals = $mealsClass->all();
$mealsTemplates = $mealsClass->allTemplates();
?>

<?php
$title = "Meals";
$subtitle = "All meals both past and present";
$icons[] = array("class" => "btn-info", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#bullseye\"/></svg> Apply Template", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add New", "value" => "onclick=\"location.href='index.php?n=admin_meal&add'\"");

echo makeTitle($title, $subtitle, $icons);
?>
<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <div class="divide-y">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
        <span>Templates</span>
        <span class="badge bg-secondary rounded-pill"><?php echo count($mealsTemplates); ?></span>
      </h4>

      <div class="list-group">
        <?php
        foreach ($mealsTemplates AS $meal) {
          $mealObject = new meal($meal['uid']);

          $output  = "<a href=\"index.php?n=admin_meal&mealUID=" . $mealObject->uid . "\" class=\"list-group-item list-group-item-action\">";
          $output .= "<div class=\"d-flex w-100 justify-content-between\">";
          $output .= "<h5 class=\"mb-1\">" . $mealObject->name . "</h5>";
          $output .= "<small class=\"text-muted\">" . "<span class=\"badge bg-primary rounded-pill\">" . $mealObject->type . "</span></small>";
          //$output .= "<p id=\"" . $term['uid'] . "\" onclick=\"dismiss(this.id);\">dismiss this box</p>";
          //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
          $output .= "</div>";
          //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
          $output .= "<small class=\"text-muted\">" . $mealObject->location . "</small>";
          $output .= "</a>";

          echo $output;
        }
        ?>
      </div>
    </div>
  </div>
  <div class="col-md-7 col-lg-8">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span>Meals</span>
      <span class="badge bg-secondary rounded-pill"><?php echo $mealsClass->allCount(); ?></span>
    </h4>

    <div class="list-group">
      <?php
      foreach ($meals AS $meal) {
        $mealObject = new meal($meal['uid']);

        $output  = "<a href=\"index.php?n=admin_meal&mealUID=" . $mealObject->uid . "\" class=\"list-group-item list-group-item-action\">";
        $output .= "<div class=\"d-flex w-100 justify-content-between\">";
        $output .= "<h5 class=\"mb-1\">" . dateDisplay($mealObject->date_meal) . " " . $mealObject->name . "</h5>";
        $output .= "<small class=\"text-muted\">" . "<span class=\"badge bg-primary rounded-pill\">" . $mealObject->type . "</span></small>";
        //$output .= "<p id=\"" . $term['uid'] . "\" onclick=\"dismiss(this.id);\">dismiss this box</p>";
        //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
        $output .= "</div>";
        //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
        $output .= "<small class=\"text-muted\">" . $mealObject->location . "</small>";
        $output .= "</a>";

        echo $output;
      }
      ?>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="termForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Apply Template to Week</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
            <label for="name">Template</label>
            <select class="form-select" name="template_name" id="template_name" required>
              <option value=""></option>
              <?php
              foreach ($settingsClass->templates() AS $template) {
                $output = "<option value=\"" . $template['name'] . "\">" . $template['name'] . " - " . $template['description'] . "</option>";

                echo $output;
              }
              ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="template_start_dateDesc">Week Commencing</label>
            <div class="input-group">
              <span class="input-group-text"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
              <input type="text" class="form-control" name="template_start_date" id="template_start_date" aria-describedby="template_start_dateDesc">
            </div>
            <small id="template_start_dateHelp" class="form-text text-muted">Template will apply to the whole week, starting on the Sunday</small>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" onclick="applyTemplate()"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#bullseye"/></svg> Apply Template</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
var fp = flatpickr("#template_start_date", {
  dateFormat: "Y-m-d",
  enable: [
        function(date) {
            // return true to disable
            return (date.getDay() === 0);

        }
    ]
})
</script>
