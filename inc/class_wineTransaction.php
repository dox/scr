<?php
class transaction {
	protected static $table_name = "wine_transactions";
	
	public $uid;
	public $date;
	public $username;
	public $type;
	public $cellar_uid;
	public $wine_uid;
	public $bottles;
	public $price_per_bottle;
	public $name;
	public $description;
	public $snapshot;
	
	function __construct($transactionUID = null) {
		global $db;
	  
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE uid = '" . $transactionUID . "'";
		
		$results = $db->query($sql)->fetchArray();
		
		foreach ($results AS $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function create($array) {
		global $db, $logsClass;
		
		$wine = new wine($array['wine_uid']);
		$bin = new bin($wine->bin_uid);
		$cellar = new cellar($bin->cellar_uid);
		$wineClass = new wineClass();
		
		// work out if this is an import, or a deduction
		$transactionTypes = $wineClass->transactionsTypes();
		if ($transactionTypes[$array['type']] == "import") {
			$bottles = abs($array['bottles']);
		} elseif ($transactionTypes[$array['type']] == "deduct") {
			$bottles = -$array['bottles'];
		} else {
			$logArray['category'] = "wine";
			$logArray['result'] = "danger";
			$logArray['description'] = "Attempted to create a transaction for [wineUID:" . $wine->uid . "] but didn't know what qty bottles: " . $array['bottles'];
			$logsClass->create($logArray);
		}
		
		// Construct the transaction query
		$sql  = "INSERT INTO " . self::$table_name;
		$sql .= " SET username = '" . $_SESSION['username'] . "',";
		$sql .= " type = '" . $array['type'] . "',";
		$sql .= " cellar_uid = '" . $cellar->uid . "',";
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
}
?>