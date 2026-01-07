<?php
if (!isset($_SESSION['impersonating'])) {
  pageAccessCheck("impersonate");
}

$title = "Impersonate";
$subtitle = "Assume the identity of an SCR Member for the purposes of managing their meal bookings";

echo makeTitle($title, $subtitle, false, true);

$membersClass = new members();
?>

<div class="row justify-content-md-center">
  <div class="col col-lg-4">

    <div class="mb-2">
    <?php
    if (isset($_SESSION['impersonating'])) {
      $disabledStatus = " disabled ";
    }
    ?>
    <input class="form-control" <?php echo $disabledStatus; ?> type="text" id='impersonate-input' list='members-list' placeholder="Search Members" aria-label="Search">

    <datalist id="members-list">
      <?php
      $members = $membersClass->getMembers('enabled');

      foreach ($members AS $memberObject) {
      if (isset($_SESSION['impersonating']) && $_SESSION['username'] == $memberObject->ldap) {
        $selectStatus = " selected";
      } else {
        $selectStatus = "";
      }

      if ($_SESSION['username'] != $memberObject->ldap) {
        echo "<option id=\"" . $memberObject->ldap . "\" value=\"" . $memberObject->displayName() . "\"></option>";
      }
      }
      ?>
    </datalist>
    </div>
    <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" value="1" id="maintainAdminAccess" name="maintainAdminAccess" checked <?php echo $disabledStatus; ?>>
    <label class="form-check-label" for="maintainAdminAccess">
      Maintain Current Access Level*
    </label>
    </div>

    <div class="d-grid gap-2 mb-5">
    <?php
    if (isset($_SESSION['impersonating'])) {
      $class = "btn-warning";
      $value = "stop";
      $text = "Stop Impersonating";
    } else {
      $class = "btn-primary";
      $value = "";
      $text = "Impersonate";
    }
    ?>
    <button type="submit" id="impersonate_submit_button" onclick='impersonateInput()' name="impersonate_submit_button" value="<?php echo $value; ?>" class="btn <?php echo $class; ?>"><?php echo $text; ?></button>
    <input type="hidden" id="1" name="2">
    </div>

    <div class="d-grid gap-2">
    <p><small>*By default when you impersonate another member, you maintain your current level of access.  Uncheck this option to see this site with the same permissions as the other member.</small></p>
    </div>
  </div>
</div>



<script>
function impersonateInput() {
  var buttonClicked = document.getElementById('impersonate_submit_button');
  var impersonateInput = document.getElementById('impersonate-input');
  var impersonateMaintainAdmin = document.getElementById('maintainAdminAccess');
  var impersonateHeaderButton = document.getElementById('impersonating_header_button');

  var val = document.getElementById("impersonate-input").value;
  var maintainAdminAccess = document.getElementById("maintainAdminAccess").checked;

  var opts = document.getElementById('members-list').childNodes;

  if (buttonClicked.value == "stop") {
  var formDataStop = new FormData();
  formDataStop.append("impersonate_submit_button", "stop");
  var requestStop = new XMLHttpRequest();
  requestStop.open("POST", "../actions/impersonate.php", true);
  requestStop.send(formDataStop);

  requestStop.onload = function() {
    buttonClicked.classList.remove("btn-warning");
    buttonClicked.classList.add("btn-primary");
    buttonClicked.innerHTML = "Impersonate";
    buttonClicked.value = "";
    impersonateMaintainAdmin.disabled = false;
    impersonateInput.disabled = false;
    impersonateHeaderButton.classList.add("visually-hidden");
  };


  }

  for (var i = 0; i < opts.length; i++) {
  if (opts[i].value === val) {
    var formData = new FormData();
    formData.append("impersonate_ldap", opts[i].id);
    formData.append("maintainAdminAccess", maintainAdminAccess);

    var request = new XMLHttpRequest();

    request.open("POST", "../actions/impersonate.php", true);
    request.send(formData);

    // 4. This will be called after the response is received
    request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else { // show the result
      //alert(`Done, got ${request.response.length} bytes`); // response is the server response
      buttonClicked.classList.remove("btn-primary");
      buttonClicked.classList.add("btn-warning");
      impersonateMaintainAdmin.disabled = true;
      buttonClicked.innerHTML = "Stop Impersonating";
      buttonClicked.value = "stop";
      impersonateInput.disabled = true;
      impersonateHeaderButton.classList.remove("visually-hidden");


    }
    };

    request.onerror = function() {
    alert("Request failed");
    };


    break;
  }
  }
}
</script>
