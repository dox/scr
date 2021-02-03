<link rel="stylesheet" href="css/flatpickr.min.css">
<script src="js/flatpickr.js"></script>

<?php
admin_gatekeeper();

$notificationsClass = new notifications();

if (isset($_POST['notificationUID'])) {
  if (!isset($_POST['dismissible'])) {
    $_POST['dismissible'] = '0';
  }
  $notificationsClass->update($_POST);
}

$notification = $notificationsClass->one($_GET['notificationUID']);

$members_dismissed_array = json_decode($notification['members_array']);
?>
<?php
$title = $notification['name'];
$subtitle = $notification['message'];
$icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Notification", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteNotificationModal\"");

echo makeTitle($title, $subtitle, $icons);

?>
<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span>Notification Dismisses</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($members_dismissed_array); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      foreach ($members_dismissed_array AS $member => $dismissedDate) {
        $memberObject = new member($member);

        $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
        $output .= "<div class=\"text-muted\">";
        $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=member&memberUID=" . $memberObject->uid . "\" class=\"text-muted\">" . $memberObject->displayName() . "</a></h6>";
        //$output .= "<span class=\"text-muted\">" . $memberObject->displayName() . "</span>";
        $output .= "</div>";
        $output .= "<small class=\"text-muted\">" . dateDisplay($dismissedDate) . " " . timeDisplay($dismissedDate) . "</small>";

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
            <option value="Primary" <?php if ($notification['type'] == "Primary") { echo " selected"; } ?>>Primary</option>
            <option value="Secondary" <?php if ($notification['type'] == "Secondary") { echo " selected"; } ?>>Secondary</option>
            <option value="Success" <?php if ($notification['type'] == "Success") { echo " selected"; } ?>>Success</option>
            <option value="Danger" <?php if ($notification['type'] == "Danger") { echo " selected"; } ?>>Danger</option>
            <option value="Warning" <?php if ($notification['type'] == "Warning") { echo " selected"; } ?>>Warning</option>
            <option value="Information" <?php if ($notification['type'] == "Information") { echo " selected"; } ?>>Information</option>
            <option value="Light" <?php if ($notification['type'] == "Light") { echo " selected"; } ?>>Light</option>
            <option value="Dark" <?php if ($notification['type'] == "Dark") { echo " selected"; } ?>>Dark</option>
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

      <div class="row">
        <div class="col-6">
          <div class="mb-3">
            <label for="date_start">Notification Start Date</label>
            <div class="input-group">
              <span class="input-group-text" id="date_start-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
              <input type="text" class="form-control" name="date_start" id="date_start" value="<?php echo $notification['date_start']; ?>" aria-describedby="date_start">
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="mb-3">
            <label for="date_end">Notification End Date</label>
            <div class="input-group">
              <span class="input-group-text" id="date_end-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
              <input type="text" class="form-control" name="date_end" id="date_end" value="<?php echo $notification['date_end']; ?>" aria-describedby="date_end">
            </div>
          </div>
        </div>
      </div>




      <div class="col mb-3">
        <label for="description" class="form-label">Message</label>
        <textarea class="form-control" name="message" id="message"><?php echo $notification['message']; ?></textarea>
      </div>

      <div class="col mb-3">
        <label class="row">
          <span class="col">Dismissible</span>
          <span class="col-auto">
            <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" id="dismissible" name="dismissible" <?php if ($notification['dismissible'] == 1) { echo "checked=\"\""; } ?> value="1"></label>
          </span>
        </label>
      </div>

      <hr class="my-4">

      <input type="hidden" name="notificationUID" id="notificationUID" value="<?php echo $notification['uid']; ?>">
      <button class="btn btn-primary btn-lg w-100" type="submit">Update Notification</button>
    </form>
  </div>
</div>

<!-- Modal -->
<div class="modal" tabindex="-1" id="deleteNotificationModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Notification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this notification?</p>
        <p class="text-danger"><strong>WARNING!</strong> This action cannot be undone!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <a href="index.php?n=admin_notifications&notificationDELETE=<?php echo $notification['uid']; ?>" role="button" class="btn btn-danger"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#trash"/></svg> Delete</a>
      </div>
    </div>
  </div>
</div>

<script>
var fp = flatpickr("#date_start", {
  dateFormat: "Y-m-d"
})

var fp2 = flatpickr("#date_end", {
  dateFormat: "Y-m-d"
})
</script>
