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
	
	

}
