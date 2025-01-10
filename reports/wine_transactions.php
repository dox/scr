<?php
$wineClass = new wineClass();

// CSV columns to include
$columns = array(
  "transaction_uid",
  "transaction_date_created",
  "transaction_date_posted",
  "username",
  "transaction_type",
  "cellar",
  "wine_uid",
  "wine_name",
  "transaction_qty",
  "transaction_price_per_bottle",
  "transaction_total",
  "transaction_name",
  "transaction_description"
);


$allTransactions = $wineClass->allTransactions();

foreach ($allTransactions AS $transaction) {
  $row = null;
  
  $transaction = new transaction($transaction['uid']);
  $wineSnapshot = json_decode($transaction->snapshot);
  
  $wine = new wine($transaction->wine_uid);
  $bin = new bin($wine->bin_uid);
  $cellar = new cellar($bin->cellar_uid);

  $row['transaction_uid'] = $transaction->uid;
  $row['transaction_date_created'] = $transaction->date;
  $row['transaction_date_posted'] = $transaction->date_posted;
  $row['username'] = $transaction->username;
  $row['transaction_type'] = $transaction->type;
  $row['cellar'] = $cellar->name;
  $row['wine_uid'] = $wineSnapshot->uid;
  $row['wine_name'] = $wineSnapshot->name;
  $row['transaction_qty'] = $transaction->bottles;
  $row['transaction_price_per_bottle'] = $transaction->price_per_bottle;
  $row['transaction_total'] = (abs($transaction->bottles) * $transaction->price_per_bottle);
  $row['transaction_name'] = $transaction->name;
  $row['transaction_description'] = $transaction->description;

  $rowArray[] = $row;
}

// Build the CSV from the bookingsArray...
foreach ($rowArray AS $CSVrow) {
  $rowOutput = null;

  foreach ($columns AS $column) {
	if (!empty($CSVrow[$column])) {
	  $rowOutput[] = $CSVrow[$column];
	} else {
	  $rowOutput[] = '';
	}

  }

  $csvOUTPUT[] = $rowOutput;

}

// output the column headings
fputcsv($output, $columns);

// loop over the rows, outputting them
foreach ($csvOUTPUT AS $row) {
  fputcsv($output, $row);
  //printArray($report);

}

$logArray['category'] = "report";
$logArray['result'] = "success";
$logArray['description'] = "[reportUID:" . $report['uid'] . "] run for [memberUID:" . $memberObject->uid . "]";
$logsClass->create($logArray);
?>
