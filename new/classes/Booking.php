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
		$output .= '<small class="' . $class . '">' . formatDate($this->date, 'short') . " " . formatTime($this->date, 'short') . '</small>';
		$output .= '</div>';
		
		if (count($this->guests()) > 0) {
			$output .= '<span class="' . $class . '">' . "+" . count($this->guests()) . '</span>';
		}
		
		$output .= '</li>';
	
		return $output;
	}
	
	public function displayMemberListGroupItem(): string {
		$meal = new Meal($this->meal_uid);
	
		return $meal->displayListGroupItem();
	}
}
