<?php
include_once("../inc/autoload.php");

$memberObject = new member($_POST['member_uid']);

if (checkpoint_charlie("members")) {
  $memberObject->delete();
} else {
  $logArray['category'] = "member";
  $logArray['result'] = "danger";
  $logArray['description'] = "Error attempting to delete [memberUID:" . $_POST['member_uid'] . "]";
  $logsClass->create($logArray);
}
?>
