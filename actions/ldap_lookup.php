<?php
include_once("../inc/autoload.php");

if (!empty($_POST['firstname'])) {
  $ldapUser = $ldap_connection->query()
  ->where('givenname', '~=', $_POST['firstname'])
  ->where('sn', '~=', $_POST['sn'])
  ->first();
} else {
  $ldapUser = $ldap_connection->query()
  ->where('sn', '~=', $_POST['sn'])
  ->first();
}

$bestGuessLDAP = $ldapUser['samaccountname'][0];

if (empty($bestGuessLDAP)) {
  echo "Unknown - please try a different firstname/lastname.";
} else {
  echo $bestGuessLDAP;
}
?>
