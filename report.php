<?php
include_once("inc/autoload.php");

$reportsClass = new reports();
$report = $reportsClass->one($_GET['reportUID']);

if ($report['admin_only'] == 0) {
  // allow the user to access this report
} else {
  admin_gatekeeper();
}

$node = "reports/" . $report['file'];
if (file_exists($node)) {
  $reportsClass->update_lastrun($report['uid']);

  if ($report['type'] == "CSV") {
    // output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
    //echo "<pre>";

    // create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    include_once($node);
  } elseif ($report['type'] == "PDF") {
    include_once("views/html_head.php");

    echo "<div class=\"container\">";
    include_once($node);
    echo "</div>";

  } else {
    echo "Report type '" . $report['type'] . "' not supported.";
    exit();
  }
} else {
  $node = "nodes/404.php";
}
?>
