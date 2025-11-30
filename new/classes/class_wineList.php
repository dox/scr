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
	
	public function isWineInList($uid) {
		if (in_array($uid, $this->wineUIDs())) {
			return true;
		} else {
			return false;
		}
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
		$output .= " <small>Updated " . timeago($this->last_updated) . "</small>";
		/*$output .= " <small>(" . count($this->wineUIDs());
		$output .= autoPluralise(" wine)", " wines)", count($this->wineUIDs()));
		$output .= "</small>";*/
		
		return $output;
	}
	
	public function heartButton($wineUID = null) {
		if ($this->isWineInList($wineUID)) {
			$heartIcon = "heart-full";
			$heartClass = " style=\"color: red;\"";
		} else {
			$heartIcon = "heart-empty";
			$heartClass = "";
		}
		
		$output  = "<svg width=\"1em\" height=\"1em\" " . $heartClass . "><use xlink:href=\"img/icons.svg#" . $heartIcon . "\"></use></svg>";
		
		return $output;
	}
	
	public function badge() {
		$class = "text-bg-secondary";
		
		if (count($this->wineUIDs()) > 0) {
			$class = "text-bg-primary";
		}
		
		$badge = "<span class=\"badge " . $class . " rounded-pill\">" . count($this->wineUIDs()) . "</span>";
		
		if (count($this->wineUIDs()) > 0) {
			$badge = "<a href=\"index.php?n=wine_search&filter=list&value=" . $this->uid . "\">" . $badge . "</a>";
		}
		
		return $badge;
	}
	
	public function liItem ($wineUID = null) {
		$listURL = "index.php?n=wine_list&uid=" . $this->uid;
		
		if (isset($wineUID)) {
			$heartAndTitle  = "<a href\"#\" onClick=\"toggleListButton(this)\" data-listuid=\"" . $this->uid . "\" data-wineuid=\"" . $wineUID . "\">" . $this->heartButton($wineUID) . " " . $this->fullname() . "</a>";
		} else {
			$heartAndTitle  = "<a href\"#\">" . $this->heartButton() . " " . $this->fullname() . "</a>";
		}
		  
		$output  = "<li class=\"list-group-item d-flex justify-content-between align-items-center\">";
		$output .= "<span>" . $heartAndTitle . "</span>";
		$output .= $this->badge();
		$output .= "</li>";
		
		echo $output;
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
	
	public function update($array) {
		global $db, $logsClass;
		// Initialize the set part of the query
		$setParts = [];
		
		//remove the uid
		unset($array['uid']);
		printArray($array);
		// Loop through the new values array
		foreach ($array as $field => $newValue) {
		  if (is_array($newValue)) {
			$newValue = implode(",", $newValue);
			printArray($newValue);
		  }
			// Check if the field exists in the current values and if the values are different
			if ($this->$field != $newValue) {
				// Sanitize the field and value to prevent SQL injection
				$field = htmlspecialchars($field);
				$newValue = trim(htmlspecialchars($newValue));
				// Add to the set part
				$setParts[$field] = "`$field` = '$newValue'";
			}
		}
		
		// If there are no changes, return null
		if (empty($setParts)) {
			return null;
		}
		
		// Combine the set parts into a single string
		$setParts['last_updated'] = "`last_updated` = '" . date('c') . "'";
		$setString = implode(", ", $setParts);
		
		// Construct the final UPDATE query
		$sql = "UPDATE wine_lists SET " . $setString;
		$sql .= " WHERE uid = '" . $this->uid . "' ";
		$sql .= " LIMIT 1";
		
		echo $sql;
		
		$update = $db->query($sql);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Updated [listUID:" . $this->uid . "] with fields " . $setString;
		$logsClass->create($logArray);
		
		return true;
	}
	
	
}
?>