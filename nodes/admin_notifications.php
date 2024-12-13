<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
pageAccessCheck("notifications");

$notificationsClass = new notifications();

if (isset($_POST['notificationADD'])) {
  if (!isset($_POST['dismissible'])) {
    $_POST['dismissible'] = '0';
  }
  $notificationsClass->create($_POST);
}

if (isset($_GET['notificationDELETE'])) {
 $notificationsClass->delete($_GET['notificationDELETE']);
}

$notifications = $notificationsClass->all();
?>

<?php
$title = "Notifications";
$subtitle = "Messages that appear to all users at the top of the page";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#chat-dots\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#notficationAddModal\"");

echo makeTitle($title, $subtitle, $icons, true);
?>

<div class="list-group">
  <?php
  foreach ($notifications AS $notification) {
    if (date('Y-m-d', strtotime($notification['date_end'])) > date('Y-m-d')) {
      $class = "";
    } else {
      $class = "list-group-item-secondary";
    }
    $output  = "<a href=\"index.php?n=admin_notification&notificationUID=" . $notification['uid'] . "\" class=\"list-group-item list-group-item-action " . $class . "\">";
    $output .= "<div class=\"d-flex w-100 justify-content-between\">";
    $output .= "<h5 class=\"mb-1\">" . $notification['name'] . "</h5>";
    $output .= "<small class=\"text-muted\">" . "<span class=\"badge bg-primary rounded-pill\">" . dateDisplay($notification['date_start']) . " - " . dateDisplay($notification['date_end']) . "</span></small>";
    //$output .= "<p id=\"" . $term['uid'] . "\" onclick=\"dismiss(this.id);\">dismiss this box</p>";
    //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
    $output .= "</div>";
    //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
    $output .= "<small class=\"text-muted\">" . htmlspecialchars($notification['message']) . "</small>";
    $output .= "</a>";

    echo $output;
  }
  ?>
</div>

<!-- Modal -->
<div class="modal fade" id="notficationAddModal" tabindex="-1" aria-labelledby="notficationAddModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="notificationForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="notficationAddModalLabel">Add New Notification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="name">Notification Name</label>
          <input type="text" class="form-control" name="name" id="name" aria-describedby="notficationNameHelp">
          <small id="notficationNameHelp" class="form-text text-muted">This is not displayed to users</small>
        </div>

        <div class="mb-3">
          <label for="name">Notification Type</label>
          <select class="form-select" name="type" id="type" required>
            <option value="Primary">Primary</option>
            <option value="Secondary">Secondary</option>
            <option value="Success">Success</option>
            <option value="Danger">Danger</option>
            <option value="Warning">Warning</option>
            <option value="Information">Information</option>
            <option value="Light">Light</option>
            <option value="Dark">Dark</option>
          </select>
        </div>

          <div class="mb-3">
            <label for="date_start">Notification Start Date</label>
            <div class="input-group">
              <span class="input-group-text" id="date_start-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
              <input type="date" class="form-control" name="date_start" id="date_start" value="<?php echo date('Y-m-d'); ?>" aria-describedby="date_start">
            </div>
          </div>

          <div class="mb-3">
            <label for="date_end">Notification End Date</label>
            <div class="input-group">
              <span class="input-group-text" id="date_end-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
              <input type="date" class="form-control" name="date_end" id="date_end" value="<?php echo date('Y-m-d', strtotime("+1 month")); ?>" aria-describedby="date_end">
            </div>
          </div>

          <div class="mb-3">
            <label for="date_end">Notification Message</label>
            <textarea class="form-control" name="message" id="message"></textarea>
          </div>

          <div class="col mb-3">
            <label class="row">
              <span class="col">Dismissible</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch">
                  <input class="form-check-input" type="checkbox" id="dismissible" name="dismissible" value="1"></label>
              </span>
            </label>
          </div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#chat-dots"/></svg> Add Notification</button>
        <input type="hidden" id="notificationADD" name="notificationADD">
      </div>
      </form>
    </div>
  </div>
</div>