<?php
class transaction {
	protected static $table_name = "wine_transactions";
	
	public $uid;
	public $date;
	public $date_posted;
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
			$logArray['description'] = "Attempted to create a transaction for [wineUID:" . $wine->uid . "] but didn't know what transaction type " . $array['type'] . " was.  Qty bottles: " . $array['bottles'];
			$logsClass->create($logArray);
		}
		
		// Construct the transaction query
		$sql  = "INSERT INTO " . self::$table_name;
		$sql .= " SET username = '" . $_SESSION['username'] . "',";
		$sql .= " type = '" . $array['type'] . "',";
		$sql .= " date_posted = '" . date('Y-m-d', strtotime($array['date_posted'])) . "',";
		$sql .= " cellar_uid = '" . $cellar->uid . "',";
		$sql .= " name = '" . htmlspecialchars($array['name']) . "',";
		$sql .= " description = '" . htmlspecialchars($array['description']) . "',";
		$sql .= " wine_uid = '" . $wine->uid . "',";
		$sql .= " bottles = '" . $bottles . "',";
		$sql .= " price_per_bottle = '" . $array['price_per_bottle'] . "',";
		$sql .= " snapshot = '" . str_replace("'", "", json_encode($wine)) . "'";
		
		$create_transaction = $db->query($sql);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Created new wine transaction for [wineUID:" . $wine->uid . "].  Bottles: " . $bottles . " / Type: " . $array['type'];
		$logsClass->create($logArray);
		
		return true;
	}
	
	public function delete() {
		global $db, $logsClass;
		
		$existingTransactionName = $this->name;
		$existingTransactionUID = $this->uid;
		
		$sql  = "DELETE FROM " . self::$table_name;
		$sql .= " WHERE uid = " . $this->uid;
		$sql .= " LIMIT 1";
		
		$db->query($sql);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "warning";
		$logArray['description'] = "Deleted [transactionUID:" . $existingTransactionUID . "] (" . $existingTransactionName . ")";
		$logsClass->create($logArray);
	}
	
	public function typeBadge() {
		$output = "<span class=\"badge rounded-pill text-bg-info\">" . $this->type . "</span>";
		
		return $output;
	}
	
	public function transactionsTable($transactions) {
		global $settingsClass;
		
		$output = "";
		
		if (count($transactions) > 0) {
			$output .= "<table class=\"table table-responsive\">";
			$output .= "<thead>";
			$output .= "<tr>";
			$output .= "<th style=\"width: 18%;\" scope=\"col\">Date</th>";
			$output .= "<th style=\"width: 18%;\" scope=\"col\">Username</th>";
			$output .= "<th style=\"width: 10%;\" scope=\"col\">Bottles</th>";
			$output .= "<th style=\"width: 18%;\" scope=\"col\">£/Bottle</th>";
			$output .= "<th scope=\"col\">Name</th>";
			$output .= "<th style=\"width: 8%;\" scope=\"col\"></th>";
			$output .= "</tr>";
			$output .= "</thead>";
			$output .= "<tbody>";
			
			foreach ($transactions AS $transaction) {
				$output .= $this->transactionTableRow($transaction);
			}
			
			$output .= "</tbody>";
			$output .= "</table>";
		}
		
		return $output;
	}
	
	private function transactionTableRow($transaction) {
		$transaction = new transaction($transaction['uid']);
		
		$rowClass = "";
		if (date('Y-m-d', strtotime($transaction->date_posted)) > date('Y-m-d')) {
			$rowClass = "table-info";
		}
		
		if ($transaction->bottles < 0) {
			$bottlesClass = "text-danger";
		} elseif($transaction->bottles > 0) {
			$bottlesClass = "text-success";
		} else {
			$bottlesClass = "";
		}
		
		$iconLink = "<a href=\"index.php?n=wine_transaction&uid=" . $transaction->uid . "\" class=\"float-end\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#info-circle\"></use></svg></a>";
		$output  = "<tr class=\"" . $rowClass . "\">";
		$output .= "<th scope=\"row\">" . dateDisplay($transaction->date_posted) . "</th>";
		$output .= "<td>" . $transaction->username . "</td>";
		$output .= "<td class=\"" . $bottlesClass . "\">" . $transaction->bottles . "</td>";
		$output .= "<td>" . currencyDisplay($transaction->price_per_bottle) . "</td>";
		$output .= "<td>" . $transaction->typeBadge() . " " . $transaction->name . "</td>";
		$output .= "<td>" . $iconLink . "</td>";
		
		$output .= "</tr>";
		
		return $output;
	}
	
	public function redOrGreen($value) {
		
	}
}
?>