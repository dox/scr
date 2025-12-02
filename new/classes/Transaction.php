<?php
class Transaction extends Model {
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
	public $linked;
	
	protected $db;
	protected static string $table = 'wine_transactions';
	
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
	
	public function update(array $postData) {
		global $db;
	
		// Map normal text/select fields
		$fields = [
			'name'      => $postData['name'] ?? null,
			'short_code'  => $postData['short_code'] ?? null,
			'notes'   => $postData['notes'] ?? null,
			'bin_types'   => $postData['bin_types'] ?? null,
			'photograph'   => $postData['photograph'] ?? null
		];
		
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
		
		toast('Cellar Updated', 'Cellar sucesfully updated', 'text-success');
	
		return $updatedRows;
	}
	
	public function bins(array $whereFilterArray = []) : array {
		global $db;
	
		// Always enforce cellar constraint first
		$whereFilterArray[] = ['key' => 'cellar_uid', 'value' => $this->uid];
	
		$sql = "SELECT * FROM " . self::$table_bins;
	
		if (!empty($whereFilterArray)) {
			$conditions = [];
	
			foreach ($whereFilterArray as $filter) {
				$key   = addslashes($filter['key']);
				$value = addslashes($filter['value']);
				$conditions[] = "$key = '$value'";
			}
	
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY name ASC";
	
		$rows = $db->query($sql)->fetchAll();
	
		$bins = [];
	
		foreach ($rows as $row) {
			$bins[] = new Bin($row['uid']);
		}
	
		return $bins;
	}
	
	public function bottlesCount() {
		global $db;
		
		$sql = "SELECT SUM(wine_total) AS total_bottles_in_cellar
		FROM (
			SELECT cellar_uid, wine_uid, GREATEST(0, SUM(bottles)) AS wine_total
			FROM wine_transactions
			WHERE cellar_uid = '" . $this->uid . "'
			GROUP BY cellar_uid, wine_uid
		) AS wine_sums";
		
		$result = $db->query($sql)->fetch();
		
		return $result['total_bottles_in_cellar'];
	}
	
	public function card() {
		$output  = "<div class=\"col-sm-12 col-md-6 mb-3\">";
		$output .= "<div class=\"card shadow-sm\">";
		$output .= "<img src=\"" . $this->photographURL() . "\" class=\"card-img-top\" alt=\"Cellar photograph\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<h5 class=\"card-title\"><i>(" . $this->short_code . ")</i> " . $this->name . "</h5>";
		$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
		$output .= "<a href=\"index.php?page=wine_cellar&uid=" . $this->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
		$output .= "<small class=\"text-body-secondary\">";
		$output .= number_format(count($this->bins())) . autoPluralise(" bin", " bins", count($this->bins()));
		$output .= " / ";
		$output .= number_format($this->bottlesCount()) . autoPluralise(" bottle", " bottles", $this->bottlesCount());
		$output .= "</small>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
	
	public function photographURL(): string {
		$urlPath  = '/new/uploads/meal_cards/';
		$filePath = $_SERVER['DOCUMENT_ROOT'] . $urlPath;
	
		// Choose the candidate filename (null or empty means default)
		$filename = trim((string) $this->photograph);
	
		// If no filename at all, skip straight to default
		if ($filename === '') {
			return './assets/images/card_default.png';
		}
	
		// If file exists, return its public path; otherwise, fall to default
		return file_exists($filePath . $filename)
			? './uploads/meal_cards/' . $filename
			: './assets/images/card_default.png';
	}

}
