<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<?php
pageAccessCheck("members");

$membersClass = new members();

# CHECK IF WE NEED TO CREATE NEW MEMBER FROM FORM SUBMISSION
if (isset($_POST['memberNew'])) {
 $memberObject = new member();
 $memberObject->create($_POST);

 $membersClass = new members();
}

if (isset($_POST['precedence_order'])) {
  $precedenceArray = explode(",", $_POST['precedence_order']);

  $i = 0;
  do {
    $memberObject = new member($precedenceArray[$i]);
    $memberObject->updateMemberPrecendece($i);

  $i++;
  } while ($i < count($precedenceArray));

  $logArray['category'] = "admin";
  $logArray['result'] = "success";
  $logArray['description'] = "Members order updated";
  $logsClass->create($logArray);
}
$scrMembersEnabled = $membersClass->getMembers('enabled', 'scr');
$scrMembersDisabled = $membersClass->getMembers('disabled', 'scr');
$mcrMembersEnabled = $membersClass->getMembers('enabled', 'mcr');
$mcrMembersDisabled = $membersClass->getMembers('disabled', 'mcr');

?>
<?php
$title = "Members";
$subtitle = "Members, and their order of precedence";
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#person-plus\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons, true);
?>

<ul class="nav nav-tabs nav-fill" id="scrUserList" role="tablist">
  <li class="nav-item" role="presentation">
  <a class="nav-link active" id="scr-tab" data-bs-toggle="tab" href="#scr" role="tab" aria-controls="scr" aria-selected="true">SCR (<?php echo count($scrMembersEnabled);?>)</a>
  </li>
  <li class="nav-item" role="presentation">
  <a class="nav-link" id="mcr-tab" data-bs-toggle="tab" href="#mcr" role="tab" aria-controls="mcr" aria-selected="false">MCR (<?php echo count($mcrMembersEnabled);?>)</a>
  </li>
</ul>

<div class="tab-content mt-3" id="membersContent">
  <div class="tab-pane fade show active" id="scr" role="tabpanel" aria-labelledby="scr-tab">
  
  <div class="row mb-3">
    <div class="col">
      <input type="text" id="filterInput" class="form-control form-control-lg" placeholder="Quick search" autocomplete="off" spellcheck="false" aria-describedby="wine_searchHelp">
    </div>
  </div>
  
  <form method="post" id="termForm" action="index.php?n=admin_members">
    <ul class="list-group" id="scr_members_list">
    <?php
    foreach ($scrMembersEnabled as $member) {
      echo $member->memberRow();
    }
    ?>
    </ul>

    <br />
    <button class="btn btn-primary position-sticky bottom-0 start-50 translate-middle-x mb-3" id="submitOrder">Save Order</button>
  </form>

  <hr />
  <h2>Disabled Dining Rights</h2>
  <ul class="list-group">
    <?php
    foreach ($scrMembersDisabled as $member) {
    echo $member->memberRow();
    }
    ?>
  </ul>
  </div>
  <div class="tab-pane fade" id="mcr" role="tabpanel" aria-labelledby="mcr-tab">
  <ul class="list-group" id="mcr_members_list">
    <?php
    foreach ($mcrMembersEnabled as $member) {
    echo $member->memberRow();
    }
    ?>
  </ul>

  <hr />

  <h2>Disabled Dining Rights</h2>
  <ul class="list-group">
    <?php
    foreach ($mcrMembersDisabled as $member) {
     echo $member->memberRow();
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

document.getElementById('termForm').addEventListener('submit', function (e) {
  const listItems = document.querySelectorAll('#scr_members_list li');
  const orderData = [];

  listItems.forEach((li) => {
  const userId = li.getAttribute('data-user-id');
  if (userId) orderData.push(userId);
  });

  // Join as comma-separated to match your PHP logic (explode)
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'precedence_order';
  input.value = orderData.join(',');

  e.target.appendChild(input);
});
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
    <form method="post" id="memberForm" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Add New SCR Member</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="col-12 mb-3">
      <label for="title" class="form-label">Title</label>
      <select class="form-select" name="title" id="title" required>
        <?php
        foreach ($membersClass->memberTitles() as $title) {
        $output = "<option value=\"" . $title . "\">" . $title . "</option>";

        echo $output;
        }
        ?>
      </select>
      <div class="invalid-feedback">
        Title is required.
      </div>
      </div>

      <div class="col-12 mb-3">
      <label for="firstname" class="form-label">First name</label>
      <input type="text" class="form-control" name="firstname" id="firstname" required>
      <div class="invalid-feedback">
        Valid first name is required.
      </div>
      </div>

      <div class="col-12 mb-3">
      <label for="lastname" class="form-label">Last name</label>
      <input type="text" class="form-control" name="lastname" id="lastname" required>
      <div class="invalid-feedback">
        Valid last name is required.
      </div>
      </div>

      <div class="col-12 mb-3">
      <label for="ldap" class="form-label">LDAP Username</label>
      <div class="input-group">
        <span class="input-group-text" onclick="ldapLookup()">@</span>
        <input type="text" class="form-control" name="ldap" id="ldap" required>
      <div class="invalid-feedback">
        LDAP Username is required.
        </div>
      </div>
      </div>

      <div class="col-12 mb-3">
      <label for="category" class="form-label">Member Category</label>
      <select class="form-select" name="category" id="category" required>
        <?php
        foreach ($membersClass->memberCategories() as $category) {
        $output = "<option value=\"" . $category . "\"" . ">" . $category . "</option>";

        echo $output;
        }
        ?>
      </select>
      <div class="invalid-feedback">
        Please select a valid Member Type.
      </div>
      </div>

      <div class="col-12 mb-3">
      <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
      <input type="email" class="form-control" name="email" id="email">
      <div class="invalid-feedback">
        Please enter a valid email address for shipping updates.
      </div>
      </div>

      <div class="col-12 mb-3">
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
    <input type="hidden" name="default_wine_choice" value="<?php echo reset(explode(",", $settingsClass->value('booking_wine_options'))); ?>" />
    <input type="hidden" name="calendar_hash" value="<?php echo bin2hex(random_bytes(12)); ?>" />
    <input type="hidden" name="type" value="SCR" />
    </div>
    </form>
  </div>
  </div>
</div>

<script>
function filterList() {
  // Get input value and convert it to lowercase for case-insensitive matching
  var filterValue = document.getElementById('filterInput').value.toLowerCase();
  
  // Get list items
  var items = document.querySelectorAll('#scr_members_list li');
  
  // Loop through all list items
  for(var i = 0; i < items.length; i++) {
  var item = items[i];
  var text = item.textContent.toLowerCase();
  
  // If the input matches the item, display it, otherwise hide it
  if(text.indexOf(filterValue) !== -1) {
    item.style.display = '';
  } else {
    item.style.display = 'none';
  }
  }
}

// Add event listener to input field
document.getElementById('filterInput').addEventListener('input', filterList);
</script>