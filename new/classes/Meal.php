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
	
	public function menuTooltip() {
		$output = "";
		
		if (!empty($this->menu)) {
			$output  = "<a href=\"#\" class=\"\" id=\"menuUID-" . $this->uid . "\" data-bs-toggle=\"modal\" data-bs-target=\"#menuModal\" onclick=\"displayMenu(this.id)\">";
			$output .= "<i class=\"bi bi-info-circle\"></i>";
			$output .= "</a>";
		}
		
		return $output;
	}
	
	public function card() {
		global $user;
		
		$mealURL = "index.php?page=meal&uid=" . $this->uid;
		
		$output  = "<div class=\"col mb-3\">";
		$output .= "<div class=\"card\">";
		$output .= "<img src=\"" . $this->photographURL() . "\" class=\"card-img-top\" alt=\"...\">";
		
		$output .= "<div class=\"card-body\">";
			$output .= "<div class=\"d-flex justify-content-between align-items-start mb-2\">";
				$output .= $user->hasPermission("meals")
					? "<h5 class=\"card-title mb-0\"><a href=\"$mealURL\" class=\"text-decoration-none \">" . $this->name . "</a></h5>"
					: "<h5 class=\"card-title mb-0\">" . $this->name . "</h5>";
				
				$output .= $this->menuTooltip(); // assumes this returns the <a> with SVG
			$output .= "</div>"; // end title row
			
			$output .= "<ul class=\"list-unstyled small text-muted mb-3\">";
				$output .= "<li><strong>" . $this->type . "</strong>, " . $this->location . " – " . formatTime($this->date_meal) . "</li>";
			$output .= "</ul>";
			
			// Progress bars
			//$output .= $this->progressBar("Dinner");
			
			//if ($this->scr_dessert_capacity > 0 && $this->total_dessert_bookings_this_meal('SCR') >= $this->scr_dessert_capacity) {
			//  $output .= $this->progressBar("Dessert");
			//}
			
			$output .= "</div>"; // end card-body
			
			$output .= "<div class=\"card-footer bg-transparent border-0 pt-0\">";
				//$output .= $this->bookingButton();
			$output .= "</div>"; // end footer
			
		$output .= "</div>"; // end card
		$output .= "</div>"; // end column
		
		return $output;
	}
}
