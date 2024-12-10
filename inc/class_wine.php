<?php
class wine {
	protected static $table_name = "wine_wines";
	
	public $uid;
	public $code;
	public $cellar_uid;
	public $bin_uid;
	public $status;
	public $name;
	public $supplier;
	public $supplier_ref;
	public $qty;
	public $category;
	public $grape;
	public $country_of_origin;
	public $region_of_origin;
	public $vintage;
	public $price_purchase;
	public $price_internal;
	public $price_external;
	public $tasting;
	public $notes;
	public $photograph;
	
	function __construct($cellarUID = null) {
		global $db;
	  
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE uid = '" . $cellarUID . "'";
		
		$results = $db->query($sql)->fetchArray();
		
		foreach ($results AS $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function clean_name($full = false) {
		$output  = $this->name;
		
		if ($full == true) {
		  $cellar = new cellar($this->cellar_uid);
		  
		  $output = $cellar->name . " > " . $this->name;
		} else {
		  $output  = $this->name;
		}
		
		return $output;
	}
	
	public function photographURL() {
		$image = "img/blank.jpg";
		
		if (!empty($this->photograph)) {
			$image = "img/wines/" . $this->photograph;
		}
		
		return $image;
	}
	
	public function pricePerBottle($target = "Internal") {
		if ($target == "Internal") {
		  $value = $this->price_internal;
		} elseif ($target == "External") {
		  if (isset($this->price_external)) {
			$value = $this->price_external;
		  } else {
			$value = $this->price_internal;
		  }
		} elseif ($target == "Purchase") {
		  $value = $this->price_purchase;
		} else {
		  $value = 999;
		}
	  
		return $value;
	}
	
	public function transactions() {
		$wineClass = new wineClass();
		
		$transactions = $wineClass->allTransactions(array('wine_uid' => $this->uid));
		
		return $transactions;
	}
	
	public function logs() {
		global $db;
		
		$sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, result, category, description  FROM logs";
		$sql .= " WHERE category = 'wine'";
		$sql .= " AND description LIKE '%[wineUID:" . $this->uid . "]%'";
		$sql .= " ORDER BY date DESC";
		
		$results = $db->query($sql)->fetchAll();
		
		return $results;
	}
	
	public function card() {
		$cellar = new cellar($this->cellar_uid);
		
		if ($this->status == "In-Bond") {
		  $binName = "<a href=\"" . $url . "\" type=\"button\" class=\"btn btn-primary position-relative\">" . $cellar->short_code . " > " . $cellar->name . "<span class=\"position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning\">In-Bond</span></a>";
		  $cardClass = " border-warning ";
		} else {
		  $binName = "<a href=\"" . $url . "\" type=\"button\" class=\"btn btn-primary position-relative\">" . $cellar->short_code . " > " . $this->bin . "</a>";
		  $cardClass = "";
		}
		
		$output  = "<div class=\"col\">";
		$output .= "<div class=\"card " . $cardClass . " shadow-sm\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<h5 class=\"card-title\">" . $binName . "</h5>";
		$output .= "<p class=\"card-text text-truncate\">" . $description . "</p>";
		$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
		$output .= "<div class=\"btn-group\">";
		$output .= "<a href=\"index.php?n=wine_search&filter=code&value=" . $this->code . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">" . $this->code . "</a>";
		$output .= "<a href=\"index.php?n=wine_search&filter=vintage&value=" . $this->vintage . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">" . $this->vintage . "</a>";
		$output .= "</div>";
		$output .= "<small class=\"text-body-secondary\">" . $this->qty . autoPluralise(" bottle", " bottles", $this->qty) . " </small>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
}
?>