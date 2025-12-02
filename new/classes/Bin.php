<?php
class Bin extends Model {
	public $uid;
	public $cellar_uid;
	public $name;
	public $category;
	public $description;
	
	protected $db;
	protected static string $table_bins = 'wine_bins';
	
	public function __construct($uid = null) {
		$this->db = Database::getInstance();
	
		if ($uid !== null) {
			$this->getOne($uid);
		}
	}
	
	public function getOne($uid) {
		$query = "SELECT * FROM " . static::$table_bins . " WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);
	
		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}
	
	public function wines(): array {
		global $db;
		
		$wines = new Wines();
		$wines = $wines->wines([
			'wine_bins.uid' => ['=', $this->uid],
			'wine_wines.status' => ['<>', 'Closed'],
		]);
	
		return $wines;
	}
	
	public function winesClosed(): array {
		global $db;
		
		$binsBySection = $wines->bins([
			'cellar_uid' => ['=', $cellar_uid],
			'wine_bins.category' => ['=', $cellar_section]
		]);
		
		$sql = "SELECT * FROM wine_wines WHERE bin_uid = ?";
	
		$rows = $db->fetchAll($sql, [$this->uid]);
		
		$wines = [];
		foreach ($rows as $row) {
			$wines[] = new Wine($row['uid']);
		}
	
		return $wines;
	}
	
	
}
