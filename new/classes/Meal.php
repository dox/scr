<?php

class Meal extends Model {
	public $uid;
	  public $template;
	  public $name;
	  public $type;
	  public $date_meal;
	  public $date_cutoff;
	  public $location;
	  public $allowed;
	  public $domus;
	  public $charge_to;
	  public $allowed_wine;
	  public $allowed_dessert;
	  public $scr_capacity;
	  public $mcr_capacity;
	  public $scr_guests;
	  public $mcr_guests;
	  public $scr_dessert_capacity;
	  public $mcr_dessert_capacity;
	  public $menu;
	  public $notes;
	  public $photo;
	
	protected $db;
	protected static string $table = 'meals';
	
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
	
	public function name() {
		return $this->name;
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
	
	public function bookings(): array {
		global $db;
		
		$bookings = [];
	
		$sql  = "SELECT bookings.uid AS uid
				 FROM bookings
				 LEFT JOIN meals ON bookings.meal_uid = meals.uid
				 WHERE meals.uid = :uid
				 ORDER BY meals.date_meal DESC";
	
		$rows = $db->fetchAll($sql, ['uid' => $this->uid]);
	
		foreach ($rows as $row) {
			$bookings[] = new Booking($row['uid']);
		}
	
		return $bookings;
	}
	
	public function displayListGroupItem() {
		global $settingsClass;
	
		$meal = new meal($this->meal_uid);
	
		if (date('Y-m-d', strtotime($meal->date_meal)) == date('Y-m-d')) {
		  $class = "text-success";
		} else {
		  $class = "text-muted";
		}
	
		$output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
		$output .= "<div class=\"" . $class . " d-inline-block text-truncate\" style=\"max-width: 73%;\">";
		$output .= "<h6 class=\"my-0\">";
	
		// if admin, link to the meal itself, otherwise, link to the booking for the user
		if (checkpoint_charlie("meals")) {
		  $output .= "<a href=\"index.php?n=admin_meal&mealUID=" . $meal->uid . "\" class=\"" . $class . "\">" . $meal->name . "</a>";
		} else {
		  $output .= "<a href=\"index.php?n=booking&mealUID=" . $meal->uid . "\" class=\"" . $class . "\">" . $meal->name . "</a>";
		}
	
		$output .= "</h6>";
		$output .= "<small class=\"" . $class . "\">" . $meal->location . "</small>";
		$output .= "</div>";
		$output .= "<span class=\"" . $class . "\">" . dateDisplay($meal->date_meal) . "</span>";
		$output .= "</li>";
	
		return $output;
	  }

	public function delete() {
		if (!isset($this->id)) return false;
		return $this->db->query("DELETE FROM " . static::$table . " WHERE id = ?", [$this->id]);
	}
	
	public function photographURL(): string {
		// Path relative to the public folder (for browser)
		$urlPath = '/new/uploads/meal_cards/';
	
		// Full filesystem path for file_exists()
		$filePath = $_SERVER['DOCUMENT_ROOT'] . $urlPath;
	
		// Use default if photo is empty
		$filename = $this->photo ?: 'default.png';
	
		// If the file does not exist on disk, fall back to default
		if (!file_exists($filePath . $filename)) {
			$filename = 'default.png';
		}
	
		return $urlPath . $filename;
	}
	
	public function card() {
		$output  = "<div class=\"col mb-3\">";
		$output .= "<div class=\"card mb-3\">";
		$output .= "<img src=\"" . $this->photographURL() . "\" class=\"card-img-top\" alt=\"...\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<p class=\"card-text\">" . $this->name . "</p>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
}
