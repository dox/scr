<?php
class Term extends Model {
	public $uid;
	public $name;
	public $date_start;
	public $date_end;
	
	protected $db;
	protected static string $table = 'terms';
	
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
	
	public function tabLabel(?string $date = null): string {
		// Use today if no date provided
		$dateObj = $date ? new DateTime($date) : new DateTime();
		$endDateObj = new DateTime($this->date_end);
	
		$weekNumber = $this->weekNumber($dateObj);
	
		if ($dateObj < $endDateObj) {
			return $this->ordinal($weekNumber) . " week";
		} else {
			return "w/c " . $dateObj->format('M jS');
		}
	}
	
	public function weekNumber(DateTime $dateObj = null): int {
		$dateFrom = new DateTime($this->date_start);
		$dateTo = $dateObj ?? new DateTime(); // default to today
	
		$daysDiff = (int)$dateFrom->diff($dateTo)->format('%a');
	
		return intdiv($daysDiff, 7) + 1;
	}
	
	public function ordinal(int $number): string {
		$lastTwo = $number % 100;
	
		if ($lastTwo >= 11 && $lastTwo <= 13) {
			return $number . 'th';
		}
	
		return $number . match ($number % 10) {
			1 => 'st',
			2 => 'nd',
			3 => 'rd',
			default => 'th',
		};
	}
}
