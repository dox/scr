<?php
include_once("../inc/autoload.php");

pageAccessCheck("members");

if (checkpoint_charlie("members")) {
  if (!empty($_POST['firstname'])) {
    $ldapUser = $ldap_connection->query()
    ->where('givenname', '~=', escape($_POST['firstname']))
    ->where('sn', '~=', escape($_POST['sn']))
    ->first();
  } else {
    $ldapUser = $ldap_connection->query()
    ->where('sn', '~=', escape($_POST['sn']))
    ->first();
  }

  $bestGuessLDAP = $ldapUser['samaccountname'][0];

  $logArray['category'] = "ldap";
  $logArray['result'] = "success";
  $logArray['description'] = "LDAP lookup permitted for " . escape($_POST['firstname']) . "/" . escape($_POST['sn']) . " = " . $bestGuessLDAP;
  $logsClass->create($logArray);

  if (empty($bestGuessLDAP)) {
    echo "Unknown - please try a different firstname/lastname.";
  } else {
    echo $bestGuessLDAP;
  }
}
?>
