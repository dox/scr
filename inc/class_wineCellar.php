<?php
class cellar {
	protected static $table_name = "wine_cellars";
	
	public $uid;
	public $name;
	public $short_code;
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
	
	public function card() {
		
		$output  = "<div class=\"col-sm-12 col-md-6 mb-3\">";
		$output .= "<div class=\"card shadow-sm\">";
		$output .= "<img src=\"" . $this->photograph . "\" class=\"card-img-top\" alt=\"Cellar photograph\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<p class=\"card-text\">" . $this->name . "</p>";
		$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
		$output .= "<a href=\"index.php?n=wine_cellar&uid=" . $this->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary stretched-link\">View</a>";
		$output .= "<small class=\"text-body-secondary\">";
		$output .= count($this->allBins()) . autoPluralise(" bin", " bins", count($this->allBins()));
		$output .= " / ";
		$output .= $this->allBottles() . autoPluralise(" bottle", " bottles", $this->allBottles());
		$output .= "</small>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
	
	public function allBins($whereFilterArray = null) {
		$wineClass = new wineClass();
		
		if (isset($whereFilterArray)) {
			$array = array_merge($whereFilterArray, array('cellar_uid' => $this->uid));
		} else {
			$array = array('cellar_uid' => $this->uid);
		}
		$bins = $wineClass->allBins($array);
		
		return $bins;
	}
	
	public function allWines($whereFilterArray = null) {
		$wineClass = new wineClass();
		
		if (isset($whereFilterArray)) {
			$array = array_merge($whereFilterArray, array('cellar_uid' => $this->uid));
		} else {
			$array = array('cellar_uid' => $this->uid);
		}
		$wines = $wineClass->allWines($array);
		
		return $wines;
	}
	
	public function allBottles($whereFilterArray = null) {
		$wineClass = new wineClass();
		
		if (isset($whereFilterArray)) {
			$array = array_merge($whereFilterArray, array('cellar_uid' => $this->uid));
		}
		$wines = $this->allWines($array);
		
		$bottles = 0;
		foreach ($wines AS $wine) {
			$bottles = $bottles + $wine['qty'];
		}
		
		return $bottles;
	}
	
	public function allBottlesByGrape() {
		$wineClass = new wineClass();
		
		foreach ($this->allWines() AS $wine) {
			$grapArray[$wine['grape']] = $grapArray[$wine['grape']] + 1;
		}
		
		return $grapArray;
	}
	
	public function binsTable($bins) {
		global $settingsClass;
		
		$output = "";
		foreach (explode(",", $settingsClass->value('wine_category')) AS $wine_category) {
			$bins = $this->allBins(array('category' => $wine_category));
			
			if (count($bins) > 0) {
				$output .= "<h2>" . $wine_category . " bins</h2>";
				$output .= "<table class=\"table\">";
				$output .= "<thead>";
				$output .= "<tr>";
				$output .= "<th scope=\"col\">Bin</th>";
				$output .= "<th scope=\"col\">Wine</th>";
				$output .= "<th scope=\"col\">Handle</th>";
				$output .= "</tr>";
				$output .= "</thead>";
				$output .= "<tbody>";
				
				foreach ($bins AS $bin) {
					$output .= $this->binTableRow($bin);
				}
				
				$output .= "</tbody>";
				$output .= "</table>";
			}
			
		}
		
		return $output;
	}
	
	private function binTableRow($bin) {
		$bin = new bin($bin['uid']);
		
		$output  = "<tr>";
		$output .= "<th scope=\"row\"><a href=\"index.php?n=wine_bin&uid=" . $bin->uid . "\">" . $bin->name . "</a></th>";
		$output .= "<td>" . $bin->currentWineName() . "</td>";
		$output .= "<td>" . $bin->name . "</td>";
		$output .= "</tr>";
		
		return $output;
	}
	
	
	
	
	
	
}
?>