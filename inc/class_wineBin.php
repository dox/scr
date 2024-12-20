<?php
class bin {
	protected static $table_name = "wine_bins";
	
	public $uid;
	public $cellar_uid;
	public $name;
	public $category;
	public $description;
	
	function __construct($cellarUID = null) {
		global $db;
	  
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE uid = '" . $cellarUID . "'";
		
		$results = $db->query($sql)->fetchArray();
		
		foreach ($results AS $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function currentWines() {
		global $db;
		
		$sql  = "SELECT * FROM wine_wines";
		$sql .= " WHERE bin_uid = '" . $this->uid . "'";
		$sql .= " AND status != 'Closed'";
		$sql .= " ORDER BY name ASC";
		
		$wines = $db->query($sql)->fetchAll();
		
		return $wines;
	}
	
	public function closedWines() {
		global $db;
		
		$sql  = "SELECT * FROM wine_wines";
		$sql .= " WHERE bin_uid = '" . $this->uid . "'";
		$sql .= " AND status = 'Closed'";
		$sql .= " ORDER BY name ASC";
		
		$wines = $db->query($sql)->fetchAll();
		
		return $wines;
	}
	
	public function currentBottlesCount() {
		$qty = 0;
		foreach ($this->currentWines() AS $wine) {
			$wine = new wine($wine['uid']);
			
			$qty = $qty + $wine->currentQty();
		}
		
		return $qty;
	}
	
	
	public function currentWineName() {
		if (count($this->currentWines()) > 1) {
			foreach ($this->currentWines() AS $wine) {
				$wineNames[] = $wine['name'];
			}
			return "Multiple Wines (" . count($this->currentWines()) . ") " . implode(", ", $wineNames);
		} elseif (count($this->currentWines()) == 0) {
			return "Empty";
		} else {
			return $this->currentWines()[0]['name'];
		}
	}
	
	public function create() {
		global $db, $logsClass;
		
		$data = [];
		foreach (get_object_vars($this) as $key => $value) {
			// Skip private or protected properties
			if (property_exists($this, $key) && $key !== 'uid') {
				$data[$key] = "'" . htmlspecialchars($value) . "'";
			}
		}
		
		// Prepare the query
		$columns = implode(", ", array_keys($data));
		$placeholders = implode(", ", $data);
		
		$sql = "INSERT INTO " . self::$table_name . " ($columns) VALUES ($placeholders)";
		
		$db->query($sql);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Created [binUID:" . $db->lastInsertID() . "] (" . $this->name . ")";
		$logsClass->create($logArray);
	}
	
	public function update($array) {
		global $db, $logsClass;
		
		// Initialize the set part of the query
		$setParts = [];
		
		//remove the uid
		unset($array['uid']);
		
		// Loop through the new values array
		foreach ($array as $field => $newValue) {
		  if (is_array($newValue)) {
			$newValue = implode(",", $newValue);
		  }
			// Check if the field exists in the current values and if the values are different
			if ($this->$field != $newValue) {
			  
				// Sanitize the field and value to prevent SQL injection
				$field = htmlspecialchars($field);
				$newValue = htmlspecialchars($newValue);
				// Add to the set part
				$setParts[$field] = "`$field` = '$newValue'";
			}
		}
		
		// If there are no changes, return null
		if (empty($setParts)) {
			return null;
		}
		
		// Combine the set parts into a single string
		$setString = implode(", ", $setParts);
		
		// Construct the final UPDATE query
		$sql = "UPDATE wine_bins SET " . $setString;
		$sql .= " WHERE uid = '" . $this->uid . "' ";
		$sql .= " LIMIT 1";
		
		$update = $db->query($sql);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Updated [binUID:" . $this->uid . "] with fields " . $setString;
		$logsClass->create($logArray);
		
		return true;
	  }
	
	public function delete() {
		global $db, $logsClass;
		
		$existingBinName = $this->name;
		$existingBinUID = $this->uid;
		
		$sql  = "DELETE FROM " . self::$table_name;
		$sql .= " WHERE uid = " . $this->uid;
		$sql .= " LIMIT 1";
		
		$db->query($sql);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "warning";
		$logArray['description'] = "Deleted [binUID:" . $existingBinUID . "] (" . $existingBinName . ")";
		$logsClass->create($logArray);
	}
}
?>