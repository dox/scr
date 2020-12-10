<?php
admin_gatekeeper();

$mealsClass = new meals();
$meals = $mealsClass->all();
?>

<?php
$title = "Meals";
$subtitle = "Some text here about meal booking.  Make it simple!";
$icons[] = array("class" => "btn-danger", "name" => "Test1", "value" => "");
$icons[] = array("class" => "btn-primary", "name" => "Test2", "value" => "");

echo makeTitle($title, $subtitle, $icons);
?>


  <div class="pb-3 text-right">
	<a class="btn btn-primary" href="index.php?n=admin_meal" role="button">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-calendar-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
        <path fill-rule="evenodd" d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7z"/>
      </svg> Add new
    </a>
  </div>

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
      $output .= "<small class=\"text-muted\">" . date('Y-m-d', strtotime($mealObject->date)) . " - " . date('Y-m-d', strtotime($mealObject->date)) . "</small>";
      $output .= "</a>";

      echo $output;
    }
    ?>
  </div>
