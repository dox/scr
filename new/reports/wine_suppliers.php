<?php
// Get all wines (excluding closed)
$winesClass = new Wines();
$wines = $winesClass->wines([
	'wine_wines.status' => ['<>', 'Closed']
]);

// CSV header row
$rowHeaders = [
	'supplier',
	'total_current_wines',
	'total_current_bottles',
	'total_current_value',
];
fputcsv($output, $rowHeaders);

// Tally counts and values by supplier
$suppliers = [];

foreach ($wines as $wine) {
	$supplier = $wine->supplier ?? 'Unknown';

	if (!isset($suppliers[$supplier])) {
		$suppliers[$supplier] = [
			'total_current_wines'   => 0,
			'total_current_bottles' => 0,
			'total_current_value'   => 0.0,
		];
	}

	$suppliers[$supplier]['total_current_wines']++;

	// If you have a 'bottles' property, replace '1' with $wine->bottles
	$bottles = $wine->currentQty();
	$suppliers[$supplier]['total_current_bottles'] += $bottles;

	// Multiply 'price_internal' by bottles for total value
	$price = floatval($wine->price_internal ?? 0);
	$suppliers[$supplier]['total_current_value'] += $price * $bottles;
}

// Sort suppliers by total value descending
uasort($suppliers, fn($a, $b) => $b['total_current_value'] <=> $a['total_current_value']);

// Output CSV rows
foreach ($suppliers as $supplierName => $totals) {
	fputcsv($output, [
		'supplier'              => $supplierName,
		'total_current_wines'   => $totals['total_current_wines'],
		'total_current_bottles' => $totals['total_current_bottles'],
		'total_current_value'   => number_format($totals['total_current_value'], 2),
	]);
}
