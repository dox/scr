<?php

//$grantedPermissions = explode(",", $memberObject->permissions);
foreach (available_permissions() AS $permission => $description) {
  echo "<h2>" . $permission . " <small class=\"text-body-secondary\">" . $description . "</small></h2>";
  $sql  = "SELECT * FROM members";
  $sql .= " WHERE permissions = \"global_admin\" OR permissions LIKE \"%" . $permission . "%\"";
  $sql .= " ORDER BY precedence ASC";
  
  $members = $db->query($sql)->fetchAll();
  foreach ($members AS $member) {
    $member = new member($member['uid']);
    echo "<p>" . $member->displayName() . " (" . $member->ldap . ")</p>";
  }
  
  echo "<hr />";
}

$logArray['category'] = "report";
$logArray['result'] = "success";
$logArray['description'] = "[reportUID:" . $report['uid'] . "] run";
$logsClass->create($logArray);
?>
