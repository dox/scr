<?php
admin_gatekeeper();

$membersClass = new members();


if (isset($_POST['precedence'])) {
  $precedenceArray = explode(",", $_POST['precedence']);

  $i = 0;
  do {
    $membersClass->updateMemberPrecendece($precedenceArray[$i], $i);

    $i++;
  } while ($i < count($precedenceArray));

  $logsClass->create("members_update", "Members order updated");
}
$members = $membersClass->all();

?>
<?php
$title = "SCR Memebers";
$subtitle = "Members, and their order of precedence.";
$icons[] = array("class" => "btn-primary", "name" => $icon_add_member . " Add New", "value" => "data-toggle=\"modal\" data-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons);
?>
<form method="post" id="termForm" action="index.php?n=admin_members">
  <ul class="list-group" id="members_list">
      <?php
      $scrStewardLDAP = $settingsClass->value('member_steward');





      foreach ($members AS $member) {
        $memberObject = new member($member['uid']);

        $output  = "<li class=\"list-group-item list-group-item-action\" id=\"" . $memberObject->uid . "\">";
        $output .= "<div class=\"d-flex w-100 \">";
        $output .= "<svg class=\"handle mr-2\" width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-grip-vertical\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">";
        $output .= "<path d=\"M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z\"/>";
        $output .= "</svg>";

        $output .= "<a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\"><span class=\"avatar avatar-sm\" style=\"background-image: url(http://ocsd.seh.ox.ac.uk//photos/UAS_UniversityCard-10076320.jpg)\">?</span></a>";
        $output .= "<a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\" class=\"mb-1\">" . $memberObject->displayName() . "</a>";
        $output .= "</div>";
        $output .= "<small class=\"\">" . $memberObject->type . "</small>";

        if ($memberObject->ldap == $scrStewardLDAP) {
          $output .= "<a href=\"#\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"SCR Steward\" class=\"list-item-actions text-warning float-right show\"><svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon text-yellow\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path><path d=\"M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z\"></path></svg></a>";
        }

        $output .= "</li>";

        echo $output;
      }
      ?>
  </li>
  </ul>

  <input type="hidden" name="precedence" id="precedence" value="" />
  <br />
  <button type="submit" onclick="itterate()" class="btn btn-block btn-primary">Save Order</button>
</form>

<script>
new Sortable(members_list, {
  handle: '.handle',
  animation: 150,
  ghostClass: 'blue-background-class'
});

function itterate() {
  var selection = document.getElementById("members_list").getElementsByClassName("list-item");

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
      <form method="post" id="termForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New SCR Memeber</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
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
            <input type="text" class="form-control" name="firstname" id="firstname" placeholder="" value="<?php echo $memberObject->firstname; ?>" required>
            <div class="invalid-feedback">
              Valid first name is required.
            </div>
          </div>

          <div class="col-12">
            <label for="lastname" class="form-label">Last name</label>
            <input type="text" class="form-control" name="lastname" id="lastname" placeholder="" value="<?php echo $memberObject->lastname; ?>" required>
            <div class="invalid-feedback">
              Valid last name is required.
            </div>
          </div>

          <div class="col-12">
            <label for="ldap" class="form-label">LDAP Username</label>
            <div class="input-group">
              <span class="input-group-text">@</span>
              <input type="text" class="form-control" name="ldap" id="ldap" placeholder="LDAP Username" value="<?php echo $memberObject->ldap; ?>" required>
            <div class="invalid-feedback">
                LDAP Username is required.
              </div>
            </div>
          </div>

          <div class="col-12">
            <label for="type" class="form-label">Member Type</label>
            <select class="form-select" name="type" id="type" required>
              <?php
              foreach ($membersClass->memberTypes() AS $type) {
                if ($type == $memberObject->type) {
                  $selected = " selected ";
                } else {
                  $selected = "";
                }
                $output = "<option value=\"" . $type . "\"" . $selected . ">" . $type . "</option>";

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
            <input type="text" class="form-control" name="dietary" id="dietary" placeholder="">
          </div>

          <div class="col-12">
            <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
            <input type="email" class="form-control" name="email" id="email" placeholder="" value="<?php echo $memberObject->email; ?>">
            <div class="invalid-feedback">
              Please enter a valid email address for shipping updates.
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save term</button>
      </div>
      </form>
    </div>
  </div>
</div>



<script>
function dismiss(el){
  document.getElementById(el).parentNode.style.display='none';
};

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
