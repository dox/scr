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
	
	public function update(array $postData) {
		global $db;
	
		// Map normal text/select fields
		$fields = [
			'name'      => $postData['name'] ?? null,
			'date_start'  => $postData['date_start'] ?? null,
			'date_end'   => $postData['date_end'] ?? null
		];
	
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
		
		toast('Term Updated', 'Term sucesfully updated', 'text-success');
	
		return $updatedRows;
	}
	
	public function delete() {
		global $db;
		if (!isset($this->uid)) return false;
		
		$db->delete(
			static::$table,
			['uid' => $this->uid],
			'logs'
		);
		
		toast('Term Deleted', 'Term sucesfully deleted', 'text-success');
	}
	
	public function mealsInTerm() {
		$meals = new Meals();
		
		$mealsInTerm = $meals->betweenDates($this->date_start, $this->date_end);
		
		return $mealsInTerm;
	}
	
	public function tabLabel(?string $date = null): string {
		// Use today if no date provided
		$dateObj = $date ? new DateTime($date) : new DateTime();
			
		if ($this->isDateInTerm($date)) {
			$weekNumber = $this->weekNumber($dateObj);
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
	
	public function isDateInTerm(string|DateTime $date): bool {
		// Ensure $date is a DateTime object
		if (!$date instanceof DateTime) {
			$date = new DateTime($date);
		}
		
		// don't do week numbers in vacation
		if ($this->name == "Vacation") {
			return false;
		}
	
		$start = new DateTime($this->date_start);
		$end = new DateTime($this->date_end);
	
		return $date >= $start && $date <= $end;
	}
}
