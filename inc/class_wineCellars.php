<?php
class cellar extends wineClass {
  protected static $table_name = "wine_cellars";
  
  function __construct($cellarUID = null) {
	global $db;
  
	$sql  = "SELECT * FROM " . self::$table_name;
	$sql .= " WHERE uid = '" . $cellarUID . "'";
  
	$results = $db->query($sql)->fetchArray();
  
	foreach ($results AS $key => $value) {
	  $this->$key = $value;
	}
  }
  
  public function getBins($includeEmpty = false) {
	global $db;
  
	$sql  = "SELECT * FROM wine_wines";
	$sql .= " WHERE cellar_uid = '" . $this->uid . "'";
	if ($includeEmpty != true) {
	  $sql .= " AND qty > 0";
	}
  
	$results = $db->query($sql)->fetchAll();
  
	return $results;
  }
  
  public function getAllWinesByBin($bin = null) {
	global $db;
  
	$sql  = "SELECT * FROM wine_wines";
	$sql .= " WHERE cellar_uid = '" . $this->uid . "'";
	$sql .= " AND bin = '" . $bin . "'";
	$sql .= " ORDER BY name ASC";
  
	$results = $db->query($sql)->fetchAll();
  
	return $results;
  }
  
  public function getAllWineBottlesTotal() {
	  global $db;
	
	  $sql  = "SELECT SUM(qty) AS total_wines FROM wine_wines";
	  $sql .= " WHERE cellar_uid = '" . $this->uid . "'";
	  
	  $results = $db->query($sql)->fetchArray();
	  
	  if (!$results['total_wines'] > 0) {
		$results['total_wines'] = 0;
	  }
	  
	  return $results['total_wines'];
	}
	
	public function getAllWineBottlesByCategoryTotal($category) {
		global $db;
	  
		$sql  = "SELECT * FROM wine_wines";
		$sql .= " WHERE cellar_uid = '" . $this->uid . "'";
		$sql .= " AND category = '" . $category . "'";
		
		$results = $db->query($sql)->fetchAll();
				
		return $results;
	  }
  
  public function totalPurchaseValue() {
	global $db;
	
	$sql  = "SELECT sum(total) AS total FROM ";
	$sql .= " (SELECT wine_wines.qty * wine_wines.price_purchase AS total FROM `wine_wines`";
	$sql .= " WHERE cellar_uid = '" . $this->uid . "') tmp";
	
	$results = $db->query($sql)->fetchArray();
	
	if (!$results['total'] > 0) {
	  $results['total'] = 0;
	}
	
	return $results['total'];
	
	
  }
}
?>