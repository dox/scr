<?php
class Cellar extends Model {
	public $uid;
	public $name;
	public $short_code;
	public $notes;
	public $bin_types;
	public $photograph;
	
	protected $db;
	protected static string $table_cellars = 'wine_cellars';
	protected static string $table_bins = 'wine_bins';
	
	public function __construct($uid = null) {
		$this->db = Database::getInstance();
	
		if ($uid !== null) {
			$this->getOne($uid);
		}
	}
	
	public function getOne($uid) {
		$query = "SELECT * FROM " . static::$table_cellars . " WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);
	
		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}
	
	public function bins(array $whereFilterArray = []) : array
	{
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
		$output .= count($this->bins()) . autoPluralise(" bin", " bins", count($this->bins()));
		$output .= " / ";
		$output .= $this->bottlesCount() . autoPluralise(" bottle", " bottles", $this->bottlesCount());
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
