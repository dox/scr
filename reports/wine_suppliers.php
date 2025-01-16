<?php
$wineClass = new wineClass();

// CSV columns to include
$columns = array(
  "supplier",
  "total_wines",
  "total_bottles",
  "total_value",
);


$allSuppliers = $db->query("SELECT DISTINCT supplier FROM wine_wines ORDER BY supplier ASC")->fetchAll();

foreach ($allSuppliers AS $supplier) {
  $row = null;
  
  $wines = 	$wineClass->allWines(array('supplier' => $supplier['supplier']));  
  
  $total_wines = 0;
  $total_bottles = 0;
  $total_value = 0;
  
  foreach ($wines AS $wine) {
	  $wine = new wine($wine['uid']);
	  
	  $total_wines ++;
	  $total_bottles += $wine->currentQty();
	  $total_value += $wine->stockValue();
	  
  }
  $row['supplier'] = $supplier['supplier'];
  $row['total_wines'] = $total_wines;
  $row['total_bottles'] = $total_bottles;
  $row['total_value'] = $total_value;

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
