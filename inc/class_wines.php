<?php
class wineClass {
	protected static $table_cellars = "wine_cellars";
	protected static $table_bins = "wine_bins";
	protected static $table_wines = "wine_wines";
	protected static $table_transactions = "wine_transactions";
	protected static $table_lists = "wine_lists";
	
	public function allCellars($whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT * FROM " . self::$table_cellars;
		
		if (!empty($whereFilterArray)) {
			$conditions = [];
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
		
		$sql .= " ORDER BY name ASC";
		
		$cellars = $db->query($sql)->fetchAll();
		
		return $cellars;
	}
	
	public function allBins($whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT * FROM " . self::$table_bins;
		
		if (!empty($whereFilterArray)) {
			$conditions = [];
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
		
		$sql .= " ORDER BY name ASC";
		
		$bins = $db->query($sql)->fetchAll();
		
		return $bins;
	}
	
	public function allWines($whereFilterArray = null, $inStock = false) {
		global $db;

		$sql  = "SELECT wine_wines.*, wine_bins.cellar_uid FROM " . self::$table_wines;
		$sql .= " LEFT JOIN wine_bins ON wine_wines.bin_uid = wine_bins.uid";
		
		$conditions = [];
		
		if ($inStock == true) {
			$conditions[] = "qty > 0";
		}
		
		if (!empty($whereFilterArray)) {
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
		}
		
		if (!empty($conditions)) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
		
		$sql .= " ORDER BY name ASC";
		
		$wines = $db->query($sql)->fetchAll();
		
		return $wines;
	}
	
	public function allTransactions($whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT * FROM " . self::$table_transactions;
		
		if (!empty($whereFilterArray)) {
			$conditions = [];
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
		
		$sql .= " ORDER BY date DESC";
		
		$transactions = $db->query($sql)->fetchAll();
		
		return $transactions;
	}
	
	public function allLists($whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT * FROM " . self::$table_lists;
		
		if (!empty($whereFilterArray)) {
			$conditions = [];
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
		
		$sql .= " ORDER BY name ASC";
		
		$lists = $db->query($sql)->fetchAll();
		
		return $lists;
	}
	
	public function winesByUIDs($wine_uids_array) {
		global $db;
		
		if (!empty($wine_uids_array)){ 
		  $sql  = "SELECT * FROM wine_wines";
		  $sql .= " WHERE uid IN (" . $wine_uids_array . ")";
		  $sql .= " ORDER BY name ASC";
		  
		  $results = $db->query($sql)->fetchAll();
		} else {
		  return array();
		}
	  
		return $results;
	}
	
	public function searchAllWines($searchArray = null, $cellarUID = null, $limit = null) {
		global $db;
		
		foreach ($searchArray AS $searchKey => $searchString) {
			$searchStatements[] = $searchKey . " LIKE '%" . $searchString . "%'";
		}
		
		$sql  = "SELECT wine_wines.*, wine_bins.cellar_uid";
		$sql .= " FROM wine_wines LEFT JOIN wine_bins ON wine_wines.bin_uid = wine_bins.uid";
		$sql .= " WHERE";
		
		if (isset($cellarUID)) {
			$sql .= " cellar_uid = '" . $cellarUID ."' AND ";
		}
		
		$sql .= "(" . implode(" OR ", $searchStatements) . ")";
		$sql .= " ORDER BY wine_wines.name ASC";
		
		if ($limit) {
			$sql .= " LIMIT " . $limit;
		}
		
		$results = $db->query($sql)->fetchAll();
		
		return $results;
	  }
	
	public function wineBottlesTotal($whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT SUM(qty) AS total FROM " . self::$table_wines;
		
		if (!empty($whereFilterArray)) {
			$conditions = [];
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
		
		$wines = $db->query($sql)->fetchArray();
		
		return $wines['total'];
	}
	
	public function listFromWines($columnName, $whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT DISTINCT " . $columnName . " FROM " . self::$table_wines;
		
		if (!empty($whereFilterArray)) {
			$conditions = [];
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
		
		$sql .= " ORDER BY " . $columnName . " ASC";
		
		$results = $db->query($sql)->fetchAll();
		
		return $results;
	}
	
	public function transactionsTypes() {
		$array['Transaction'] = "deduct";
		$array['Import'] = "import";
		$array['Wastage'] = "deduct";
		
		return $array;
	}
}
?>