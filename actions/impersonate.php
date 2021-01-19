<?php
include_once("../inc/autoload.php");

admin_gatekeeper();

// impersonate
if (isset($_POST['impersonate_ldap'])) {
  $logsClass->create("admin", $_SESSION['username'] . " impersonating " . $_POST['impersonate_ldap']);

  $_SESSION['username_original'] = $_SESSION['username'];
  $_SESSION['username'] = $_POST['impersonate_ldap'];
  $_SESSION['impersonating'] = "true";
}

// impersonate stop
if ($_POST['impersonate_submit_button'] == "stop") {
  $logsClass->create("admin", $_SESSION['username_original'] . " no longer impersonating " . $_SESSION['username']);

  $_SESSION['username'] = $_SESSION['username_original'];
  unset($_SESSION['username_original']);
  unset($_SESSION['impersonating']);
}
?>
