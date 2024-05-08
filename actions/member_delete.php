<?php
include_once("../inc/autoload.php");

$uid = filter_var($_POST['member_uid'], FILTER_SANITIZE_NUMBER_INT);

$memberObject = new member($uid);

if (checkpoint_charlie("members")) {
  $memberObject->delete();
} else {
  $logArray['category'] = "member";
  $logArray['result'] = "danger";
  $logArray['description'] = "Error attempting to delete [memberUID:" . $uid . "]";
  $logsClass->create($logArray);
}
?>
