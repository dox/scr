<?php
class wine_transactions extends wineClass {
  protected static $table_name = "wine_transactions";
  
  public function create($array) {
	global $db, $logsClass;
	
	$wine = new wine($array['wine_uid']);
	
	
	// check if we are deducting, or adding bottles
	if ($array['type'] == "transaction" || $array['type'] == "wastage") {
		$wine->deduct($array['bottles']);
		$bottles = $array['bottles'] <= 0 ? $array['bottles'] : -$array['bottles'];
	} else {
		$wine->import($array['bottles']);
		$bottles = $array['bottles'];
	}
	
	
	// Construct the transaction query
	$sql  = "INSERT INTO " . self::$table_name;
	$sql .= " SET username = '" . $_SESSION['username'] . "',";
	$sql .= " type = '" . $array['type'] . "',";
	$sql .= " cellar_uid = '" . $wine->cellar_uid . "',";
	$sql .= " wine_uid = '" . $wine->uid . "',";
	$sql .= " bottles = '" . $bottles . "',";
	$sql .= " price_per_bottle = '" . $array['price_per_bottle'] . "',";
	$sql .= " description = '" . $array['description'] . "', ";
	$sql .= " snapshot = '" . str_replace("'", "", json_encode($wine)) . "'";
	
	$create_transaction = $db->query($sql);
	
	$logArray['category'] = "wine";
	$logArray['result'] = "success";
	$logArray['description'] = "Created new wine transaction with fields TBC";
	$logsClass->create($logArray);
	
	return true;
  }
  
  public function getTransaction($uid) {
	global $db;
  
	$sql  = "SELECT * FROM " . self::$table_name;
	$sql .= " WHERE uid = '" . $uid ."'";
  
	$result = $db->query($sql)->fetchArray();
	
	return $result;
  }
  
  public function getAllTransactions() {
	global $db;
  
	$sql  = "SELECT * FROM " . self::$table_name;
	$sql .= " ORDER BY date DESC";
  
	$results = $db->query($sql)->fetchAll();
	
	return $results;
  }
  
  public function getAllTransactionsForWine($uid = null) {
	global $db;
  
	$sql  = "SELECT * FROM " . self::$table_name;
	$sql .= " WHERE wine_uid = '" . $uid . "'";
	$sql .= " ORDER BY date DESC";
  
	$results = $db->query($sql)->fetchAll();
	
	return $results;
  }
  
}
?>