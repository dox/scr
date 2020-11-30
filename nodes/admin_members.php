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
    <p class="lead">Some text here about meal booking.  Make it simple!</p>
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
      <form method="post" id="termForm" action="index.php?n=admin_terms">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New Term Date</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="inputTermName">Term Name</label>
            <input type="text" class="form-control" name="inputTermName" id="inputTermName" aria-describedby="termNameHelp">
            <small id="termNameHelp" class="form-text text-muted">Something like 'Trinity 2020'</small>
          </div>

          <div class="form-group">
            <label for="inputTermStartDate">Term Start Date</label>
            <input type="text" class="form-control" name="inputTermStartDate" id="inputTermStartDate" aria-describedby="termStartDate">
            <small id="termStartDate" class="form-text text-muted">2020-01-01</small>
          </div>

          <div class="form-group">
            <label for="inputTermEndDate">Term End Date</label>
            <input type="text" class="form-control" name="inputTermEndDate" id="inputTermEndDate" aria-describedby="termEndDate">
            <small id="termEndDate" class="form-text text-muted">2020-09-30</small>
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
