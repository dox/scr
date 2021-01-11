<?php
include_once("inc/autoload.php");

admin_gatekeeper();

$reportsClass = new reports();
$report = $reportsClass->one(1);

// output headers so that the file is downloaded rather than displayed
//header('Content-Type: text/csv; charset=utf-8');
//header('Content-Disposition: attachment; filename=data.csv');
echo "<pre>";

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');


$node = "reports/" . $report['file'];

if (!file_exists($node)) {
  $node = "nodes/404.php";
}
include_once($node);
?>
