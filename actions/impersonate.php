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

  $logsClass->create("admin", $_SESSION['username'] . " impersonating " . $_POST['impersonate_ldap']);
}

// impersonate stop
if ($_POST['impersonate_submit_button'] == "stop") {
  $logsClass->create("admin", $_SESSION['username_original'] . " no longer impersonating " . $_SESSION['username']);

  $_SESSION['username'] = $_SESSION['username_original'];
  $_SESSION['type'] = $_SESSION['type_original'];
  unset($_SESSION['username_original']);
  unset($_SESSION['type_original']);
  unset($_SESSION['impersonating']);
}
?>
