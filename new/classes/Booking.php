<?php
class Booking extends Model {
	public $uid;
	public $date;
	public $type;
	public $meal_uid;
	public $member_ldap;
	public $guests_array; // json encoded array
	public $charge_to;
	public $domus_reason;
	public $wine; // retired
	public $wine_choice;
	public $dessert;
	
	protected $db;
	protected static string $table = 'bookings';
	
	public function __construct() {}
	
	public static function fromUID(?string $uid): self {
		global $db;
	
		$self = new self();
	
		// Guard format
		if ($uid === null || !preg_match('/^[0-9._-]+$/', $uid)) {
			return $self;
		}
		
		$query = "SELECT * FROM " . static::$table . " 
				  WHERE uid = ?";
		
		$row = $db->fetch($query, [$uid]);
	
		if ($row) {
			foreach ($row as $key => $value) {
				$self->$key = $value;
			}
		}
		
		return $self;
	}
	
	public static function fromMealUID(?string $mealUID): self {
		global $db, $user;
	
		$self = new self();
	
		// Guard format
		if ($mealUID === null || !preg_match('/^[0-9._-]+$/', $mealUID)) {
			return $self;
		}
		
		// Both boundaries must hold
		$query = "SELECT * FROM " . static::$table . " 
				  WHERE meal_uid = ? AND member_ldap = ?";
		$row = $db->fetch($query, [$mealUID, $user->getUsername()]);
	
		if ($row) {
			foreach ($row as $key => $value) {
				$self->$key = $value;
			}
		}
	
		return $self;
	}
	
	public function exists(): bool {
		return !empty($this->uid);
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
			'charge_to'      => $postData['charge_to'] ?? null,
			'domus_reason'  => $postData['domus_reason'] ?? null,
			'wine_choice'   => $postData['wine_choice'] ?? null,
			'dessert'   => $postData['dessert'] ?? 0
		];
	
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
	
		return $updatedRows;
	}
	
	public function addGuest(array $postData): bool|array {
		global $db;
	
		// generate guest uid
		$guestUid = 'x' . bin2hex(random_bytes(5));
	
		// sanitise fields
		$guest = ['guest_uid' => $guestUid];
		foreach ($postData as $key => $value) {
			$guest[$key] = ($key === 'guest_dietary')
				? $value
				: htmlspecialchars(trim($value), ENT_QUOTES);
		}
	
		// push into JSON column
		$payload = json_encode($guest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
		$sql = "
			UPDATE " . self::$table . "
			SET guests_array = JSON_SET(COALESCE(guests_array, '{}'), '$.\"{$guestUid}\"', '{$payload}')
			WHERE uid = :uid
			LIMIT 1
		";
	
		$db->query($sql, [':uid' => $this->uid]);
	
		// write the log
		/*$logsClass->create([
			'category'    => 'booking',
			'result'      => 'success',
			'description' =>
				"Guest '{$guest['guest_name']}' of {$member->displayName()} added to ".
				"[bookingUID:{$this->uid}] for [mealUID:{$this->meal_uid}]. ".
				"Dessert: {$this->dessert} Wine: {$guest['guest_wine_choice']}"
		]);*/
	
		// return updated structure
		return true;
	}
	
	public function editGuest(array $postData): bool|array {
		global $db;
	
		// existing guest UID must be present
		if (empty($postData['guest_uid'])) {
			return ['error' => 'Missing guest UID'];
		}
	
		$guestUid = $postData['guest_uid'];
	
		// sanitise fields
		$guest = ['guest_uid' => $guestUid];
		foreach ($postData as $key => $value) {
			$guest[$key] = ($key === 'guest_dietary')
				? $value
				: htmlspecialchars(trim($value), ENT_QUOTES);
		}
	
		// push into JSON column (overwrite this guest entry)
		$payload = json_encode($guest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
		$sql = "
			UPDATE " . self::$table . "
			SET guests_array = JSON_SET(guests_array, '$.\"{$guestUid}\"', '{$payload}')
			WHERE uid = :uid
			LIMIT 1
		";
	
		$db->query($sql, [':uid' => $this->uid]);
	
		return true;
	}
	
	public function deleteGuest(string $guestUid): bool {
		global $db;
	
		// Remove the guest key entirely from the JSON structure
		$sql = "
			UPDATE " . self::$table . "
			SET guests_array = JSON_REMOVE(guests_array, '$.\"{$guestUid}\"')
			WHERE uid = :uid
			LIMIT 1
		";
	
		$db->query($sql, [
			':uid' => $this->uid
		]);
	
		return true;
	}
	
	public function guests(): array {
		if (empty($this->guests_array)) {
			return [];
		}
	
		$outer = json_decode($this->guests_array, true);
		if (!is_array($outer)) {
			return [];
		}
	
		// Decode inner JSON and filter out empty elements
		$decoded = array_filter(array_map(function($jsonValue) {
			$guest = json_decode($jsonValue, true);
			return is_array($guest) && !empty(array_filter($guest)) ? $guest : null;
		}, $outer));
	
		return $decoded;
	}
	
	public function wineChoice(): string {
		$meal = new Meal($this->meal_uid);
		
		if ($meal->allowed_wine != 1) {
			return "None";
		} else {
			return $this->wine_choice;
		}
	}
	
	public function dessertChoice(): string {
		$meal = new Meal($this->meal_uid);
		
		if ($meal->allowed_dessert != 1) {
			return "0";
		} else {
			return $this->dessert;
		}
	}
	
	public function displayMealListGroupItem(): string {
		global $user;
	
		$meal = new Meal($this->meal_uid);
		$member = Member::fromLDAP($this->member_ldap);
		
		// Determine text class: green if today, muted otherwise
		$class = (date('Y-m-d H:i', strtotime($this->date)) > date('Y-m-d H:i', strtotime($meal->date_cutoff))) 
			? 'text-danger' 
			: 'text-muted';
	
		// Determine link: admin sees meal, others see booking
		$linkUrl = "index.php?page=member&ldap=" . urlencode($member->ldap);
	
		$mealName     = htmlspecialchars($meal->name, ENT_QUOTES);
		$mealLocation = htmlspecialchars($meal->location, ENT_QUOTES);
		$mealDate     = formatDate($meal->date_meal, 'short');
	
		$output  = '<li class="list-group-item d-flex justify-content-between lh-sm">';
		$output .= '<div class="' . $class . ' d-inline-block text-truncate" style="max-width: 73%;">';
		$output .= '<h6 class="my-0">';
		$output .= '<a href="' . $linkUrl . '" class="' . $class . '">' . $member->name() . '</a>';
		$output .= '</h6>';
		$output .= '<small class="' . $class . '">' . formatDate($this->date, 'short') . " " . formatTime($this->date) . '</small>';
		$output .= '</div>';
		
		if (count($this->guests()) > 0) {
			$output .= '<span class="' . $class . '">' . "+" . count($this->guests()) . '</span>';
		}
		
		$output .= '</li>';
	
		return $output;
	}
	
	public function displayMemberListGroupItem(): string {
		$meal = new Meal($this->meal_uid);
		
		// Determine text class: green if today, muted otherwise
		$class = (date('Y-m-d', strtotime($meal->date_meal)) === date('Y-m-d'))
			? 'text-success'
			: 'text-muted';
		
		// Determine link: admin sees meal, others see booking
		$linkUrl = "index.php?page=booking&uid=" . urlencode($this->uid);
		
		$mealName     = htmlspecialchars($meal->name, ENT_QUOTES);
		$mealLocation = htmlspecialchars($meal->location, ENT_QUOTES);
		$mealDate     = formatDate($meal->date_meal, 'short');
		
		$output  = '<li class="list-group-item d-flex justify-content-between lh-sm">';
		$output .= '<div class="' . $class . ' d-inline-block text-truncate" style="max-width: 73%;">';
		$output .= '<h6 class="my-0">';
		$output .= '<a href="' . $linkUrl . '" class="' . $class . '">' . $mealName . '</a>';
		$output .= '</h6>';
		$output .= '<small class="' . $class . '">' . $mealLocation . '</small>';
		$output .= '</div>';
		$output .= '<span class="' . $class . '">' . $mealDate . '</span>';
		$output .= '</li>';
		
		return $output;
	}
	
	public function delete() {
		global $db;
		
		if (!isset($this->uid)) return false;
		
		// Send to database to delete bookings
		$deleteBooking = $db->delete(
			'bookings',
			['uid' => $this->uid],
			'logs'
		);
		
		return true;
	}
}
