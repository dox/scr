<?php
admin_gatekeeper();

$notificationsClass = new notifications();
$notifications = $notificationsClass->all();
?>

<?php
$title = "Notifications";
$subtitle = "Some text here</a>.";
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#chat-dots\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons);
?>

<div class="list-group">
  <?php
  foreach ($notifications AS $notification) {
    $output  = "<a href=\"index.php?n=admin_notification&notificationUID=" . $notification['uid'] . "\" class=\"list-group-item list-group-item-action\">";
    $output .= "<div class=\"d-flex w-100 justify-content-between\">";
    $output .= "<h5 class=\"mb-1\">" . $notification['name'] . "</h5>";
    $output .= "<small class=\"text-muted\">" . "<span class=\"badge bg-primary rounded-pill\">" . dateDisplay($notification['date']) . "</span></small>";
    //$output .= "<p id=\"" . $term['uid'] . "\" onclick=\"dismiss(this.id);\">dismiss this box</p>";
    //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
    $output .= "</div>";
    //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
    $output .= "<small class=\"text-muted\">" . htmlspecialchars($notification['message']) . "</small>";
    $output .= "</a>";

    echo $output;
  }
  ?>
</div>
