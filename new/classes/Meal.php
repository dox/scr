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
	
	public function update(array $postData) {
		global $db;
	
		// Map normal text/select fields
		$fields = [
			'type'      => $postData['type'] ?? null,
			'name'  => $postData['name'] ?? null,
			'location'   => $postData['location'] ?? null,
			'date_meal'   => $postData['date_meal'] ?? null,
			'date_cutoff'   => $postData['date_cutoff'] ?? null,
			'scr_capacity'   => $postData['scr_capacity'] ?? null,
			'scr_dessert_capacity'   => $postData['scr_dessert_capacity'] ?? null,
			'scr_guests'   => $postData['scr_guests'] ?? null,
			'menu'   => $postData['menu'] ?? null,
			'notes'   => $postData['notes'] ?? null,
			'photo'   => $postData['photo'] ?? null,
			'charge_to'   => $postData['charge_to'] ?? null,
			'allowed_wine'   => $postData['allowed_wine'] ?? '0',
			'allowed_dessert'   => $postData['allowed_dessert'] ?? '0'
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
	
	public function bookings(): array {
		global $db;
		
		$bookings = [];
	
		$sql = "
			SELECT bookings.uid AS uid
			FROM bookings
			LEFT JOIN meals ON bookings.meal_uid = meals.uid
			LEFT JOIN members ON bookings.member_ldap = members.ldap
			WHERE meals.uid = :uid
			ORDER BY members.precedence ASC
		";
	
		$rows = $db->fetchAll($sql, ['uid' => $this->uid]);
	
		foreach ($rows as $row) {
			$bookings[] = Booking::fromUID($row['uid']);
		}
	
		return $bookings;
	}
	
	public function totalDiners(): int {
		$count = 0;
	
		foreach ($this->bookings() as $booking) {
			// Each booking counts as one person
			$count++;
	
			// Guests stored as JSON array in $booking->guests_array
			// Decode, but guard against null or invalid content
			$guests = $booking->guests();
	
			if (is_array($guests)) {
				$count += count($guests);
			}
		}
	
		return $count;
	}
	
	public function totalDessertDiners(): int {
		$count = 0;
		
		if (!$this->allowed_dessert) return $count;
	
		foreach ($this->bookings() as $booking) {
			if ($booking->dessert) {
				// Each booking counts as one person
				$count++;
				
				// Any guests include dessert if the host diner is having dessert
				$count += count($booking->guests());
			}
		}
	
		return $count;
	}
	
	public function delete() {
		global $db;
		if (!isset($this->uid)) return false;
		
		// Delete bookings
		$db->delete(
			"bookings",
			['meal_uid' => $this->uid],
			'logs'
		);
		
		// Delete meal
		$db->delete(
			static::$table,
			['uid' => $this->uid],
			'logs'
		);
	}
	
	public function photographURL(): string {
		// Path relative to the public folder (for browser)
		$urlPath = '/new/uploads/meal_cards/';
	
		// Full filesystem path for file_exists()
		$filePath = $_SERVER['DOCUMENT_ROOT'] . $urlPath;
	
		// Use default if photo is empty
		$filename = $this->photo ?: 'generic.png';
	
		// If the file does not exist on disk, fall back to default
		if (!file_exists($filePath . $filename)) {
			$filename = 'generic.png';
		}
	
		return $urlPath . $filename;
	}
	
	public function menuTooltip() {
		$output = "";
		
		if (!empty($this->menu)) {
			$output  = "<a href=\"#\" class=\"load-remote-menu\" id=\"menuUID-" . $this->uid . "\" 
			data-url=\"./ajax/menu_modal.php?mealUID=" . $this->uid . "\"
			data-bs-toggle=\"modal\"
			data-bs-target=\"#menuModal\">";
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
				
				$output .= $this->menuTooltip();
			$output .= "</div>"; // end title row
			
			$output .= "<ul class=\"list-unstyled small text-muted mb-3\">";
				$output .= "<li><strong>" . $this->type . "</strong>, " . $this->location . " – " . formatTime($this->date_meal) . "</li>";
			$output .= "</ul>";
			
			// Progress bars
			$output .= $this->progressBar("Dinner");
			
			$output .= "</div>"; // end card-body
			
			$output .= "<div class=\"card-footer bg-transparent border-0 pt-0\">";
				$output .= $this->bookingButton();
			$output .= "</div>"; // end footer
			
		$output .= "</div>"; // end card
		$output .= "</div>"; // end column
		
		return $output;
	}
	
	public function progressBar(): string {
		$booked     = $this->totalDiners();
		$capacity   = (int)$this->scr_capacity;
		$percentage = $capacity > 0 ? ($booked / $capacity) * 100 : 0;
	
		if ($percentage >= 100) {
			$class = "bg-danger";
		} elseif ($percentage >= 80) {
			$class = "bg-warning";
		} else {
			$class = "bg-info";
		}
	
		$output  = "<div class=\"d-flex align-items-center justify-content-between\">";
		$output .= "<span>Bookings</span>";
		$output .= "<span>{$booked} of {$capacity}</span>";
		$output .= "</div>";
	
		$output .= "<div class=\"progress mb-3\" style=\"height: 6px;\" role=\"progressbar\" ";
		$output .= "aria-valuenow=\"{$booked}\" aria-valuemin=\"0\" aria-valuemax=\"{$capacity}\">";
		$output .= "<div class=\"progress-bar {$class}\" style=\"width: {$percentage}%\"></div>";
		$output .= "</div>";
	
		return $output;
	}
	
	private function getBookingButtonText(): string {
		$booking = Booking::fromMealUID($this->uid);
	
		if ($booking->exists()) {
			return "Manage Booking";
		}
	
		if (!$this->isCutoffValid()) {
			return "Deadline Passed";
		}
	
		if (!$this->hasCapacity()) {
			return "Capacity Reached";
		}
	
		return "Book Meal";
	}
	
	private function getBookingButtonClass(): string {
		$booking = Booking::fromMealUID($this->uid);
	
		if ($booking->exists()) {
			return "btn-success";
		}
	
		if (!$this->isCutoffValid() || !$this->hasCapacity()) {
			return "btn-secondary disabled";
		}
	
		return "btn-primary";
	}
	
	private function getBookingButtonLink(): string {
		$booking = Booking::fromMealUID($this->uid);
	
		if ($booking->exists()) {
			return "index.php?page=booking&uid=" . urlencode($booking->uid);
		}
	
		return "#";
	}
	
	public function bookingButton(): string {
		$text = $this->getBookingButtonText();
		$class = $this->getBookingButtonClass();
		$link = $this->getBookingButtonLink();
	
		return sprintf(
			'<a href="%s" id="mealUID-%s" class="btn btn-sm %s w-100">%s</a>',
			htmlspecialchars($link),
			htmlspecialchars($this->uid),
			$class,
			htmlspecialchars($text)
		);
	}
	
	private function bookingButton2() {
		$bookingsClass = new bookings();
		$userType = $_SESSION['type'] ?? '';
		$username = $_SESSION['username'] ?? '';
		$bookingExists = $bookingsClass->bookingExistCheck($this->uid, $username);
		
		$bookingLink = "#";
		$bookingClass = "btn-primary";
		$bookingOnClick = "onclick=\"bookMealQuick(this.id)\"";
		$bookingDisplayText = "Book Meal";
	  
		  // If a booking already exists
		  if ($bookingExists) {
			  $bookingLink = "index.php?n=booking&mealUID=" . $this->uid;
			  $bookingClass = "btn-success";
			  $bookingOnClick = "";
			  $bookingDisplayText = "Manage Booking";
		  } else {
			  // Booking does not exist: check eligibility in priority order
	  
			  if (!$this->check_member_ok()) {
				  $bookingClass = "btn-secondary disabled";
				  $bookingOnClick = "";
				  $bookingDisplayText = "Your account is disabled";
	  
			  } elseif (!$this->check_member_type_ok(true)) {
				  $bookingClass = "btn-secondary disabled";
				  $bookingOnClick = "";
				  $bookingDisplayText = "Restricted Meal";
	  
			  } elseif (!$this->check_capacity_ok()) {
				  if (checkpoint_charlie("bookings")) {
					  $bookingClass = "btn-warning";
					  $bookingDisplayText = "Capacity Reached";
				  } else {
					  $bookingClass = "btn-warning disabled";
					  $bookingOnClick = "";
					  $bookingDisplayText = "Capacity Reached";
				  }
	  
			  } elseif (!$this->check_cutoff_ok()) {
				  if (checkpoint_charlie("bookings")) {
					  $bookingClass = "btn-secondary";
					  $bookingDisplayText = "Deadline Passed";
				  } else {
					  $bookingClass = "btn-secondary disabled";
					  $bookingOnClick = "";
					  $bookingDisplayText = "Deadline Passed";
				  }
			  }
		  }
	  
		  // Build button HTML
		  $output  = "<a class=\"btn btn-sm {$bookingClass} w-100\" href=\"" . htmlspecialchars($bookingLink) . "\" ";
		  $output .= "id=\"mealUID-" . htmlspecialchars($this->uid) . "\" {$bookingOnClick}>";
		  $output .= htmlspecialchars($bookingDisplayText);
		  $output .= "</a>";
	  
		  return $output;
	  }
	
	public function displayListGroupItem(): string {
		global $user;
	
		// Determine text class: green if today, muted otherwise
		$class = (date('Y-m-d', strtotime($this->date_meal)) === date('Y-m-d')) 
			? 'text-success' 
			: 'text-muted';
	
		// Determine link: admin sees meal, others see booking
		$linkUrl = $user->hasPermission('meals')
			? "index.php?page=meal&uid=" . urlencode($this->uid)
			: "index.php?page=booking&uid=" . urlencode($this->uid);
	
		$mealName     = htmlspecialchars($this->name, ENT_QUOTES);
		$mealLocation = htmlspecialchars($this->location, ENT_QUOTES);
		$mealDate     = formatDate($this->date_meal, 'short');
	
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
	
	// Meal booking logic
	public function hasCapacity(): bool {
		return $this->totalDiners() < $this->scr_capacity;
	}
	
	public function hasDessertCapacity(): bool {
		if (!$this->allowed_dessert) return false;
		
		return $this->totalDessertDiners() < $this->scr_dessert_capacity;
	}
	
	public function isCutoffValid(): bool {
		return new DateTime() < new DateTime($this->date_cutoff);
	}
	
	public function hasGuestCapacity(): bool {
		// this needs to be a booking check??
		return $this->totalDiners() < $this->scr_guests;
	}
	
	public function canBook(): bool {
		return $this->hasCapacity()
			&& $this->hasDessertCapacity()
			&& $this->isCutoffValid()
			&& $this->hasGuestCapacity();
	}
}
