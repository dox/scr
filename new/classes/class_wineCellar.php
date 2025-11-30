<?php
class cellarOLD {
	
	public function binTypes() {
		$types = explode(",", $this->bin_types);
		
		foreach ($types AS $type) {
			$returnArray[] = trim($type);
		}
		return $returnArray;
	}
	
	public function photographURL() {
		$imageDir = "img/cards/";
		$defaultImage = $imageDir . "wine.png";
		
		if (!empty($this->photograph)) {
			if (file_exists($imageDir . $this->photograph)) {
				$image = $imageDir . $this->photograph;
			}
		}
		
		return $image;
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