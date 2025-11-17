<?php

class Booking extends Model {
	public $uid;
	public $type;
	public $date;
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
		$output .= '<span class="' . $class . '">' . "+??" . '</span>';
		$output .= '</li>';
	
		return $output;
	}
	
	public function displayMemberListGroupItem(): string {
		global $user;
	
		$meal = new Meal($this->meal_uid);
	
		// Determine text class: green if today, muted otherwise
		$class = (date('Y-m-d', strtotime($meal->date_meal)) === date('Y-m-d')) 
			? 'text-success' 
			: 'text-muted';
	
		// Determine link: admin sees meal, others see booking
		$linkUrl = $user->hasPermission('meals')
			? "index.php?page=meal&uid=" . urlencode($meal->uid)
			: "index.php?page=booking&uid=" . urlencode($meal->uid);
	
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
		if (!isset($this->id)) return false;
		return $this->db->query("DELETE FROM " . static::$table . " WHERE id = ?", [$this->id]);
	}
}
