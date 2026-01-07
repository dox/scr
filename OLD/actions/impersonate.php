<?php
include_once("../inc/autoload.php");

// impersonate
if (isset($_POST['impersonate_ldap'])) {
  $member = filter_var($_POST['impersonate_ldap'], FILTER_SANITIZE_STRING);

  $memberObject = new member($member);
  
  $_SESSION['original_details'] = $_SESSION;

  $_SESSION['type'] = $memberObject->type;
  $_SESSION['category'] = $memberObject->category;
  $_SESSION['username'] = $memberObject->ldap;
  $_SESSION['impersonating'] = "true";

  if ($_POST['maintainAdminAccess'] == "true") {
    $_SESSION['permissions'] = $_SESSION['original_details']['permissions'];
  } else {
    $_SESSION['permissions'] = explode(",",$memberObject->permissions);
  }

  $logArray['category'] = "admin";
  $logArray['result'] = "info";
  $logArray['description'] = $_SESSION['original_details']['username'] . " impersonating " . $_SESSION['username'];
  $logsClass->create($logArray);
}

// impersonate stop
if ($_POST['impersonate_submit_button'] == "stop") {
  $logArray['category'] = "admin";
  $logArray['result'] = "info";
  $_SESSION['admin'] = "1";
  $logArray['description'] = $_SESSION['original_details']['username'] . " no longer impersonating " . $_SESSION['username'];
  $logsClass->create($logArray);

  $_SESSION = $_SESSION['original_details'];
  
  unset($_SESSION['original_details']);
  unset($_SESSION['impersonating']);
}
?>
