<?php
pageAccessCheck("reports");

$reportsClass = new reports();
if (isset($_POST['reportUID'])) {
  $reportsClass->update($_POST);
}

$report = $reportsClass->one($_GET['reportUID']);
$reportLogs = $logsClass->allWhereMatch("[reportUID:" . $report['uid'] . "]");
?>
<?php
$title = $report['name'];
$subtitle = $report['description'];

echo makeTitle($title, $subtitle);
?>
<div class="row g-3">
  <div class="col-md-5 col-lg-4 order-md-last">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Report Logs</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($reportLogs); ?></span>
    </h4>
    <ul class="list-group mb-3">
      <?php
      foreach ($reportLogs AS $log) {
        $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
        $output .= "<div class=\"text-muted\">";
        $output .= "<h6 class=\"my-0\"><a href=\"index.php?n=admin_logs\" class=\"text-muted\">" . $log['description'] . "</a></h6>";
        $output .= "<small class=\"text-muted\">" . dateDisplay($log['date']) . " " . timeDisplay($log['date']) . "</small>";
        $output .= "</div>";
        $output .= "<span class=\"text-muted\">" . $log['username'] . "</span>";
        $output .= "</li>";

        echo $output;
      }
      ?>
  </div>
  <div class="col-md-7 col-lg-8">
    <h4 class="mb-3">Report</h4>
    <form method="post" id="termUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
      <div class="row">
        <div class="col-3 mb-3">
          <label for="type" class="form-label">Type</label>
          <select class="form-select" name="type" id="type" required>
            <option value="CSV" <?php if ($report['type'] == "CSV") { echo " selected"; } ?>>CSV</option>
            <option value="PDF" <?php if ($report['type'] == "PDF") { echo " selected"; } ?>>PDF</option>
            <option value="HTML" <?php if ($report['type'] == "HTML") { echo " selected"; } ?>>HTML</option>
          </select>
          <div class="invalid-feedback">
            Valid report type is required.
          </div>
        </div>
        <div class="col-9 mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" name="name" id="name" value="<?php echo $report['name']; ?>" required>
          <div class="invalid-feedback">
            Valid report name is required.
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-3 mb-3">
          <label for="admin_only" class="form-label">Admin Only</label>
          <select class="form-select" name="admin_only" id="admin_only" required>
            <option value="1" <?php if ($report['admin_only'] == "1") { echo " selected"; } ?>>Admin Only</option>
            <option value="0" <?php if ($report['admin_only'] == "0") { echo " selected"; } ?>>Member Access</option>
          </select>
        </div>
        <div class="col-9 mb-3">
          <label for="file" class="form-label">File</label>
          <input type="text" class="form-control" name="file" id="file" value="<?php echo $report['file']; ?>">
        </div>
      </div>

      <div class="col mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" name="description" id="description"><?php echo $report['description']; ?></textarea>
      </div>

      <hr class="my-4">

      <input type="hidden" name="reportUID" id="reportUID" value="<?php echo $report['uid']; ?>">
      <button class="btn btn-primary btn-lg w-100" type="submit">Update Report</button>
    </form>
  </div>
</div>
