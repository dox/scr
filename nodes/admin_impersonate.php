<?php
$title = "Impersonate";
$subtitle = "Assume the identity of an SCR Member for the purposes of managing their meal bookings";

echo makeTitle($title, $subtitle);
?>

<div class="row justify-content-md-center">
  <div class="col col-lg-4">
    <form method="post" id="impersonateForm" action="../actions/impersonate.php" onsubmit="return impersonate(this);">
      <div class="mb-3">
        <?php
        if (isset($_SESSION['impersonating'])) {
          $selectStatus = " disabled";
        }
        ?>
        <select <?php echo $selectStatus; ?> class="form-select form-select mb-3" id="impersonate_ldap" name="impersonate_ldap" aria-label="Impersonate select">
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

            echo "<option " . $selectStatus . " value=\"" . $memberObject->ldap . "\">" . $memberObject->displayName() . "</option>";
          }
          ?>
        </select>
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
        <button type="submit" id="impersonate_submit_button" name="impersonate_submit_button" value="<?php echo $value; ?>" class="btn <?php echo $class; ?>"><?php echo $text; ?></button>
      </div>
    </form>
  </div>
</div>
