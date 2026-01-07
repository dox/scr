<?php
// Example data rows
$rows = [
	['Name', 'Meal', 'Date'],
	['John Smith', 'Dinner', '2025-11-27'],
	['Jane Doe', 'Lunch', '2025-11-26'],
];

foreach ($rows as $row) {
	fputcsv($output, $row);
}