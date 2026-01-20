<?php
class Transaction extends Model {
	public $uid;
	public $date;
	public $date_posted;
	public $username;
	public $type;
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
	
	public function isLinked(): bool {
		return !empty($this->linked);
	}
	
	public function linkedTransactions(): array {
		if ($this->isLinked()) {
			$wines = $GLOBALS['wines'] ?? new Wines();
			$conditionArray['linked'] = ['=', $this->linked];
			return $wines->transactions($conditionArray);
		}
		return [];
	}
	
	public function totalBottles(): int {
		if ($this->isLinked()) {
			// linked row: sum all bottles in the group
			$query = "SELECT SUM(bottles) AS bottles FROM " . static::$table . " WHERE linked = ?";
			$row = $this->db->fetch($query, [$this->linked]);
	
			return (int) (abs($row['bottles']) ?? 0);
		} else {
			// unlinked row: just its own bottles
			return abs((int) $this->bottles);
		}
	}
	
	public function totalValue(): float {
		if ($this->isLinked()) {
			$total = 0;
			foreach ($this->linkedTransactions() as $transaction) {
				$total += $transaction->value();
			}
		} else {
			$total = $this->value();
		}
		
		return round(abs((float) $total), 2);
	}
	
	public function value(): float {
		$total = ($this->bottles * $this->price_per_bottle);
		
		return round(abs((float) $total), 2);
	}
	
	public function delete() {
		global $db;
		
		if (!isset($this->uid)) {
			return false;
		}
		
		// Send to database to delete bookings
		$deleteBooking = $db->delete(
			'wine_transactions',
			['uid' => $this->uid],
			'logs'
		);
		
		return true;
	}
}
