<div class="container">
  <div class="px-3 py-3 pt-md-5 pb-md-4 text-center">
    <h1 class="display-4">Impersonate</h1>
    <p class="lead">Assume the identity of an SCR Member for the purposes of managing their meal bookings.</p>
  </div>

Coming soon...

<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
  <?php
  $membersClass = new members();

  $members = $membersClass->all();
  foreach ($members AS $member) {
    $memberObject = new member($member['uid']);

    echo "<option selected>" . $memberObject->displayName() . "</option>";
  }
  ?>
</select>
</div>
