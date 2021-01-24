<?php
admin_gatekeeper();

$notificationsClass = new notifications();
if (isset($_POST['notificationUID'])) {
  //$notificationsClass->update($_POST);
}

$notification = $notificationsClass->one($_GET['notificationUID']);
?>
<?php
$title = $notification['name'];
$subtitle = $notification['message'];

echo makeTitle($title, $subtitle);

?>
<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Notification Dismisses</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($meals); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      $members_array = json_decode($notification['members_array']);

      foreach ($members_array AS $member => $dismissedDate) {
        $memberObject = new member($member);

        $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
        $output .= "<div class=\"text-muted\">";
        $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=admin_meal&mealUID=" . $mealObject->uid . "\" class=\"text-muted\">" . $mealObject->name . "</a></h6>";
        $output .= "<small class=\"text-muted\">" . $memberObject->displayName() . "</small>";
        $output .= "</div>";
        $output .= "<span class=\"text-muted\">" . dateDisplay($dismissedDate) . "</span>";

        $output .= "</li>";

        echo $output;
      }
      ?>
  </div>
  <div class="col-md-7 col-lg-8">
    <h4 class="mb-3">Notification</h4>
    <form method="post" id="termUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
      <div class="row">
        <div class="col-3 mb-3">
          <label for="type" class="form-label">Type</label>
          <select class="form-select" name="type" id="type" required>
            <option value="CSV" <?php if ($notification['type'] == "Primary") { echo " selected"; } ?>>Primary</option>
            <option value="CSV" <?php if ($notification['type'] == "Secondary") { echo " selected"; } ?>>Secondary</option>
            <option value="CSV" <?php if ($notification['type'] == "Success") { echo " selected"; } ?>>Success</option>
            <option value="CSV" <?php if ($notification['type'] == "Danger") { echo " selected"; } ?>>Danger</option>
            <option value="CSV" <?php if ($notification['type'] == "Warning") { echo " selected"; } ?>>Warning</option>
            <option value="CSV" <?php if ($notification['type'] == "Information") { echo " selected"; } ?>>Information</option>
            <option value="CSV" <?php if ($notification['type'] == "Light") { echo " selected"; } ?>>Light</option>
            <option value="CSV" <?php if ($notification['type'] == "Dark") { echo " selected"; } ?>>Dark</option>
          </select>
          <div class="invalid-feedback">
            Valid type is required.
          </div>
        </div>
        <div class="col-9 mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" name="name" id="name" value="<?php echo $notification['name']; ?>" required>
          <div class="invalid-feedback">
            Valid name is required.
          </div>
        </div>
      </div>

      <div class="col mb-3">
        <label for="description" class="form-label">Message</label>
        <textarea class="form-control" name="description" id="description"><?php echo $notification['message']; ?></textarea>
      </div>

      <div class="col mb-3">
        <label class="row">
          <span class="col">Dismissible</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" id="Dismissible" name="Dismissible" <?php if ($notification['dismissible'] == 1) { echo "checked=\"\""; } ?> value="1"></label>
          </span>
        </label>
      </div>

      <hr class="my-4">

      <input type="hidden" name="reportUID" id="notificationUID" value="<?php echo $notification['uid']; ?>">
      <button class="btn btn-primary btn-lg w-100" type="submit" disabled>Update Notification</button>
    </form>
  </div>
</div>
