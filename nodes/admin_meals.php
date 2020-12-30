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
?>

<?php
$title = "Meals";
$subtitle = "Some text here about meal booking.  Make it simple!";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add New", "value" => "");

echo makeTitle($title, $subtitle, $icons);
?>




  <div class="list-group">
    <?php
    foreach ($meals AS $meal) {
      $mealObject = new meal($meal['uid']);

      $output  = "<a href=\"index.php?n=admin_meal&mealUID=" . $mealObject->uid . "\" class=\"list-group-item list-group-item-action\">";
      $output .= "<div class=\"d-flex w-100 justify-content-between\">";
      $output .= "<h5 class=\"mb-1\">" . $mealObject->name . "</h5>";
      $output .= "<small class=\"text-muted\">" . "<span class=\"badge bg-primary rounded-pill\">" . $mealObject->name . " weeks</span> " . $mealObject->uid . "</small>";
      //$output .= "<p id=\"" . $term['uid'] . "\" onclick=\"dismiss(this.id);\">dismiss this box</p>";
      //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
      $output .= "</div>";
      //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
      $output .= "<small class=\"text-muted\">" . dateDisplay($mealObject->date_meal) . " - " . dateDisplay($mealObject->date_meal) . "</small>";
      $output .= "</a>";

      echo $output;
    }
    ?>
  </div>
