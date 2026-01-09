<?php

class Member extends Model {
	public ?int $uid = null;
	public bool $enabled = false;
	public ?string $type = null;
	public string $ldap = 'Unknown';
	public ?string $permissions = null;
	public ?string $title = null;
	public string $firstname = 'Unknown';
	public string $lastname = 'Unknown';
	public ?string $category = null;
	public ?int $precedence = 0;
	public ?string $email = 'unknown@unknown.com';
	public ?string $dietary = null;
	public bool $opt_in = false;
	public bool $email_reminders = false;
	public ?string $default_wine_choice = null;
	public ?string $default_dessert = null;
	public ?string $date_lastlogon = null;
	public ?string $calendar_hash = null;

	protected $db;
	protected static string $table = 'members';
	
	protected static function fromField(
		?string $value,
		string $field,
		string $pattern
	): self {
		if ($value === null || !preg_match($pattern, $value)) {
			return new self();
		}
		
		global $db;
		
		$row = $db->fetch(
			"SELECT * FROM " . static::$table . " WHERE {$field} = ?",
			[$value]
		);
		
		return $row ? self::hydrate($row) : new self();
	}
	
	protected static function hydrate(array $row): self {
		$self = new self();
		
		foreach ($row as $key => $value) {
			if (property_exists($self, $key)) {
				$self->$key = $value;
			}
		}
		
		return $self;
	}
	
	public static function fromLDAP(?string $ldap): self {
		return self::fromField($ldap, 'ldap', '/^[a-zA-Z0-9._-]+$/');
	}
	public static function fromUID(?string $uid): self {
		return self::fromField($uid, 'uid', '/^[0-9]+$/');
	}
	public static function fromHash(?string $hash): self {
		return self::fromField($hash, 'calendar_hash', '/^[a-zA-Z0-9._-]+$/');
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
		
		$imploded = html_entity_decode(implode(' ', $parts), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		
		return $imploded;
	}
	
	public function public_displayName(): string {
		global $user;
	
		// Base name
		if ($user->hasPermission("members") || $this->opt_in == 1) {
			$name = $this->name();
		} else {
			$name = "Hidden";
		}
	
		// Add "(You)" if this is the current user
		if ($user->getUsername() === $this->ldap) {
			$name .= ' <i>(You)</i>';
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
	
	public function bookingsBetweenDates($start, $end): array {
		global $db;
	
		$start = $start instanceof DateTime
			? $start->format('Y-m-d 00:00:00')
			: date('Y-m-d 00:00:00', strtotime($start));
	
		$end = $end instanceof DateTime
			? $end->format('Y-m-d 23:59:59')
			: date('Y-m-d 23:59:59', strtotime($end));
	
		$sql = "
			SELECT b.uid
			FROM bookings b
			JOIN meals m ON m.uid = b.meal_uid
			WHERE UPPER(b.member_ldap) = :ldap
			  AND m.date_meal BETWEEN :start AND :end
			ORDER BY m.date_meal ASC
		";
		
		$rows = $db->fetchAll($sql, [
			'ldap' => $this->ldap,
			'start' => $start,
			'end'   => $end,
		]);
		
		$bookings = [];
		foreach ($rows as $row) {
			$bookings[] = Booking::fromUID($row['uid']);
		}
		
		return $bookings;
	}
	
	public function countBookingsByTypeBetweenDates($start, $end): array {
		global $db;
	
		// Normalize to YYYY-MM-DD
		if ($start instanceof DateTime) {
			$start = $start->format('Y-m-d');
		}
		if ($end instanceof DateTime) {
			$end = $end->format('Y-m-d');
		}
	
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
		global $db, $user, $log;
	
		// Map normal text/select fields
		$fields = [
			'title'      => $postData['title'] ?? null,
			'firstname'  => $postData['firstname'] ?? null,
			'lastname'   => $postData['lastname'] ?? null,
			'email'      => $postData['email'] ?? null,
		];
		
		// Handle checkboxes / arrays (dietary, permissions)
		$fields['dietary'] = isset($postData['dietary']) 
			? implode(',', array_filter($postData['dietary'])) 
			: '';
		
		// Handle switches (checkboxes that may not be submitted)
		$fields['opt_in']         = isset($postData['opt_in']) ? 1 : 0;
		$fields['email_reminders'] = isset($postData['email_reminders']) ? 1 : 0;
		$fields['default_dessert'] = isset($postData['default_dessert']) ? 1 : 0;
		
		// Handle radio buttons
		$fields['default_wine_choice'] = $postData['default_wine_choice'] ?? 'None';
		
		// Map privileged text/select fields
		if ($user->hasPermission("member")) {
			$fields = array_merge($fields, [
				'ldap'       => $postData['ldap'] ?? null,
				'category'   => $postData['category'] ?? null,
				'type'       => $postData['type'] ?? null,
				'enabled'    => isset($postData['enabled']) ? (int)$postData['enabled'] : 0,
			]);
			
			if ($user->hasPermission("global_admin")) {
				$fields['permissions'] = isset($postData['permissions']) ? implode(',', array_filter($postData['permissions'])) : '';
			}
		}
		
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
		
		// write the log
		$log->add('Profile updated for ' . $this->name(), Log::SUCCESS);
		
		toast('Member Updated', 'Member sucesfully updated', 'text-success');
		
		return $updatedRows;
	}
	
	public function updatePosition(int $precedence) {
		global $db;
	
		// Map normal text/select fields
		$fields = [
			'precedence'      => $precedence ?? '999'
		];
		
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid]
		);
		
		//toast('Member Precedence Updated', 'Member precedence sucesfully updated', 'text-success');
		
		return $updatedRows;
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
	
	public function bookingsCount(): int {
		global $db;
	
		$sql = "
			SELECT COUNT(*) AS total
			FROM bookings
			WHERE member_ldap = :member_ldap
		";
	
		$row = $db->fetch($sql, ['member_ldap' => $this->ldap]);
	
		return (int)($row['total'] ?? 0);
	}

	public function delete() {
		global $db;
		
		if (!isset($this->uid)) {
			return false;
		}
		
		// Send to database to delete bookings
		$deleteBookings = $db->delete(
			'bookings',
			['member_ldap' => $this->ldap],
			'logs'
		);
		
		// Send to database to delete bookings
		$deleteMember = $db->delete(
			static::$table,
			['uid' => $this->uid],
			'logs'
		);
		
		toast('Member Deleted', 'Member sucesfully updated', 'text-success');
		
		return true;
	}
	
	public function lastLogon(): ?string{
		return isset($this->date_lastlogon)
			? $this->date_lastlogon
			: null;
	}
}
