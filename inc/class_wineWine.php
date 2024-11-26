<?php
class wine extends wineClass {
  protected static $table_name = "wine_wines";
  
  public $uid;
  public $code;
  public $cellar_uid;
  public $bin;
  public $status;
  public $name;
  public $qty;
  public $category;
  public $grape;
  public $country_of_origin;
  public $vintage;
  public $price_purchase;
  public $price_internal;
  public $price_external;
  public $tasting;
  public $notes;
  public $photograph;
  
  function __construct($uid = null) {
	global $db;
	
	$sql  = "SELECT * FROM " . self::$table_name;
	$sql .= " WHERE uid = '" . $uid . "'";
	
	$results = $db->query($sql)->fetchArray();
  
	foreach ($results AS $key => $value) {
	  $this->$key = $value;
	}
  }
  
  public function create($array) {
	global $db, $logsClass;
	
	// Initialize the set part of the query
	$setParts = [];
	
	//remove the memberUID
	unset($array['uid']);
	
	// Loop through the new values array
	foreach ($array as $field => $newValue) {
	  if (is_array($newValue)) {
		$newValue = implode(",", $newValue);
	  }
	  
	  $setParts[$field] = "`$field` = '$newValue'";
	}
	
	// If there are no changes, return null
	if (empty($setParts)) {
		return null;
	}
	
	// Combine the set parts into a single string
	$setString = implode(", ", $setParts);
	
	// Construct the final UPDATE query
	$sql = "INSERT INTO wine_wines SET " . $setString;
	
	$insert = $db->query($sql);
	
	//log a transaction
	//$wine_transactions->create();
	
	$logArray['category'] = "wine";
	$logArray['result'] = "success";
	$logArray['description'] = "Created new wine with fields " . $setString;
	$logsClass->create($logArray);
	
	
	
	return true;
  }
  
  public function create_transaction($array = null) {
	  $wineTransaction = new wine_transactions();
	  
	  $wineTransaction->create($array);
	  $this->deduct($array['value']);
  }
  
  public function deduct($qtyToDeduct) {
	global $logsClass;
	
	// turn positive numbers into negative numbers
	$qtyToDeduct = $qtyToDeduct <= 0 ? $qtyToDeduct : -$qtyToDeduct ;
	
	$currentTotalBottles = $this->qty;
	$targetTotalBottles = $currentTotalBottles + $qtyToDeduct;
	$actualTargetTotalBottles = max(0, ($targetTotalBottles)); // don't allow less than 0 bottles
	
	$array = array("qty" => $actualTargetTotalBottles);
	$this->update($array);
	
	if ($targetTotalBottles < 0) {
	  // an attempt to deduct more bottles than there were... so log this
	  $logArray['category'] = "wine";
	  $logArray['result'] = "warning";
	  $logArray['description'] = "Attempted to deduct " . $qtyToDeduct . " from [wineUID:" . $this->uid . "] when only " . $currentTotalBottles . " existed";
	  $logsClass->create($logArray);
	}
	
	return true;
  }
  
  public function friendly_name($full = false) {
	$output  = $this->name;
	
	if ($full == true) {
	  $cellar = new cellar($this->cellar_uid);
	  
	  $output = $cellar->name . " > " . $this->name;
	} else {
	  $output  = $this->name;
	}
	
	return $output;
  }
  
  public function update($array) {
	global $db, $logsClass;
	
	//printArray($array);
	//printArray($_FILES);
	
	// Initialize the set part of the query
	$setParts = [];
	
	//remove the uid
	unset($array['uid']);
	
	if (!empty($_FILES['photograph']['name'])) {
	  
	  $target_dir = "/var/www/scr2.seh.ox.ac.uk/public_html/img/wines/";
	  $target_file = $target_dir . basename($_FILES["photograph"]["name"]);
	  $uploadOk = 1;
	  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	  
	  // Check if image file is a actual image or fake image
		$check = getimagesize($_FILES["photograph"]["tmp_name"]);
		if($check == false) {
		  $uploadOk = 0;
		}
	  
	  
	  // Check if file already exists
	  if (file_exists($target_file)) {
		echo "Sorry, file already exists.";
		$uploadOk = 0;
	  }
	  
	  // Check file size
	  if ($_FILES["photograph"]["size"] > 5000000) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	  }
	  
	  // Allow certain file formats
	  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	  && $imageFileType != "gif" ) {
		echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	  }
	  
	  // Check if $uploadOk is set to 0 by an error
	  if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	  // if everything is ok, try to upload file
	  } else {
		if (move_uploaded_file($_FILES["photograph"]["tmp_name"], $target_file)) {
		  $array['photograph'] = basename($_FILES["photograph"]["name"]);
		  echo "The file ". htmlspecialchars( basename( $_FILES["photograph"]["name"])). " has been uploaded.";
		} else {
		  echo "Sorry, there was an error uploading your file.";
		}
	  }
	  
	  
	  
	  
	}
	
	// Loop through the new values array
	foreach ($array as $field => $newValue) {
	  if (is_array($newValue)) {
		$newValue = implode(",", $newValue);
	  }
		// Check if the field exists in the current values and if the values are different
		if ($this->$field != $newValue) {
		  
			// Sanitize the field and value to prevent SQL injection
			//$field = mysqli_real_escape_string($conn, $field);
			//$newValue = mysqli_real_escape_string($conn, $newValue);
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
	$sql = "UPDATE wine_wines SET " . $setString;
	$sql .= " WHERE uid = '" . $this->uid . "' ";
	$sql .= " LIMIT 1";
	
	$update = $db->query($sql);
	
	$logArray['category'] = "wine";
	$logArray['result'] = "success";
	$logArray['description'] = "Updated [wineUID:" . $this->uid . "] with fields " . $setString;
	$logsClass->create($logArray);
	
	return true;
  }
  
  public function totalBottlesInStock() {
	global $db;
	
	$sql  = "SELECT SUM(qty) AS total_wines FROM " . self::$table_name;
	$sql .= " WHERE cellar_uid = '" . $this->cellar_uid . "'";
	$sql .= " AND bin = '" . $this->bin . "'";
	
	$results = $db->query($sql)->fetchArray();
	
	if (!$results['total_wines'] > 0) {
	  $results['total_wines'] = 0;
	}
	
	return $results['total_wines'];
  }
  
  public function photograph() {
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
  
  public function binCard() {
	$cellar = new cellar($this->cellar_uid);
	
	if ($_GET['n'] != "wine_bin" && count($cellar->getAllWinesByBin($this->bin)) > 1) {
	  $url = "index.php?n=wine_bin&cellar_uid=" . $cellar->uid . "&bin=" . $this->bin;
	  $description = "Multiple wines in bin (" . count($cellar->getAllWinesByBin($this->bin)) . ")";
	} else {
	  $url = "index.php?n=wine_wine&uid=" . $this->uid;
	  $description = $this->name;
	}
	
	if ($this->status == "In-Bond") {
	  $binName = "<a href=\"" . $url . "\" type=\"button\" class=\"btn btn-primary position-relative\">" . $this->bin . "<span class=\"position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning\">In-Bond</span></a>";
	  $cardClass = " border-warning ";
	} else {
	  $binName = "<a href=\"" . $url . "\" type=\"button\" class=\"btn btn-primary position-relative\">" . $this->bin . "</a>";
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
	$output .= "<small class=\"text-body-secondary\">" . $this->totalBottlesInStock() . autoPluralise(" bottle", " bottles", $this->totalBottlesInStock()) . " </small>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";
	
	return $output;
  }
  
  public function transactions() {
	$wine_transactions = new wine_transactions();
	
	$results = $wine_transactions->getAllTransactionsForWine($this->uid);
	
	return $results;
  }
  
  public function logs() {
	global $db;
	
	$sql  = "SELECT * FROM logs";
	$sql .= " WHERE category = 'wine'";
	$sql .= " AND description LIKE '%[wineUID:" . $this->uid . "]%'";
	$sql .= " ORDER BY date DESC";
	
	$results = $db->query($sql)->fetchAll();
	
	return $results;
  }
}
?>