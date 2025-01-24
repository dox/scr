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
		$logArray['description'] = "Created [cellarUID:" . $db->lastInsertID() . "] (" . $this->name . ")";
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
		$sql = "UPDATE wine_cellars SET " . $setString;
		$sql .= " WHERE uid = '" . $this->uid . "' ";
		$sql .= " LIMIT 1";
		
		$update = $db->query($sql);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Updated [binUID:" . $this->uid . "] with fields " . $setString;
		$logsClass->create($logArray);
		
		return true;
	  }
	
	public function photographURL() {
		$image = "img/cards/wine.png";
		
		if (!empty($this->photograph)) {
			if (file_exists($this->photograph)) {
				$image = $this->photograph;
			}
		}
		
		return $image;
	}
	
	public function card() {
		
		$output  = "<div class=\"col-sm-12 col-md-6 mb-3\">";
		$output .= "<div class=\"card shadow-sm\">";
		$output .= "<img src=\"" . $this->photographURL() . "\" class=\"card-img-top\" alt=\"Cellar photograph\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<h5 class=\"card-title\"><i>(" . $this->short_code . ")</i> " . $this->name . "</h5>";
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
		$wines = $wineClass->allWines($array, true);
		
		return $wines;
	}
	
	public function allBottles($whereFilterArray = null) {
		$wineClass = new wineClass();
		
		if (isset($whereFilterArray)) {
			$array = array_merge($whereFilterArray, array('cellar_uid' => $this->uid));
		}
		$wines = $this->allWines($array, true);
		
		$bottles = 0;
		foreach ($wines AS $wine) {
			$wine = new wine($wine['uid']);
			$bottles = $bottles + $wine->currentQty();
		}
		
		return $bottles;
	}
	
	public function allBottlesByGrape() {
		$wineClass = new wineClass();
		
		foreach ($this->allWines() AS $wine) {
			$wine = new wine($wine['uid']);
			$graphArray[$wine->grape] += $wine->currentQty();
		}
		
		return $graphArray;
	}
	
	public function binsTable($bins) {
		global $settingsClass;
		
		$output .= "<table class=\"table\">";
		$output .= "<thead>";
		$output .= "<tr>";
		$output .= "<th style=\"width: 10%;\" scope=\"col\">Bin</th>";
		$output .= "<th scope=\"col\">Wine</th>";
		$output .= "<th style=\"width: 10%;\" scope=\"col\">Vintage</th>";
		$output .= "<th style=\"width: 10%;\" scope=\"col\">Bottles</th>";
		$output .= "</tr>";
		$output .= "</thead>";
		$output .= "<tbody>";
		
		foreach ($bins AS $bin) {
			$output .= $this->binTableRow($bin);
		}
			
		$output .= "</tbody>";
		$output .= "</table>";
		
		return $output;
	}
	
	private function binTableRow($bin) {
		$bin = new bin($bin['uid']);
		$currentWines = $bin->currentWines();
		
		$class = "";
		if (count($bin->currentWines()) == 1) {
			$url = "index.php?n=wine_wine&wine_uid=" . $bin->currentWines()[0]['uid'];
			$title = $bin->currentWineName();
			
			if ($bin->currentWines()[0]['category'] != $bin->category) {
				$title = $title . " <span class=\"text-warning\">(" . $bin->currentWines()[0]['category'] . ")</strong>";
			}
		} elseif (count($bin->currentWines()) == 0) {
			$class = " text-muted";
			$url = "index.php?n=wine_bin&bin_uid=" . $bin->uid;
			$title = $bin->currentWineName();
		} elseif (count($bin->currentWines()) > 0) {
			$class = " text-warning";
			$url = "index.php?n=wine_bin&bin_uid=" . $bin->uid;
			$title = $bin->currentWineName();
		} else {
			$url = "index.php?n=wine_bin&bin_uid=" . $bin->uid;
			$title = $bin->currentWineName();
		}
		
		$output  = "<tr>";
		$output .= "<th scope=\"row\"><a href=\"" . $url . "\">" . $bin->name . "</a></th>";
		$output .= "<td class=\"" . $class . "\">" . $title . "</td>";
		$output .= "<td>" . $bin->currentWineVintage() . "</td>";
		$output .= "<td>" . $bin->currentBottlesCount() . "</td>";
		$output .= "</tr>";
		
		return $output;
	}
	
	
	
	
	
	
}
?>