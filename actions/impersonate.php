<?php
include_once("../inc/autoload.php");

admin_gatekeeper();

// impersonate
if (isset($_POST['impersonate_ldap'])) {
  $memberObject = new member($_POST['impersonate_ldap']);

  $_SESSION['username_original'] = $_SESSION['username'];
  $_SESSION['type_original'] = $_SESSION['type'];
  $_SESSION['type'] = $memberObject->type;
  $_SESSION['username'] = $memberObject->ldap;
  $_SESSION['impersonating'] = "true";

  $logArray['category'] = "admin";
  $logArray['result'] = "info";
  $logArray['description'] = $_SESSION['username_original'] . " impersonating " . $_POST['impersonate_ldap'];
  $logsClass->create($logArray);
}

// impersonate stop
if ($_POST['impersonate_submit_button'] == "stop") {
  $logArray['category'] = "admin";
  $logArray['result'] = "info";
  $logArray['description'] = $_SESSION['username_original'] . " no longer impersonating " . $_SESSION['username'];
  $logsClass->create($logArray);

  $_SESSION['username'] = $_SESSION['username_original'];
  $_SESSION['type'] = $_SESSION['type_original'];
  unset($_SESSION['username_original']);
  unset($_SESSION['type_original']);
  unset($_SESSION['impersonating']);
}
?>
