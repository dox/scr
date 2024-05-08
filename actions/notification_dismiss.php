<?php
include_once("../inc/autoload.php");

// dismiss notification for current user
if (isset($_POST['notificationUID'])) {
  $uid = filter_var($_POST['notificationUID'], FILTER_SANITIZE_NUMBER_INT);

  $notificationsClass = new notifications();
  $notificationsClass->markAsRead($uid);
}
?>
