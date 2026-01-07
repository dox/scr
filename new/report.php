<?php
require_once "inc/autoload.php";
require_once "inc/reports.php";

if (!$user->isLoggedIn() || !$user->hasPermission("reports")) {
	die("User does not have permission to run reports or is not logged in.");
}

// Determine requested report
$reportKey = $_GET['page'] ?? '';
if (!array_key_exists($reportKey, $reports)) {
	die("Unknown report requested.");
}

// Use the format defined in the report array
$format = strtolower($reports[$reportKey]['format']);
$reportFile = __DIR__ . '/reports/' . $reports[$reportKey]['file'];

// CSV output
if ($format === 'csv') {
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="report-' . date('Ymd-His') . '.csv"');

	$output = fopen('php://output', 'w');
	if (!$output) {
		die("Unable to open output for CSV.");
	}

	// Include the CSV report (the report scripts should use $output)
	require_once $reportFile;

	fclose($output);
	exit;
}

// HTML output
ob_start();
require_once $reportFile;
$bodyContent = ob_get_clean();

echo '<!DOCTYPE html>
<html lang="en">
<head>';
require_once __DIR__ . '/layout/html_head.php';
echo '</head>
<body>
<div class="container my-4">
' . $bodyContent . '
</div>
</body>
</html>';
exit;
