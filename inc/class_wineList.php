<?php
class wine_list {
	protected static $table_name = "wine_lists";
	
	public $uid;
	
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
}
?>