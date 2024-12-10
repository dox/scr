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
		$sql .= " AND qty > 0";
		$sql .= " ORDER BY name ASC";
		
		$wines = $db->query($sql)->fetchAll();
		
		return $wines;
	}
	
	public function currentWineName() {
		if (count($this->currentWines()) > 1) {
			return "Multiple Wines (" . count($this->currentWines()) . ")";
		} elseif (count($this->currentWines()) == 0) {
			return "Empty";
		} else {
			return $this->currentWines()[0]['name'];
		}
	}
}
?>