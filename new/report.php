<?php
include_once "inc/autoload.php";

if (!$user->isLoggedIn() || !$user->hasPermission("reports")) {
	die("User does not have permission to run reports or is not logged in.");
}

// Always force CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="report-' . date('Ymd-His') . '.csv"');

// Whitelisted reports that are allowed to run
$allowed = [
	'member_bookings' => 'reports/member_bookings.php',
	'meal_bookings' => 'reports/meal_bookings.php',
	'test' => 'reports/test.php',
];

// Determine requested report
$report = $_GET['page'] ?? '';
if (!array_key_exists($report, $allowed)) {
	die("Unknown report requested.");
}

// Create a CSV output handle for included pages to write to
$output = fopen('php://output', 'w');

// Make $output available inside included scripts
// Include the script that writes rows into $output
include __DIR__ . '/' . $allowed[$report];

fclose($output);
