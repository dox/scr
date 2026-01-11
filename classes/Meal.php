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
		} else {
			//$this->template;
			$this->name = "";
			$this->type;
			$this->date_meal = date('Y-m-d 12:30:00');
			$this->date_cutoff = date('Y-m-d 10:00:00', strtotime('1 day ago'));
			$this->location = "";
			$this->allowed;
			$this->domus;
			$this->charge_to;
			$this->allowed_wine = 0;
			$this->allowed_dessert;
			$this->scr_capacity = 0;
			//$this->mcr_capacity;
			$this->scr_guests = 0;
			//$this->mcr_guests;
			$this->scr_dessert_capacity = 0;
			//$this->mcr_dessert_capacity;
			$this->menu = "";
			$this->notes = "";
			$this->photo;
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
		global $db, $log;
	
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
		
		// Handle checkboxes / arrays (dietary, permissions)
		$fields['allowed'] = isset($postData['allowed']) 
			? implode(',', array_filter($postData['allowed'])) 
			: '';
	
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
		
		// write the log
		$log->add('Meal updated for ' . $this->name, Log::SUCCESS);
		
		toast('Meal Updated', 'Meal sucesfully updated', 'text-success');
		
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
	
	public function late_bookings() {
		global $db;
		
		$bookings = [];
		
		$sql = "
			SELECT bookings.uid AS uid
			FROM bookings
			INNER JOIN meals ON bookings.meal_uid = meals.uid
			LEFT JOIN members ON bookings.member_ldap = members.ldap
			WHERE meals.uid = :uid
			  AND bookings.date > meals.date_cutoff
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
		
		if (!$this->allowed_dessert) {
			return $count;
		}
	
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
	
	public function allowedGroups(): array {
		if (empty($this->allowed)) {
			return [];
		}
	
		$groups = array_filter(array_map('trim', explode(',', $this->allowed)));
	
		return $groups;
	}
	
	public function delete() {
		global $db, $log;
		
		if (!isset($this->uid)) {
			return false;
		}
		
		$bookings = $this->bookings();
		
		// Delete bookings
		foreach ($bookings as $booking) {
			$booking->delete();
		}
		
		// Delete meal
		$db->delete(
			static::$table,
			['uid' => $this->uid],
			false
		);
		
		// write the log
		$log->add('Meal UID: ' . $this->uid . ' and ' . count($bookings) . '  booking(s) deleted', 'MEALS', Log::WARNING);
	}
	
	public function photographURL(): string {
		$urlPath  = '/new/uploads/meal_cards/';
		$filePath = $_SERVER['DOCUMENT_ROOT'] . $urlPath;
	
		// Choose the candidate filename (null or empty means default)
		$filename = trim((string) $this->photo);
	
		// If no filename at all, skip straight to default
		if ($filename === '') {
			return './assets/images/card_default.png';
		}
	
		// If file exists, return its public path; otherwise, fall to default
		return file_exists($filePath . $filename)
			? './uploads/meal_cards/' . $filename
			: './assets/images/card_default.png';
	}
	
	public function menuTooltip() {
		$output = "";
		
		if (!empty($this->menu)) {
			$output  = "<a href=\"javascript:void(0)\" class=\"load-remote-menu\"
			data-url=\"./ajax/menu_modal.php?mealUID=" . $this->uid . "\"
			data-bs-toggle=\"modal\"
			data-bs-target=\"#menuModal\">";
			$output .= "<i class=\"bi bi-info-circle\"></i>";
			$output .= "</a>";
		}
		
		return $output;
	}
	
	public function cleanMenu(): string {
		// Remove figure blocks (images, captions, etc.)
		$html = preg_replace('/<figure\b[^>]*>.*?<\/figure>/si', '', $this->menu);
	
		// Convert line breaks and paragraphs into real newlines
		$html = preg_replace('/<(br|\/p|\/div)>/i', "\n", $html);
	
		// Strip all remaining tags
		$text = strip_tags($html);
	
		// Decode HTML entities (&nbsp; &amp; etc.)
		$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	
		// Normalise whitespace
		$text = preg_replace("/\r\n|\r/", "\n", $text); // Windows/Mac
		$text = preg_replace("/\n{3,}/", "\n\n", $text); // Max two newlines
		$text = trim($text);
	
		return $text;
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
		$capacity   = (int) $this->scr_capacity;
		$percentage = $capacity > 0 ? round(($booked / $capacity) * 100) : 0;
	
		if ($percentage >= 100) {
			$class = "bg-danger";
		} elseif ($percentage >= 80) {
			$class = "bg-warning";
		} else {
			$class = "bg-info";
		}
	
		$output  = '<div class="d-flex align-items-center justify-content-between">';
		$output .= '<span>Bookings</span>';
		$output .= '<span class="booking-count" data-capacity="' . $capacity . '">';
		$output .= $booked . ' of ' . $capacity;
		$output .= '</span>';
		$output .= '</div>';
	
		$output .= '<div class="progress mb-3" style="height: 6px;" role="progressbar" ';
		$output .= 'data-capacity="' . $capacity . '" ';
		$output .= 'aria-valuenow="' . $booked . '" ';
		$output .= 'aria-valuemin="0" ';
		$output .= 'aria-valuemax="' . $capacity . '">';
	
		$output .= '<div class="progress-bar ' . $class . '" style="width: ' . $percentage . '%"></div>';
		$output .= '</div>';
	
		return $output;
	}
	
	public function bookingButton(): string {
		$booking = Booking::fromMealUID($this->uid);
		
		$text = $this->getBookingButtonText();
		$class = $this->getBookingButtonClass();
		$href = $this->getBookingButtonLink();
		
		// Determine if already booked
		$dataBooked = $booking->exists() ? '1' : '0';
	
		return sprintf(
			'<a href="%s" class="btn btn-sm %s w-100 meal-book-btn" data-meal_uid="%s" data-booked="%s">%s</a>',
			htmlspecialchars($href),
			htmlspecialchars($class),
			htmlspecialchars($this->uid),
			$dataBooked,
			htmlspecialchars($text)
		);
	}
	
	private function getBookingButtonText(): string {
		$booking = Booking::fromMealUID($this->uid);
	
		if ($booking->exists()) {
			return "Manage Booking";
		}
	
		if (!$this->isCutoffValid(false)) {
			return "Deadline Passed";
		}
	
		if (!$this->hasCapacity(false)) {
			return "Capacity Reached";
		}
		
		if (!$this->isAllowedGroupsValid(false)) {
			return "Restricted Meal";
		}
	
		return "Book Meal";
	}
	
	private function getBookingButtonClass(): string {
		global $user;
		
		$booking = Booking::fromMealUID($this->uid);
	
		if ($booking->exists()) {
			return "btn-success";
		}
		
		if (!$this->isCutoffValid(false) || !$this->hasCapacity(false) || !$this->isAllowedGroupsValid(false)) {
			if ($user->hasPermission("bookings")) {
				return "btn-secondary";
			} else {
				return "btn-secondary disabled";
			}
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
	public function hasCapacity(bool $factorInAdmin = true): bool {
		global $user;
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
	
		return $this->totalDiners() < $this->scr_capacity;
	}
	
	public function hasDessertCapacity(bool $factorInAdmin = true): bool {
		global $user;
		
		if (!$this->allowed_dessert) {
			return false;
		}
		
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
		
		return $this->totalDessertDiners() < $this->scr_dessert_capacity;
	}
	
	public function hasGuestDessertCapacity(int $guests, bool $factorInAdmin = true): bool {
		global $user;
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
	
		if (!$this->allowed_dessert) {
			return false;
		}
		
		$totalIfAdded = $this->totalDessertDiners() + $guests + 1;
		
		// true only if adding would NOT exceed capacity
		return $totalIfAdded <= $this->scr_dessert_capacity;
	}
	
	public function isCutoffValid(bool $factorInAdmin = true): bool {
		global $user;
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
	
		if ($this->date_cutoff) {
			return new DateTime() < new DateTime($this->date_cutoff);
		}
		
		return false;
	}
	
	public function isAllowedGroupsValid(bool $factorInAdmin = true): bool {
		global $user;
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
	
		$member = Member::fromLDAP($user->getUsername());
			
		if (empty($this->allowedGroups())) {
			return true;
		}
		
		if (in_array($member->category, $this->allowedGroups())) {
			return true;
		}
		
		return false;
	}
	
	public function hasGuestCapacity(int $existingGuests, bool $factorInAdmin = true): bool {
		global $user;
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
		
		if ($this->totalDiners() < $this->scr_capacity) {
			if ($existingGuests < $this->scr_guests) {
				return true;
			}
		}
		
		return false;
	}
	
	public function canBook(bool $factorInAdmin = true): bool {
		global $user;
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
	
		return $this->hasCapacity()
			// && $this->hasDessertCapacity()
			&& $this->isCutoffValid()
			&& $this->isAllowedGroupsValid();
	}
}
