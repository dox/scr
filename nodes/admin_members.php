<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

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

  $logArray['category'] = "admin";
  $logArray['result'] = "success";
  $logArray['description'] = "Members order updated";
  $logsClass->create($logArray);
}
$scrMembersEnabled = $membersClass->allEnabled('scr');
$scrMembersDisabled = $membersClass->allDisabled('scr');
$mcrMembersEnabled = $membersClass->allEnabled('mcr');
$mcrMembersDisabled = $membersClass->allDisabled('mcr');

?>
<?php
$title = "Members";
$subtitle = "Members, and their order of precedence.";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons);
?>

<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <a class="nav-link active" id="scr-tab" data-bs-toggle="tab" href="#scr" role="tab" aria-controls="scr" aria-selected="true">SCR (<?php echo count($scrMembersEnabled);?>)</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link" id="mcr-tab" data-bs-toggle="tab" href="#mcr" role="tab" aria-controls="mcr" aria-selected="false">MCR (<?php echo count($mcrMembersEnabled);?>)</a>
  </li>
</ul>

<div class="tab-content mt-3" id="membersContent">
  <div class="tab-pane fade show active" id="scr" role="tabpanel" aria-labelledby="scr-tab">
    <form method="post" id="termForm" action="index.php?n=admin_members">
      <!--<div class="list-group" id="members_list">-->
      <ul class="list-group" id="scr_members_list">
        <?php
        $scrStewardLDAP = $settingsClass->value('member_steward');

        foreach ($scrMembersEnabled AS $member) {
          $memberObject = new member($member['uid']);
          $handle  = "<svg width=\"1em\" height=\"1em\" class=\"handle\"><use xlink:href=\"img/icons.svg#grip-vertical\"/></svg>";


          $output  = "<li class=\"list-group-item\" id=\"" . $memberObject->uid . "\">";
          $output .= $handle;
          $output .= "<a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\">" . $memberObject->displayName() . "</a>";

          $output .= "<span class=\"float-end\">";
          $output .= $memberObject->stewardBadge() ." ";

          $output .= $memberObject->adminBadge() ." ";

          $output .= "<span class=\"text-muted\">" . $memberObject->category . "</span>";

          $output .= "</span>";
          $output .= "</li>";

          echo $output;
        }
        ?>
      </ul>

      <input type="hidden" name="precedence" id="precedence" value="" />
      <br />
      <button type="submit" onclick="itterate()" class="btn btn-block btn-primary">Save Order</button>
    </form>

    <hr />
    <h2>Disabled Dining Rights</h2>
    <ul class="list-group">
      <?php
      foreach ($scrMembersDisabled AS $member) {
        $memberObject = new member($member['uid']);
        $handle  = "<svg width=\"1em\" height=\"1em\" class=\"handle\"><use xlink:href=\"img/icons.svg#grip-vertical\"/></svg>";


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
  </div>
  <div class="tab-pane fade" id="mcr" role="tabpanel" aria-labelledby="mcr-tab">
    <ul class="list-group" id="mcr_members_list">
      <?php
      foreach ($mcrMembersEnabled AS $member) {
        $memberObject = new member($member['uid']);

        $output  = "<li class=\"list-group-item\" id=\"" . $memberObject->uid . "\">";
        $output .= "<a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\">" . $memberObject->displayName() . "</a>";

        $output .= "<span class=\"float-end\">";
        $output .= "<span class=\"text-muted\">" . $memberObject->category . "</span>";

        $output .= "</span>";
        $output .= "</li>";

        echo $output;
      }
      ?>
    </ul>

    <hr />

    <h2>Disabled Dining Rights</h2>
    <ul class="list-group">
      <?php
      foreach ($mcrMembersDisabled AS $member) {
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
  </div>
</div>



<script>
new Sortable(scr_members_list, {
  handle: '.handle',
  animation: 150,
  ghostClass: 'blue-background-class'
});

function itterate() {
  var selection = document.getElementById("scr_members_list").getElementsByClassName("list-group-item");

  var arrayMembersUIDs = '';

  for(var i = 0; i < selection.length; i++) {
    arrayMembersUIDs = arrayMembersUIDs + selection[i]['id'] + ",";
  }

  document.getElementById("precedence").value = arrayMembersUIDs;
}
</script>

<style>
.handle {
	cursor: grab;
}
</style>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="memberForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New SCR Memeber</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="mb-12">
            <label for="title" class="form-label">Title</label>
            <select class="form-select" name="title" id="title" required>
              <?php
              foreach ($membersClass->memberTitles() AS $title) {
                $output = "<option value=\"" . $title . "\">" . $title . "</option>";

                echo $output;
              }
              ?>
            </select>
            <div class="invalid-feedback">
              Title is required.
            </div>
          </div>

          <div class="mb-12">
            <label for="firstname" class="form-label">First name</label>
            <input type="text" class="form-control" name="firstname" id="firstname" required>
            <div class="invalid-feedback">
              Valid first name is required.
            </div>
          </div>

          <div class="col-12">
            <label for="lastname" class="form-label">Last name</label>
            <input type="text" class="form-control" name="lastname" id="lastname" required>
            <div class="invalid-feedback">
              Valid last name is required.
            </div>
          </div>

          <div class="col-12">
            <label for="ldap" class="form-label">LDAP Username</label>
            <div class="input-group">
              <span class="input-group-text" onclick="ldapLookup()">@</span>
              <input type="text" class="form-control" name="ldap" id="ldap" required>
            <div class="invalid-feedback">
                LDAP Username is required.
              </div>
            </div>
          </div>

          <div class="col-12">
            <label for="category" class="form-label">Member Category</label>
            <select class="form-select" name="category" id="category" required>
              <?php
              foreach ($membersClass->memberCategories() AS $category) {
                $output = "<option value=\"" . $category . "\"" . ">" . $category . "</option>";

                echo $output;
              }
              ?>
            </select>
            <div class="invalid-feedback">
              Please select a valid Member Type.
            </div>
          </div>

          <div class="col-12">
            <label for="dietary" class="form-label">Dietary Information</label>
            <input type="text" class="form-control" name="dietary" id="dietary">
          </div>

          <div class="col-12">
            <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
            <input type="email" class="form-control" name="email" id="email">
            <div class="invalid-feedback">
              Please enter a valid email address for shipping updates.
            </div>
          </div>

          <div class="col-12">
            <label for="enabled" class="form-label">Enabled/Disabled Status</label>
            <select class="form-select" name="enabled" id="enabled" required>
              <option value="1" selected>Enabled</option>
              <option value="0">Disabled</option>
            </select>
            <div class="invalid-feedback">
              Status is required.
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#person-plus"/></svg> Add Member</button>
        <input type="hidden" name="memberNew" value="true" />
        <input type="hidden" name="precedence" value="999" />
        <input type="hidden" name="type" value="SCR" />
      </div>
      </form>
    </div>
  </div>
</div>

<script>

function dismiss(el){
  document.getElementById(el).parentNode.style.display='none';
};



</script>
