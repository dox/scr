<?php
$wineClass = new wineClass();

// CSV columns to include
$columns = array(
  "wine_uid",
  "code",
  "cellar",
  "bin",
  "status",
  "name",
  "supplier",
  "supplier_ref",
  "qty",
  "category",
  "grape",
  "country_of_origin",
  "region_of_origin",
  "vintage",
  "stock_value",
  "price_purchase",
  "price_internal",
  "price_external",
  "tasting",
  "notes",
  "photograph"
);


$allWines = $wineClass->allWines();

foreach ($allWines AS $wine) {
  $row = null;
  
  $wine = new wine($wine['uid']);
  $bin = new bin($wine->bin_uid);
  $cellar = new cellar($bin->cellar_uid);

  $row['wine_uid'] = $wine->uid;
  $row['code'] = $wine->code;
  $row['cellar'] = $cellar->name;
  $row['bin'] = $bin->name;
  $row['status'] = $wine->status;
  $row['name'] = $wine->name;
  $row['supplier'] = $wine->supplier;
  $row['supplier_ref'] = $wine->supplier_ref;
  $row['qty'] = $wine->currentQty();
  $row['category'] = $wine->category;
  $row['grape'] = $wine->grape;
  $row['country_of_origin'] = $wine->country_of_origin;
  $row['region_of_origin'] = $wine->region_of_origin;
  $row['vintage'] = $wine->vintage();
  $row['stock_value'] = ($wine->currentQty() * $wine->price_purchase);
  $row['price_purchase'] = $wine->price_purchase;
  $row['price_internal'] = $wine->price_internal;
  $row['price_external'] = $wine->price_external;
  $row['tasting'] = $wine->tasting;
  $row['notes'] = $wine->notes;
  $row['photograph'] = $wine->photograph;

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
