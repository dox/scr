<?php
// Get date range from POST
$start = filter_input(INPUT_POST, 'from_date', FILTER_DEFAULT);
$end   = filter_input(INPUT_POST, 'to_date', FILTER_DEFAULT);

if (!$start || !$end) {
	die('Invalid or missing date range.');
}

// Optional: enforce YYYY-MM-DD format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) ||
	!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
	die('Invalid date format.');
}

// Get transactions in date range
$winesClass = new Wines();
$transactions = $winesClass->transactions([
	'date_posted' => [
		['>=', $start],
		['<=', $end],
	]
]);

// CSV header row (matching Transaction class vars)
$rowHeaders = [
	'uid',
	'date',
	'date_posted',
	'username',
	'type',
	'cellar_uid',
	'wine_uid',
	'bottles',
	'price_per_bottle',
	'name',
	'description',
	'linked',
	'total_bottles',
	'total_value',
];
fputcsv($output, $rowHeaders);

// Iterate transactions and output each as a CSV row
if (!empty($transactions) && is_iterable($transactions)) {
	foreach ($transactions as $transaction) {

		$row = [
			'uid'              => $transaction->uid ?? '',
			'date'             => $transaction->date ?? '',
			'date_posted'      => $transaction->date_posted ?? '',
			'username'         => $transaction->username ?? '',
			'type'             => $transaction->type ?? '',
			'cellar_uid'       => $transaction->cellar_uid ?? '',
			'wine_uid'         => $transaction->wine_uid ?? '',
			'bottles'          => $transaction->bottles ?? 0,
			'price_per_bottle' => number_format($transaction->price_per_bottle ?? 0, 2),
			'name'             => $transaction->name ?? '',
			'description'      => $transaction->description ?? '',
			'linked'           => $transaction->linked ?? '',
			'total_bottles'    => $transaction->totalBottles(),
			'total_value'      => number_format($transaction->totalValue(), 2),
		];

		fputcsv($output, $row);
	}
}
