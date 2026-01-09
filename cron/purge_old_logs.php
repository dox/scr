<?php
require_once '../inc/autoload.php';

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit("This script can only be run from the command line.\n");
}

// Get the retention period from settings
$days = (int) ($settings->get('logs_retention') ?? 0); // default to 0

// Delete old bookings first
$query = "DELETE FROM logs WHERE `date` < NOW() - INTERVAL ? DAY";
$stmt = $db->query($query, [$days]);
$deleted = $stmt->rowCount();

// write the log
$log->add($deleted . ' logs older than ' . $days . ' days purged', 'System', Log::WARNING);
?>