<?php

class Member extends Model {
	public $uid;
	public $enabled;
	public $type;
	public $ldap;
	public $permissions;
	public $title;
	public $firstname;
	public $lastname;
	public $category;
	public $precedence;
	public $email;
	public $dietary;
	public $opt_in;
	public $email_reminders;
	public $default_wine; // retired
	public $default_wine_choice;
	public $default_dessert;
	public $date_lastlogon;
	public $calendar_hash;

	protected $db;
	protected static string $table = 'members';
	
	protected function __construct() {} // keep raw creation private
	
	public static function fromLDAP(?string $ldap): self {
		global $db;
		
		$self = new self();
		
		// First guard: format
		if ($ldap === null || !preg_match('/^[a-zA-Z0-9._-]+$/', $ldap)) {
			return $self; // returns invalid
		}
	
		// Lookup
		$query = "SELECT * FROM " . static::$table . " WHERE ldap = ?";
		$row = $db->fetch($query, [$ldap]);
		
		
		if ($row) {
			foreach ($row as $key => $value) {
				$self->$key = $value;
			}
			
			return $self;
		}
		
		if (!$row) {
			return $self;
		}
	}
	
	public static function fromUID(?string $uid): self {
		global $db;
		
		$self = new self();
		
		// First guard: format
		if ($uid === null || !preg_match('/^[0-9._-]+$/', $uid)) {
			return $self; // returns invalid
		}
	
		// Lookup
		$query = "SELECT * FROM " . static::$table . " WHERE uid = ?";
		$row = $db->fetch($query, [$uid]);
		
		
		if ($row) {
			foreach ($row as $key => $value) {
				$self->$key = $value;
			}
			
			return $self;
		}
		
		if (!$row) {
			return $self;
		}
	}
	
	public function name(): string {
		$parts = [];
	
		if (!empty($this->title)) {
			$parts[] = $this->title;
		}
	
		if (!empty($this->firstname)) {
			$parts[] = $this->firstname;
		}
	
		if (!empty($this->lastname)) {
			$parts[] = $this->lastname;
		}
	
		return implode(' ', $parts);
	}
	
	public function public_displayName() {
		if ($user->hasPermission("members")) {
			$name = $this->name();
		} else {
			if ($this->opt_in == 1) {
				$name = $this->name();
			} else {
				$name  = "<div class=\"col-6\">";
				$name .= "<span class=\"placeholder col-1\"></span> ";
				$name .= "<span class=\"placeholder col-2\"></span> ";
				$name .= "<span class=\"placeholder col-3\"></span>";
				$name .= "</div>";
			}
		}
	
		if ($this->ldap == $_SESSION['username']) {
			$name = $this->name() . " <i>(You)</i>";
		}
		
		return $name;
	}
	
	public function isSteward() {
		global $settings;
	
		$arrayofStewards = explode(",", strtoupper($settings->get('member_steward')));
	
		if (in_array(strtoupper($this->ldap), $arrayofStewards)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function stewardBadge() {
		if ($this->isSteward()) {
			$badge = " <span class=\"badge bg-warning\">SCR Steward</span>";
	
			return $badge;
		}
	}
	
	public function upcomingBookings(): array {
		global $db;
		
		$bookings = [];
	
		$sql  = "SELECT bookings.uid AS uid
				 FROM bookings
				 LEFT JOIN meals ON bookings.meal_uid = meals.uid
				 WHERE meals.date_meal >= NOW()
				   AND bookings.member_ldap = :ldap
				 ORDER BY meals.date_meal DESC";
	
		$rows = $db->fetchAll($sql, ['ldap' => $this->ldap]);
	
		foreach ($rows as $row) {
			$bookings[] = new Booking($row['uid']);
		}
	
		return $bookings;
	}
	
	public function recentBookings(): array {
		global $db;
		
		$bookings = [];
	
		$sql  = "SELECT bookings.uid AS uid
				 FROM bookings
				 LEFT JOIN meals ON bookings.meal_uid = meals.uid
				 WHERE meals.date_meal < NOW()
				   AND bookings.member_ldap = :ldap
				 ORDER BY meals.date_meal DESC";
	
		$rows = $db->fetchAll($sql, ['ldap' => $this->ldap]);
	
		foreach ($rows as $row) {
			$bookings[] = new Booking($row['uid']);
		}
	
		return $bookings;
	}
	
	public function save() {
		if (isset($this->id)) {
			// update
			$sql = "UPDATE " . static::$table . " 
					SET user_id = ?, budget_code = ?, amount = ?, description = ?, invoice_path = ? 
					WHERE id = ?";
			return $this->db->query($sql, [
				$this->user_id, $this->budget_code, $this->amount,
				$this->description, $this->invoice_path, $this->id
			]);
		} else {
			// insert
			$sql = "INSERT INTO " . static::$table . " (user_id, budget_code, amount, description, invoice_path, created_at) 
					VALUES (?, ?, ?, ?, ?, NOW())";
			$this->db->query($sql, [
				$this->user_id, $this->budget_code, $this->amount,
				$this->description, $this->invoice_path
			]);

			$this->id = $this->db->lastInsertId();
			return $this->id;
		}
	}
	
	public function items() {
		$itemsArray = [];
		
		if (isset($this->items)) {
			$items = json_decode($this->items, true);
			
			foreach ($items AS $item) {
				$itemsArray[] = $item;
			}
		}
		
		return $itemsArray;
	}
	
	public function bookingsByDay(): array {
		global $db;
	
		$days = [1=>'Sunday',2=>'Monday',3=>'Tuesday',4=>'Wednesday',5=>'Thursday',6=>'Friday',7=>'Saturday'];
	
		// Query total meals by day and type
		$sql = "SELECT DAYOFWEEK(meals.date_meal) AS dayofweek, meals.type, COUNT(*) AS totalMeals
				FROM bookings
				LEFT JOIN meals ON bookings.meal_uid = meals.uid
				WHERE member_ldap = ?
				GROUP BY DAYOFWEEK(meals.date_meal), meals.type
				ORDER BY DAYOFWEEK(meals.date_meal) ASC";
	
		$bookings = $db->fetchAll($sql, [$this->ldap]);
		
		$returnArray = [];
	
		foreach ($bookings as $booking) {
			$type = $booking['type'];
			$dayName = $days[$booking['dayofweek']];
			$returnArray[$type][$dayName] = (int)$booking['totalMeals'];
		}
	
		// Fill in missing days with zeros
		foreach ($returnArray as $type => $daysArray) {
			foreach ($days as $dayName) {
				$returnArray[$type][$dayName] = $returnArray[$type][$dayName] ?? 0;
			}
		}
		
		return $returnArray;
	}

	public function delete() {
		if (!isset($this->id)) return false;
		return $this->db->query("DELETE FROM " . static::$table . " WHERE id = ?", [$this->id]);
	}
}
