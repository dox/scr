<?php
pageAccessCheck("reports");

$reportsClass = new reports();
$reports = $reportsClass->all();
?>

<?php
$title = "Reports";
$subtitle = "Export data for members, meals, wine, etc.";
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#calendar-plus\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons);

echo $reportsClass->displayTable();
?>