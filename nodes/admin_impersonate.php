<div class="container">
  <div class="px-3 py-3 pt-md-5 pb-md-4 text-center">
    <h1 class="display-4">Impersonate</h1>
    <p class="lead">Assume the identity of an SCR Member for the purposes of managing their meal bookings.</p>
  </div>
  <div class="row justify-content-md-center">
    <div class="col col-lg-4">
      <form method="post" id="impersonateForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <div class="mb-3">
          <?php
          if (isset($_SESSION['impersonating'])) {
            $selectStatus = " disabled";
          }
          ?>
          <select <?php echo $selectStatus; ?> class="form-select form-select mb-3" name="impersonate_ldap" aria-label="Impersonate select">
            <?php
            $membersClass = new members();

            $members = $membersClass->all();
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
            echo "<button type=\"submit\" class=\"btn btn-warning\">Stop Impersonating</button>";
            echo "<input type=\"hidden\" name=\"stop_impersonating\" value=\"true\">";
          } else {
            echo "<button type=\"submit\" class=\"btn btn-primary\">Impersonate</button>";
          }
          ?>
        </div>
      </form>
    </div>
  </div>
</div>
