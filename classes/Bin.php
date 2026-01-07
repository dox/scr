<?php
class Bin extends Model {
	public $uid;
	public $cellar_uid;
	public $name;
	public $section;
	public $description;
	
	protected $db;
	protected static string $table = 'wine_bins';
	
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
			'cellar_uid'  => $postData['cellar_uid'] ?? null,
			'section'   => $postData['section'] ?? null,
			'description'   => $postData['description'] ?? null
		];
		
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
		
		toast('Bin Updated', 'Bin sucesfully updated', 'text-success');
	
		return $updatedRows;
	}
	
	public function delete() {
		global $db;
		if (!isset($this->uid)) {
			return false;
		}
		
		// Delete meal
		$db->delete(
			static::$table,
			['uid' => $this->uid],
			'logs'
		);
		
		toast('Bin Deleted', 'Bin sucesfully deleted', 'text-success');
	}
	
	public function wines($whereArray = null): array {
		global $db;
		
		$wines = new Wines();
		
		$whereArray = array_merge(['wine_bins.uid' => ['=', $this->uid]], $whereArray ?? []);
		
		return $wines->wines($whereArray);
	}
}
