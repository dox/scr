<?php
admin_gatekeeper();

$membersClass = new members();

# CHECK IF WE NEED TO CREATE NEW MEMBER FROM FORM SUBMISSION
if (isset($_POST['memberNew'])) {
 $memberObject = new member();
 $memberObject->create($_POST);
 $membersClass = new members();
}

if (isset($_POST['precedence'])) {
  $precedenceArray = explode(",", $_POST['precedence']);

  $i = 0;
  do {
    $membersClass->updateMemberPrecendece($precedenceArray[$i], $i);

    $i++;
  } while ($i < count($precedenceArray));

  $logsClass->create("members_update", "Members order updated");
}
$membersEnabled = $membersClass->allEnabled('mcr');
$membersDisabled = $membersClass->allDisabled('mcr');

?>
<?php
$title = "MCR Members";
$subtitle = "MCR Members, and other non-SCR Memebers.";

echo makeTitle($title, $subtitle);
?>

  <!--<div class="list-group" id="members_list">-->
  <ul class="list-group" id="members_list">
    <?php
    $scrStewardLDAP = $settingsClass->value('member_steward');

    foreach ($membersEnabled AS $member) {
      $memberObject = new member($member['uid']);

      $output  = "<li class=\"list-group-item\" id=\"" . $memberObject->uid . "\">";
      $output .= "<a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\">" . $memberObject->displayName() . "</a>";

      $output .= "<span class=\"float-end\">";
      if ($memberObject->ldap == $scrStewardLDAP) {
        $output .= "<a href=\"#\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"SCR Steward\" class=\"list-item-actions text-warning\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#star\"/></svg></a> ";
      }
      $output .= "<span class=\"text-muted\">" . $memberObject->category . "</span>";

      $output .= "</span>";
      $output .= "</li>";

      echo $output;
    }
    ?>
  </ul>

<hr />

<h2>Disabled Members</h2>
<ul class="list-group">
  <?php
  foreach ($membersDisabled AS $member) {
    $memberObject = new member($member['uid']);

    $output  = "<li class=\"list-group-item\" id=\"" . $memberObject->uid . "\">";
    $output .= "<a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\">" . $memberObject->displayName() . "</a>";

    $output .= "<span class=\"float-end\">";
    if ($memberObject->ldap == $scrStewardLDAP) {
      $output .= "<a href=\"#\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"SCR Steward\" class=\"list-item-actions text-warning\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#star\"/></svg></a> ";
    }
    $output .= "<span class=\"text-muted\">" . $memberObject->category . "</span>";

    $output .= "</span>";
    $output .= "</li>";

    echo $output;
  }
  ?>
</ul>
