<?php
// Get all wines
$winesClass = new Wines();
$wines = $winesClass->wines();

// CSV header row
$rowHeaders = [
	'uid',
	'date_created',
	'date_updated',
	'code',
	'bin_uid',
	'status',
	'name',
	'supplier',
	'supplier_ref',
	'category',
	'grape',
	'country_of_origin',
	'region_of_origin',
	'vintage',
	'qty',
	'price_purchase',
	'price_internal',
	'price_external',
	'stock_value',
	'tasting',
	'notes',
	'photograph',
];
fputcsv($output, $rowHeaders);

// Iterate wines and output each as a CSV row
if (!empty($wines) && is_iterable($wines)) {
	foreach ($wines as $wine) {
		$qty = $wine->currentQty() ?? 0;
		$stockValue = number_format($qty * ($wine->price_purchase ?? 0), 2);
		
		$row = [
			'uid'               => $wine->uid ?? '',
			'date_created'      => $wine->date_created ?? '',
			'date_updated'      => $wine->date_updated ?? '',
			'code'              => $wine->code ?? '',
			'bin_uid'           => $wine->bin_uid ?? '',
			'status'            => $wine->status ?? '',
			'name'              => $wine->name ?? '',
			'supplier'          => $wine->supplier ?? '',
			'supplier_ref'      => $wine->supplier_ref ?? '',
			'category'          => $wine->category ?? '',
			'grape'             => $wine->grape ?? '',
			'country_of_origin' => $wine->country_of_origin ?? '',
			'region_of_origin'  => $wine->region_of_origin ?? '',
			'vintage'           => $wine->vintage ?? '',
			'qty'               => $qty,
			'price_purchase'    => number_format($wine->price_purchase ?? '', 2),
			'price_internal'    => number_format($wine->price_internal ?? '', 2),
			'price_external'    => number_format($wine->price_external ?? '', 2),
			'stock_value'       => $stockValue,
			'tasting'           => $wine->tasting ?? '',
			'notes'             => $wine->notes ?? '',
			'photograph'        => $wine->photograph ?? '',
		];

		fputcsv($output, $row);
	}
}
