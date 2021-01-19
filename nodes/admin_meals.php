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

$meals = $mealsClass->all();
$mealsTemplates = $mealsClass->allTemplates();
?>

<?php
$title = "Meals";
$subtitle = "All meals both past and present";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add New", "value" => "onclick=\"location.href='index.php?n=admin_meal&add'\"");

echo makeTitle($title, $subtitle, $icons);
?>
<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <div class="divide-y">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Templates</span>
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
      <span class="text-muted">Meals</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($meals); ?></span>
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
