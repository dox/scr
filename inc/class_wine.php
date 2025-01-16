<?php
class wine {
	protected static $table_name = "wine_wines";
	
	public $uid;
	public $date_created;
	public $date_updated;
	public $code;
	public $bin_uid;
	public $status;
	public $name;
	public $supplier;
	public $supplier_ref;
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
	
	function __construct($wineUID = null) {
		global $db;
	  
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE uid = '" . $wineUID . "'";
		
		$results = $db->query($sql)->fetchArray();
		
		foreach ($results AS $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function clean_name($full = false) {
		$output  = $this->name;
		
		if ($full == true) {
			$bin = new bin($this->bin_uid);
			$cellar = new cellar($bin->cellar_uid);
			
			$output = $cellar->name . " > " . $bin->name . " > " . $this->name;
			
			if (!empty($this->vintage)) {
				$output .= " (" . $this->vintage() . ")";
			}
		} else {
			$output  = $this->name;
		}
		
		return $output;
	}
	
	public function photographURL() {
		$img_dir = "img/wines/";
		$image = "img/blank.jpg";
		
		if (!empty($this->photograph)) {
			if (file_exists($img_dir . $this->photograph)) {
				$image = $img_dir . $this->photograph;
			}
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
	
	public function stockValue($filterDate = null) {
		return ($this->currentQty($filterDate) * $this->price_purchase);
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
		$bin = new bin($this->bin_uid);
		$cellar = new cellar($bin->cellar_uid);
		
		$url = "index.php?n=wine_wine&wine_uid=" . $this->uid;
		$title = "<a href=\"" . $url . "\">" . $this->name . "</a>";
		
		if ($this->status == "Closed") {
		  $cardClass = " border-danger ";
		} elseif ($this->status <> "In Use") {
		  $cardClass = " border-warning ";
		} else {
		  $cardClass = "";
		}
		
		$output  = "<div class=\"col\">";
		$output .= "<div class=\"card " . $cardClass . " shadow-sm\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<h5 class=\"card-title text-truncate\">" . $title . "</h5>";
		$output .= "<p class=\"card-text text-truncate\">" . $cellar->name . " / " . $bin->name . "</p>";
		$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
		$output .= "<div class=\"btn-group\">";
		$output .= "<a href=\"index.php?n=wine_search&filter=price&value=" . $this->price_purchase . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">" . currencyDisplay($this->price_purchase) . "</a>";
		$output .= "<a href=\"index.php?n=wine_search&filter=code&value=" . $this->code . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">" . $this->code . "</a>";
		$output .= "<a href=\"index.php?n=wine_search&filter=vintage&value=" . $this->vintage . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">" . $this->vintage() . "</a>";
		$output .= "</div>";
		$output .= $this->statusBadge();
		$output .= "<small class=\"text-body-secondary\">" . $this->currentQty() . autoPluralise(" bottle", " bottles", $this->currentQty()) . " </small>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
	
	public function statusBadge() {
		if ($this->status == "Closed") {
		  $output = "<span class=\"badge rounded-pill text-bg-danger\">" . strtoupper($this->status) . "</span>";
		} elseif ($this->status <> "In Use") {
		  $output = "<span class=\"badge rounded-pill text-bg-warning\">" . strtoupper($this->status) . "</span>";
		} else {
		  $output = "";
		}
		
		return $output;
	}
	
	public function vintage() {
		// Check if the vintage is null or empty
		if (empty($this->vintage)) {
			return 'NV';
		}
		
		// Check if the vintage is a valid year in the format YYYY
		if (preg_match('/^\d{4}$/', $this->vintage)) {
			return $this->vintage;
		}
		
		// If it's not valid, return 'NV'
		return 'NV';
	}
	
	public function statusBanner() {
		if ($this->status == "Closed") {
		  $output = "<div class=\"alert alert-danger text-center\" role=\"alert\">STATUS: <strong>" . strtoupper($this->status) . "</strong></div>";
		} elseif ($this->status <> "In Use") {
		  $output = "<div class=\"alert alert-warning text-center\" role=\"alert\">STATUS: <strong>" . strtoupper($this->status) . "</strong></div>";
		} else {
		  $output = "";
		}
		
		return $output;
	}
	
	public function currentQty($filterDate = null) {
		global $db;
		
		$sql  = "SELECT SUM(bottles) AS total FROM wine_transactions";
		$sql .= " WHERE wine_uid = '" . $this->uid . "'";
		
		if (isset($filterDate)) {
			$sql .= " AND date_posted <= '" . $filterDate . "'";
		}
		
		$results = $db->query($sql)->fetchArray();
		
		if (!isset($results['total'])) {
			$results['total'] = 0;
		}
		
		return $results['total'];
	}
	
	public function transactionsInFuture() {
		global $db;
		
		$sql  = "SELECT * FROM wine_transactions ";
		$sql .= " WHERE wine_uid = '" . $this->uid . "'";
		$sql .= " ORDER BY date_posted DESC";
		$sql .= " AND date_posted > '" . date('Y-m-d') . "'";
		
		$results = $db->query($sql)->fetchAll();
		
		return $results;
	}
	
	public function transactionsToDate($filterDate = null) {
		global $db;
		
		$sql  = "SELECT * FROM wine_transactions ";
		$sql .= " WHERE wine_uid = '" . $this->uid . "'";
		if ($filterDate != null) {
			$sql .= " AND date_posted <= '" . date('Y-m-d', strtotime($filterDate)) . "'";
		} else {
			$sql .= " AND date_posted <= '" . date('Y-m-d') . "'";
		}
		$sql .= " ORDER BY date_posted DESC";
		
		$results = $db->query($sql)->fetchAll();
		
		return $results;
	}
	
	public function updatePhotograph($file) {
		global $db, $logsClass;
		
		if (!empty($file['photograph']['name'])) {
			$uploadOk = 1;
			
			$target_dir = "../img/wines/";
			$imageFileType = strtolower(pathinfo($file['photograph']['name'],PATHINFO_EXTENSION));
			$newFileName = "wine_" . $this->uid . '.' . $imageFileType;
			$target_file = $target_dir . $newFileName;
			
			

			// Check if image file is a actual image or fake image
			$check = getimagesize($file["photograph"]["tmp_name"]);
			if($check == false) {
				$uploadOk = 0;
			}
			
			// Check if file already exists
			if (file_exists($target_file)) {
				unlink($target_file);
			}
			if (file_exists($target_dir . $this->photograph)) {
				unlink($target_dir . $this->photograph);
			}
			
			// Check file size
			if ($file["photograph"]["size"] > 5000000) {
				echo "Sorry, your file is too large.";
				$uploadOk = 0;
				
				$logArray['category'] = "wine";
				$logArray['result'] = "warning";
				$logArray['description'] = $file["photograph"]["size"] . " for [wineUID:" . $this->uid . "] is too large";
				$logsClass->create($logArray);
			}
			
			// Allow only certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
				echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
				$uploadOk = 0;
				
				$logArray['category'] = "wine";
				$logArray['result'] = "warning";
				$logArray['description'] = $file["photograph"]["size"] . " for [wineUID:" . $this->uid . "] has the wrong extension";
				$logsClass->create($logArray);
			}
			
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				echo "Sorry, your file was not uploaded.";
				
				$logArray['category'] = "wine";
				$logArray['result'] = "warning";
				$logArray['description'] = "Could not upload photo for [wineUID:" . $this->uid . "]";
				$logsClass->create($logArray);
				
				// if everything is ok, try to upload file
			} else {
				if (move_uploaded_file($file["photograph"]["tmp_name"], $target_file)) {
					$array['photograph'] = basename($file["photograph"]["name"]);
					echo "The file ". htmlspecialchars( basename( $file["photograph"]["name"])). " has been uploaded.";
					
					// Construct the final UPDATE query
					$sql = "UPDATE wine_wines SET photograph = '" . $newFileName . "'";
					$sql .= " WHERE uid = '" . $this->uid . "' ";
					$sql .= " LIMIT 1";
					
					$update = $db->query($sql);
					
					$logArray['category'] = "wine";
					$logArray['result'] = "success";
					$logArray['description'] = "Photo " . $newFileName . " uploaded  for [wineUID:" . $this->uid . "]";
					$logsClass->create($logArray);
				} else {
					echo "Sorry, there was an error uploading your file.";
					
					$logArray['category'] = "wine";
					$logArray['result'] = "warning";
					$logArray['description'] = "Could not upload photo for [wineUID:" . $this->uid . "]";
					$logsClass->create($logArray);
				}
			}
		}
	}
	
	public function update($array) {
		global $db, $logsClass;
		
		// Initialize the set part of the query
		$setParts = [];
		
		//remove the uid
		unset($array['uid']);
		
		// null missing vintage
		if (empty($array['vintage'])) {
			$array['vintage'] = null;
		}
		
		//upload photo (if empty no worries)
		$this->updatePhotograph($_FILES);
		
		// Loop through the new values array
		foreach ($array as $field => $newValue) {
		  if (is_array($newValue)) {
			$newValue = implode(",", $newValue);
		  }
		  
		  // Check if the field exists in the current values and if the values are different
		  if ($this->$field != $newValue) {
			  if (is_null($newValue)) {
					$setParts[$field] = "`$field` = NULL";
				} else {
					// Sanitize the field and value to prevent SQL injection
					$field = htmlspecialchars($field);
					$newValue = trim(htmlspecialchars($newValue));
					// Add to the set part
					$setParts[$field] = "`$field` = '$newValue'";
				}
		  }
		}
		
		// If there are no changes, return null
		if (empty($setParts)) {
			return null;
		}
		
		// Combine the set parts into a single string
		$setParts['date_updated'] = "`date_updated` = '" . date('c') . "'";
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
	  
	public function create($array) {
		global $db, $logsClass;
		  
		// Initialize the set part of the query
		$setParts = [];
		
		// null missing vintage
		if (empty($array['vintage'])) {
			$array['vintage'] = null;
		}
		
		// Loop through the new values array
		foreach ($array as $field => $newValue) {
			if (is_array($newValue)) {
				$newValue = implode(",", $newValue);
			}
			
			if (is_null($newValue)) {
				$setParts[$field] = "`$field` = NULL";
			} else {
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
		
		// we don't want the qty for the wine record (only for the transaction)
		unset($setParts['qty']);
		unset($setParts['posting_date']);
		
		// Combine the set parts into a single string
		$setString = implode(", ", $setParts);
		
		// Construct the final UPDATE query
		$sql = "INSERT INTO wine_wines SET " . $setString;
		$insert = $db->query($sql);
		$this->uid = $db->lastInsertID();
		
		$logArray['category'] = "wine";
		$logArray['result'] = "success";
		$logArray['description'] = "Created [wineUID:" . $this->uid . "] with fields " . $setString;
		$logsClass->create($logArray);
		
		//upload photo (if empty no worries)
		$this->updatePhotograph($_FILES);
		
		//log a transaction
		$data['wine_uid'] = $this->uid;
		$data['type'] = "Import";
		if (isset($array['posting_date'])) {
			$data['date_posted'] = $array['posting_date'];
		} else {
			$data['date_posted'] = date('Y-m-d');
		}
		$data['bottles'] = $array['qty'];
		$data['price_per_bottle'] = $array['price_purchase'];
		$data['name'] = "Original import into system";
		
		$transaction = new transaction();
		$transaction->create($data);
		
		return true;
	}
	
	public function delete() {
		global $db, $logsClass;
		
		$originalWineUID = $this->uid;
		
		//delete photo (if empty no worries)
		$target_dir = "../img/wines/";
		if (file_exists($target_dir . $this->photograph)) {
			unlink($target_dir . $this->photograph);
			
			$logArray['category'] = "wine";
			$logArray['result'] = "warning";
			$logArray['description'] = "Deleted file:" . $this->photograph;
			$logsClass->create($logArray);
		} 
		
		// Construct the final UPDATE query
		$sql = "DELETE FROM wine_wines WHERE uid = '" . $this->uid . "'";
		$sql .= " LIMIT 1";
		
		$delete = $db->query($sql);
		
		$logArray['category'] = "wine";
		$logArray['result'] = "warning";
		$logArray['description'] = "Deleted [wineUID:" . $originalWineUID . "]";
		$logsClass->create($logArray);
		
		return true;
	}
}
?>