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
	
	protected function __construct() {
		$this->uid = null;
		$this->enabled = false;
		$this->type = null;
		$this->ldap = "Unknown";
		$this->permissions = [];
		$this->title = null;
		$this->firstname = "Unknown";
		$this->lastname = "Unknown";
		$this->category = null;
		$this->precedence = 0;
		$this->email = "unknown@unknown.com";
		$this->dietary = null;
		$this->opt_in = false;
		$this->email_reminders = false;
		$this->default_wine_choice = null;
		$this->default_dessert = null;
		$this->date_lastlogon = null;
		$this->calendar_hash = null;
	}
	
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
	
	public function permissions() {
		// If $this->permissions is empty/null, return an empty array
		if (empty($this->permissions)) {
			return [];
		}
	
		// Otherwise, split the comma-separated string into an array
		return array_map('trim', explode(',', $this->permissions));
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
	
	public function countBookingsByTypeBetweenDates($start, $end): array {
		global $db;
	
		// Normalize to YYYY-MM-DD
		if ($start instanceof DateTime) $start = $start->format('Y-m-d');
		if ($end instanceof DateTime) $end = $end->format('Y-m-d');
	
		$sql = "
			SELECT m.type, COUNT(*) AS total
			FROM bookings b
			INNER JOIN meals m ON b.meal_uid = m.uid
			WHERE b.member_ldap = ?
			  AND m.date_meal >= ? 
			  AND m.date_meal <= ?
			GROUP BY m.type
			ORDER BY total DESC
		";
	
		$results = $db->fetchAll($sql, [$this->ldap, $start, $end]);
		
		return $results;
	}
	
	public function update(array $postData) {
		global $db;
	
		// Map normal text/select fields
		$fields = [
			'title'      => $postData['title'] ?? null,
			'firstname'  => $postData['firstname'] ?? null,
			'lastname'   => $postData['lastname'] ?? null,
			'ldap'       => $postData['ldap'] ?? null,
			'category'   => $postData['category'] ?? null,
			'email'      => $postData['email'] ?? null,
			'type'       => $postData['type'] ?? null,
			'enabled'    => isset($postData['enabled']) ? (int)$postData['enabled'] : 0,
		];
	
		// Handle checkboxes / arrays (dietary, permissions)
		$fields['dietary'] = isset($postData['dietary']) 
			? implode(',', array_filter($postData['dietary'])) 
			: '';
			
		$fields['permissions'] = isset($postData['permissions']) 
			? implode(',', array_filter($postData['permissions'])) 
			: '';
	
		// Handle switches (checkboxes that may not be submitted)
		$fields['opt_in']         = isset($postData['opt_in']) ? 1 : 0;
		$fields['email_reminders'] = isset($postData['email_reminders']) ? 1 : 0;
		$fields['default_dessert'] = isset($postData['default_dessert']) ? 1 : 0;
	
		// Handle radio buttons
		$fields['default_wine_choice'] = $postData['default_wine_choice'] ?? 'None';
	
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
	
		return $updatedRows;
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
	
		// Monday-first order
		$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
	
		// Query total meals by day and type
		$sql = "
			SELECT 
				DAYOFWEEK(meals.date_meal) AS dayofweek, 
				meals.type, 
				COUNT(*) AS totalMeals
			FROM bookings
			LEFT JOIN meals ON bookings.meal_uid = meals.uid
			WHERE member_ldap = ?
			GROUP BY DAYOFWEEK(meals.date_meal), meals.type
		";
	
		$bookings = $db->fetchAll($sql, [$this->ldap]);
	
		$returnArray = [];
	
		foreach ($bookings as $booking) {
			$type = $booking['type'];
			// Convert MySQL 1=Sunday … 7=Saturday to Monday-first index 0..6
			$index = ($booking['dayofweek'] + 5) % 7; 
			$dayName = $days[$index];
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
