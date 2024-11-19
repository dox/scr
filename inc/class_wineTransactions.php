<?php
class wine_transactions extends wineClass {
  protected static $table_name = "wine_transactions";
  
  public function create($array) {
	global $db, $logsClass;
	
	$wine = new wine($array['wine_uid']);
	$wine->deduct($array['qty']);
	
	// Construct the transaction query
	$sql  = "INSERT INTO " . self::$table_name;
	$sql .= " SET username = '" . $_SESSION['username'] . "',";
	$sql .= " type = '" . $array['type'] . "',";
	$sql .= " cellar_uid = '" . $array['cellar_uid'] . "',";
	$sql .= " wine_uid = '" . $array['wine_uid'] . "',";
	$sql .= " value = '" . $array['value'] . "',";
	$sql .= " description = '" . $array['description'] . "'";
	
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