<?php
class Wine extends Model {
	protected static string $table = 'wine_wines';

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
	private $attachments;

	protected $db;

	public function __construct($uid = null) {
		$this->db = Database::getInstance();

		if ($uid !== null) {
			$this->getOne($uid);
		}
	}

	public function getOne($uid) {
		$query = "SELECT * FROM " . static::$table . " WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);

		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	public function clean_name($full = false) {
		$output  = $this->name;

		if ($full) {
			$bin = new Bin($this->bin_uid);
			$cellar = new Cellar($bin->cellar_uid);

			$output = $cellar->name . " > " . $bin->name . " > " . $this->name;

			if (!empty($this->vintage)) {
				$output .= " (" . $this->vintage() . ")";
			}
		}

		return $output;
	}

	public function binName() {
		$bin = new Bin($this->bin_uid);
		return $bin->name;
	}

	public function code(): string {
		return isset($this->code) && trim((string)$this->code) !== '' ? (string)$this->code : '0';
	}

	public function photographURL(): string {
		$filePath = UPLOAD_DIR . 'wines/';
		$filename = trim((string)$this->photograph);
		
		if ($filename === '') {
			return './assets/images/blank.png';
		}

		return file_exists($filePath . $filename)
			? './uploads/wines/' . $filename
			: './assets/images/blank.png';
	}

	public function pricePerBottle($target = "Internal") {
		return match($target) {
			'Internal' => $this->price_internal,
			'External' => $this->price_external ?? $this->price_internal,
			'Purchase' => $this->price_purchase,
			default => 999
		};
	}

	public function stockValue($filterDate = null) {
		return $this->currentQty($filterDate) * $this->price_purchase;
	}

	public function transactions() {
		$wineClass = new wineClass();
		return $wineClass->allTransactions(['wine_uid' => $this->uid]);
	}

	public function logs() {
		global $db;

		$sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, result, category, description FROM logs";
		$sql .= " WHERE category = 'wine'";
		$sql .= " AND description LIKE '%[wineUID:" . $this->uid . "]%'";
		$sql .= " ORDER BY date DESC";

		return $db->query($sql)->fetchAll();
	}

	public function card() {
		$bin = new Bin($this->bin_uid);
		$cellar = new Cellar($bin->cellar_uid);

		$url = "index.php?page=wine_wine&uid=" . $this->uid;
		$title = "<a href=\"" . $url . "\">" . $this->name . "</a>";

		$cardClass = match($this->status) {
			'Closed' => ' border-danger ',
			default => ($this->status != 'In Use' ? ' border-warning ' : '')
		};

		$output  = "<div class=\"col\">";
		$output .= "<div class=\"card {$cardClass} mb-3\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<h5 class=\"card-title text-truncate\">{$title}</h5>";
		$output .= "<p class=\"card-text text-truncate\">{$cellar->name} / {$bin->name}</p>";
		$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
		$output .= "<div class=\"btn-group\">";

		$output .= "<a href=\"#\" class=\"btn btn-sm btn-outline-secondary\">" . formatMoney($this->price_purchase) . "</a>";
		$output .= "<a href=\"#\" class=\"btn btn-sm btn-outline-secondary\">" . $this->code() . "</a>";
		$output .= "<a href=\"#\" class=\"btn btn-sm btn-outline-secondary\">" . $this->vintage() . "</a>";

		$output .= "</div>";
		$output .= $this->statusBadge();
		$output .= "<small class=\"text-body-secondary\">" . $this->currentQty() . autoPluralise(" bottle", " bottles", $this->currentQty()) . " </small>";
		$output .= "</div></div></div></div>";

		return $output;
	}

	public function statusBadge() {
		return match($this->status) {
			'Closed' => "<span class=\"badge rounded-pill text-bg-danger\">{$this->status}</span>",
			default => ($this->status != 'In Use' ? "<span class=\"badge rounded-pill text-bg-warning\">{$this->status}</span>" : '')
		};
	}

	public function vintage(bool $int = false) {
		if (empty($this->vintage) || !preg_match('/^\d{4}$/', $this->vintage)) {
			return $int ? '' : 'NV';
		}

		return $int ? (int)$this->vintage : $this->vintage;
	}

	public function statusBanner() {
		return match($this->status) {
			'Closed' => "<div class=\"alert alert-danger text-center\" role=\"alert\">STATUS: <strong>{$this->status}</strong></div>",
			default => ($this->status != 'In Use' ? "<div class=\"alert alert-warning text-center\" role=\"alert\">STATUS: <strong>{$this->status}</strong></div>" : '')
		};
	}

	public function favButton() {
		return "<button type=\"button\" class=\"btn text-danger btn-link\" data-bs-toggle=\"modal\" data-bs-target=\"#listModal\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#heart-full\"/></svg></button>";
	}

	public function currentQty($filterDate = null) {
		global $db;

		$sql  = "SELECT SUM(bottles) AS total FROM wine_transactions WHERE wine_uid = '" . $this->uid . "'";
		if ($filterDate) {
			$sql .= " AND date_posted <= '" . $filterDate . "'";
		}

		$results = $db->query($sql)->fetch();
		$total = $results['total'] ?? 0;

		return max(0, $total);
	}

	public function transactionsInFuture() {
		global $db;

		$sql  = "SELECT * FROM wine_transactions WHERE wine_uid = '{$this->uid}' AND date_posted > '" . date('Y-m-d') . "' ORDER BY date_posted DESC";
		return $db->query($sql)->fetchAll();
	}

	public function transactionsToDate($filterDate = null) {
		global $db;

		$dateLimit = $filterDate ? date('Y-m-d', strtotime($filterDate)) : date('Y-m-d');
		$sql = "SELECT * FROM wine_transactions WHERE wine_uid = '{$this->uid}' AND date_posted <= '{$dateLimit}' ORDER BY date_posted DESC";

		return $db->query($sql)->fetchAll();
	}

	// -----------------------
	// Generic file handling
	// -----------------------
	protected function handleFileUpload(array $file, string $type = 'attachment', array $allowedExtensions = [], int $maxSize = 5000000): ?array {
		global $db, $log, $user;

		if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;

		$originalName = basename($file['name']);
		$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

		if ($allowedExtensions && !in_array($ext, $allowedExtensions)) {
			$logsClass->create([
				'category' => 'wine',
				'result' => 'warning',
				'description' => "Invalid file extension ({$ext}) for {$type} upload on wine [wineUID:{$this->uid}]"
			]);
			return null;
		}

		if ($file['size'] > $maxSize) {
			$logsClass->create([
				'category' => 'wine',
				'result' => 'warning',
				'description' => "File too large ({$file['size']} bytes) for {$type} upload on wine [wineUID:{$this->uid}]"
			]);
			return null;
		}
		
		$uploadDir = UPLOAD_DIR . 'wines/';
		if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

		$uniqueName = uniqid("wine_{$this->uid}_", true) . '.' . $ext;
		$targetFile = $uploadDir . $uniqueName;

		if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
			$log->add("Failed to move uploaded file for {$type} on wine [wineUID:{$this->uid}]", 'file', Log::WARNING);
			return null;
		}

		return ['original' => $originalName, 'stored' => $uniqueName];
	}

	protected function handleFileDelete(string $storedFilename, string $type = 'attachment'): bool {
		global $log;

		$filePath = __DIR__ . UPLOAD_DIR . $storedFilename;

		if (file_exists($filePath) && !unlink($filePath)) {
			$log->add("Failed to physically delete {$type} file {$storedFilename} for [wineUID:{$this->uid}]", 'file', Log::WARNING);
			return false;
		}

		return true;
	}

	public function updatePhotograph(array $file): bool {
		global $db, $log, $settings;

		$allowed = array_map('trim', explode(',', $settings->get('uploads_allowed_filetypes')));
		
		if (!empty($this->photograph)) {
			$this->handleFileDelete($this->photograph, 'photograph');
		}

		$upload = $this->handleFileUpload($file, 'photograph', $allowed, 5000000);
		if (!$upload) return false;

		$db->query("UPDATE " . static::$table . " SET photograph = ? WHERE uid = ?", [$upload['stored'], $this->uid]);
		$this->photograph = $upload['stored'];
		
		$log->add("Photograph updated to {$upload['stored']} for wine [wineUID:{$this->uid}]", 'file', Log::SUCCESS);

		return true;
	}

	public function updateAttachment(array $file): ?array {
		global $db, $log, $settings, $user;

		$allowed = array_map('trim', explode(',', $settings->get('uploads_allowed_filetypes')));
		$upload = $this->handleFileUpload($file, 'attachment', $allowed, 5000000);
		if (!$upload) return null;

		$attachments = $this->attachments() ?? [];
		$attachments[] = [
			'original' => $upload['original'],
			'stored' => $upload['stored'],
			'uploaded_at' => date('Y-m-d H:i:s'),
			'username' => $user->getUsername()
		];

		$db->query("UPDATE " . static::$table . " SET attachments = ? WHERE uid = ?", [json_encode($attachments), $this->uid]);
		$this->attachments = json_encode($attachments);
		
		$log->add("Uploaded attachment {$upload['stored']} (original: {$upload['original']}) for wine [wineUID:{$this->uid}]", 'file', Log::SUCCESS);
			
		toast('File Uploaded', 'File sucesfully uploaded', 'text-success');

		return $upload;
	}

	public function removeAttachment(string $storedFilename): bool {
		global $db, $log;

		if (!$this->handleFileDelete($storedFilename, 'attachment')) return false;

		$attachments = $this->attachments() ?? [];
		$attachments = array_filter($attachments, fn($a) => $a['stored'] !== $storedFilename);
		$attachments = array_values($attachments);

		$db->query("UPDATE " . static::$table . " SET attachments = ? WHERE uid = ?", [json_encode($attachments), $this->uid]);
		$this->attachments = json_encode($attachments);
		
		$log->add("Deleted attachment {$storedFilename} for wine [wineUID:{$this->uid}]", 'file', Log::SUCCESS);
		toast('File Deleted', 'File sucesfully deleted', 'text-success');
		
		return true;
	}

	// -----------------------
	// Attachment helper
	// -----------------------
	public function attachments(): array {
		if (empty($this->attachments)) {
			return [];
		}
	
		$attachments = json_decode($this->attachments, true);
	
		if (!is_array($attachments)) {
			return [];
		}
		
		return $attachments;
	}
	
	// -----------------------
	// Other CRUD functions
	// -----------------------
	public function update(array $postData) {
		global $db;

		$fields = [
			'date_updated'      => date('c'),
			'code'              => $postData['code'] ?? '0',
			'bin_uid'           => $postData['bin_uid'] ?? null,
			'status'            => $postData['status'] ?? null,
			'name'              => $postData['name'] ?? null,
			'supplier'          => $postData['supplier'] ?? null,
			'supplier_ref'      => $postData['supplier_ref'] ?? null,
			'category'          => $postData['category'] ?? null,
			'grape'             => $postData['grape'] ?? null,
			'country_of_origin' => $postData['country_of_origin'] ?? null,
			'region_of_origin'  => $postData['region_of_origin'] ?? null,
			'vintage'           => ($postData['vintage'] === '' ? null : $postData['vintage']),
			'price_purchase'    => $postData['price_purchase'] ?? null,
			'price_internal'    => $postData['price_internal'] ?? null,
			'price_external'    => $postData['price_external'] ?? null,
			'tasting'           => $postData['tasting'] ?? null,
			'notes'             => $postData['notes'] ?? null
		];

		$updatedRows = $db->update(static::$table, $fields, ['uid' => $this->uid], 'logs');
		toast('Wine Updated', 'Wine successfully updated', 'text-success');

		return $updatedRows;
	}

	public function createWine(array $postData) {
		global $db, $user;

		$fields = [
			'date_updated'      => date('c'),
			'code'              => ($postData['code'] === '' ? null : 0),
			'bin_uid'           => $postData['bin_uid'] ?? null,
			'status'            => $postData['status'] ?? null,
			'name'              => $postData['name'] ?? null,
			'supplier'          => $postData['supplier'] ?? null,
			'supplier_ref'      => $postData['supplier_ref'] ?? null,
			'category'          => $postData['category'] ?? null,
			'grape'             => $postData['grape'] ?? null,
			'country_of_origin' => $postData['country_of_origin'] ?? null,
			'region_of_origin'  => $postData['region_of_origin'] ?? null,
			'vintage'           => ($postData['vintage'] === '' ? null : $postData['vintage']),
			'price_purchase'    => $postData['price_purchase'] ?? 0,
			'price_internal'    => $postData['price_internal'] ?? 0,
			'price_external'    => $postData['price_external'] ?? 0,
			'tasting'           => $postData['tasting'] ?? null,
			'notes'             => $postData['notes'] ?? null
		];

		$wine = $this->create($fields);
		
		$transaction_fields = [
			'date'				=> date('c'),
			'date_posted'		=> ($postData['date_posted'] === '' ? null : date('c')),
			'username'			=> $user->getUsername(),
			'type'				=> 'Import',
			'wine_uid'			=> $wine,
			'bottles'			=> $postData['qty'] ?? null,
			'price_per_bottle'	=> $postData['price_purchase'] ?? '0',
			'name'				=> 'Original Import'
		];
		$transaction = new Transaction;
		$transaction->create($transaction_fields);
		
		toast('Wine Created', 'Wine successfully created', 'text-success');

		return $wine;
	}

	public function delete() {
		global $db, $logsClass;

		$originalWineUID = $this->uid;

		// Delete photograph
		$target_dir = UPLOAD_DIR . 'wines/';
		if (!empty($this->photograph) && file_exists($target_dir . $this->photograph)) {
			unlink($target_dir . $this->photograph);
			$logsClass->create([
				'category' => "wine",
				'result' => "warning",
				'description' => "Deleted file: " . $this->photograph
			]);
		}

		$sql = "DELETE FROM wine_wines WHERE uid = '{$this->uid}' LIMIT 1";
		$db->query($sql);

		$logsClass->create([
			'category' => "wine",
			'result' => "warning",
			'description' => "Deleted [wineUID:{$originalWineUID}]"
		]);

		return true;
	}
}
