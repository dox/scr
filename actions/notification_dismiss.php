<?php
include_once("../inc/autoload.php");

// dismiss notification for current user
if (isset($_POST['notificationUID'])) {
  $notificationsClass = new notifications();
  $notificationsClass->markAsRead($_POST['notificationUID']);
}
?>
