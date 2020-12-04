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
<div class="container">
  <div class="px-3 py-3 pt-md-5 pb-md-4 text-center">
    <h1 class="display-4">SCR Members</h1>
    <p class="lead">Members, and their order of precedence.</p>
  </div>

  <div class="pb-3 text-right">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-calendar-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
        <path fill-rule="evenodd" d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7z"/>
      </svg> Add new
    </button>
  </div>

  <form method="post" id="termForm" action="index.php?n=admin_members">
    <ul class="list-group nested-sortable" name="demo1" id="demo1">
      <?php
      foreach ($members AS $member) {
        $memberObject = new member($member['uid']);

        $icon = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-grip-horizontal handle\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M2 8a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2z\"/></svg>&nbsp;&nbsp;";

        $output  = "<li id=\"" . $memberObject->uid . "\" class=\"list-group-item\">" . $icon;
        $output .= "<a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\">" . $memberObject->displayName() . "</a>" . $memberObject->memberBadge();
        $output .= "<span class=\"float-right text-muted\">" . $memberObject->type . "</span>";
        $output .= "</li>";

        echo $output;
      }
      ?>
    </ul>
    <input type="hidden" name="precedence" id="precedence" value="" />
    <br />
    <button type="submit" onclick="itterate()" class="btn btn-block btn-primary">Save Order</button>
  </form>
</div>

<script>
new Sortable(demo1, {
  handle: '.handle',
  animation: 150,
  ghostClass: 'blue-background-class'
});

function itterate() {
  var selection = document.getElementById("demo1").getElementsByTagName("li");

  var arrayMembersUIDs = '';

  for(var i = 0; i < selection.length; i++) {
    arrayMembersUIDs = arrayMembersUIDs + selection[i]['id'] + ",";
      // do something with selection[i]
      //alert(selection[i]['id']);
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
</script>
