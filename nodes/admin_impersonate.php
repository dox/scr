<?php
$title = "Impersonate";
$subtitle = "Assume the identity of an SCR Member for the purposes of managing their meal bookings";

echo makeTitle($title, $subtitle);
?>

<div class="row justify-content-md-center">
  <div class="col col-lg-4">

      <div class="mb-3">
        <?php
        if (isset($_SESSION['impersonating'])) {
          $selectStatus = " disabled";
        }
        ?>
        <input class="form-control mb-3" <?php echo $selectStatus; ?> type="text" id='impersonate-input' list='members-list' placeholder="Search Members" aria-label="Search">

        <datalist id="members-list">
          <?php
          $membersClass = new members();
          $members = $membersClass->allEnabled();

          foreach ($members AS $member) {
            $memberObject = new member($member['uid']);

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

      <div class="d-grid gap-2">
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
  </div>
</div>


<script>
function impersonateInput() {
  var buttonClicked = document.getElementById('impersonate_submit_button');
  var impersonateInput = document.getElementById('impersonate-input');
  var impersonateHeaderButton = document.getElementById('impersonating_header_button');

  var val = document.getElementById("impersonate-input").value;
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
        impersonateInput.disabled = false;
        impersonateHeaderButton.classList.add("visually-hidden");
    };


  } else {
    buttonClicked.classList.remove("btn-primary");
    buttonClicked.classList.add("btn-warning");
    buttonClicked.innerHTML = "Stop Impersonating";
    buttonClicked.value = "stop";
    impersonateInput.disabled = true;
    impersonateHeaderButton.classList.remove("visually-hidden");
  }


  for (var i = 0; i < opts.length; i++) {
    if (opts[i].value === val) {
      // An item was selected from the list!
      //window.location.href = 'index.php?n=node&meterUID='+opts[i].id;




      var formData = new FormData();
      formData.append("impersonate_ldap", opts[i].id);

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
