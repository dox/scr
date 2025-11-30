<?php
class Wine {
	protected static $table_wines = "wine_wines";
	
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
	public $attachments;
	
	protected $db;
	
	public function __construct($uid = null) {
		$this->db = Database::getInstance();
	
		if ($uid !== null) {
			$this->getOne($uid);
		}
	}
	
	public function getOne($uid) {
		$query = "SELECT * FROM " . static::$table_wines . " WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);
	
		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
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
	
	public function binName(){
		$bin = new Bin($this->bin_uid);
		
		return $bin->name;
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
	
	public function favButton() {
		$output  = "<button type=\"button\" class=\"btn text-danger btn-link\" data-bs-toggle=\"modal\" data-bs-target=\"#listModal\">";
		$output .= "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#heart-full\"/></svg>";
		$output .= "</button>";
		
		return $output;
	}
	
	public function currentQty($filterDate = null) {
		global $db;
		
		$sql  = "SELECT SUM(bottles) AS total FROM wine_transactions";
		$sql .= " WHERE wine_uid = '" . $this->uid . "'";
		
		if (isset($filterDate)) {
			$sql .= " AND date_posted <= '" . $filterDate . "'";
		}
		
		$results = $db->query($sql)->fetch();
		
		// don't allow null
		if (!isset($results['total'])) {
			$results['total'] = 0;
		}
		
		// don't allow negatives
		if ($results['total'] < 0) {
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
		if (isset($array['vintage']) && empty($array['vintage'])) {
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
	
	public function attachments() {
		if (empty($this->attachments)) {
			return [];
		}
	
		$attachments = json_decode($this->attachments, true);
	
		if (json_last_error() !== JSON_ERROR_NONE || !is_array($attachments)) {
			return [];
		}
	
		return $attachments;
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
	
	public function uploadAttachment($fileField = 'attachment') {
		global $db, $settingsClass, $logsClass;
	
		// Check for uploaded file
		if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
			return null; // No file or failed upload
		}
	
		$upload = $_FILES[$fileField];
		$originalName = basename($upload['name']);
		$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
	
		// Validate extension
		$allowed = array_map('trim', explode(',', $settingsClass->value('uploads_allowed_filetypes')));
		if (!in_array($ext, $allowed)) {
			$logArray['category'] = "wine";
			$logArray['result'] = "warning";
			$logArray['description'] = "Invalid file extension (" . $ext . ") for attachment upload on wine [wineUID:{$this->uid}]";
			$logsClass->create($logArray);
			return null;
		}
	
		// Create unique filename
		$uniqueName = uniqid("wine_" . $this->uid . "-", true) . '.' . $ext;
		$uploadDir = "uploads/";
	
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0775, true);
		}
	
		$target = $uploadDir . $uniqueName;
		
		if (move_uploaded_file($upload['tmp_name'], $target)) {
			// Construct the final UPDATE query
			$sql = "UPDATE wine_wines
			SET attachments = 
			JSON_ARRAY_APPEND(
				IFNULL(attachments, JSON_ARRAY()),
				'$',
				JSON_OBJECT('original' , '" . $originalName . "', 'stored', '" . $uniqueName . "')
			)
			WHERE uid = " . $this->uid;
			$db->query($sql);
			
			$logArray['category'] = "wine";
			$logArray['result'] = "success";
			$logArray['description'] = "Uploaded attachment {$uniqueName} (original: {$originalName}) for [wineUID:{$this->uid}]";
			$logsClass->create($logArray);
	
			return [
				'original' => $originalName,
				'stored' => $uniqueName
			];
		} else {
			$logArray['category'] = "wine";
			$logArray['result'] = "warning";
			$logArray['description'] = "Failed to move uploaded attachment to " . $target . " for [wineUID:{$this->uid}]";
			$logsClass->create($logArray);
			return null;
		}
	}
	
	public function deleteAttachment($storedFilename) {
		global $db, $logsClass;
		
		$uploadDir = "uploads/";
		$filePath = $uploadDir . $storedFilename;
		
		// 1. Delete the file from disk if it exists
		if (file_exists($filePath)) {
			if (!unlink($filePath)) {
				// Failed to delete file physically
				$logArray = [
					'category' => "wine",
					'result' => "warning",
					'description' => "Failed to physically delete attachment file {$storedFilename} for [wineUID:{$this->uid}]"
				];
				$logsClass->create($logArray);
				return false;
			}
		} else {
			// File doesn't exist, but maybe it was already removed; log as warning
			$logArray = [
				'category' => "wine",
				'result' => "warning",
				'description' => "Attachment file {$storedFilename} does not exist for [wineUID:{$this->uid}]"
			];
			$logsClass->create($logArray);
		}
		
		// 2. Remove the attachment JSON object from the attachments field
		$attachments = $this->attachments();
		
		if (is_array($attachments)) {
			// Filter out the attachment with this stored filename
			$newAttachments = array_filter($attachments, function($item) use ($storedFilename) {
				return !isset($item['stored']) || $item['stored'] !== $storedFilename;
			});
			$newAttachments = array_values($newAttachments); // Reindex array
			
			// Update DB
			$newAttachmentsJson = json_encode($newAttachments);
			$db->query("UPDATE wine_wines SET attachments = ? WHERE uid = ?", $newAttachmentsJson, $this->uid);
			
			$logArray = [
				'category' => "wine",
				'result' => "success",
				'description' => "Deleted attachment {$storedFilename} for [wineUID:{$this->uid}]"
			];
			$logsClass->create($logArray);
			
			return true;
		} else {
			// attachments field empty or malformed
			$logArray = [
				'category' => "wine",
				'result' => "warning",
				'description' => "Malformed or empty attachments field when deleting {$storedFilename} for [wineUID:{$this->uid}]"
			];
			$logsClass->create($logArray);
			return false;
		}
	}
}
?>