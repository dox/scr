<?php
class wine_list {
	protected static $table_name = "wine_lists";
	
	public $uid;
	public $type;
	public $name;
	public $member_ldap;
	public $wine_uids;
	public $notes;
	public $last_updated;
	
	function __construct($cellarUID = null) {
		global $db;
	  
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE uid = '" . $cellarUID . "'";
		
		$results = $db->query($sql)->fetchArray();
		
		foreach ($results AS $key => $value) {
			$this->$key = $value;
		}
	}
	
	
	
	public function isWineInList() {
		
	}
	
	public function wineUIDs() {
		if (!empty($this->wine_uids)) {
			return explode(",", $this->wine_uids);
		} else {
			return array();
		}
	}
	
	public function fullname() {
		$output  = $this->name;
		$output .= " <small>(" . count($this->wineUIDs());
		$output .= autoPluralise(" wine)", " wines)", count($this->wineUIDs()));
		$output .= "</small>";
		
		return $output;
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
		$logArray['description'] = "Created [listUID:" . $db->lastInsertID() . "] (" . $this->name . ")";
		$logsClass->create($logArray);
	}
	
	
}
?>