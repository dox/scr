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
	  public $capacity;
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
	
		// Map normal text/select fields (unchanged)
		$fields = [
			'type'      => $postData['type'] ?? null,
			'name'      => $postData['name'] ?? null,
			'location'  => $postData['location'] ?? null,
			'date_meal' => $postData['date_meal'] ?? null,
			'date_cutoff' => $postData['date_cutoff'] ?? null,
			'menu'      => $postData['menu'] ?? null,
			'notes'     => $postData['notes'] ?? null,
			'photo'     => $postData['photo'] ?? null,
			'charge_to' => $postData['charge_to'] ?? null,
			'allowed_wine' => $postData['allowed_wine'] ?? '0',
			'allowed_dessert' => $postData['allowed_dessert'] ?? '0'
		];
	
		// Handle checkboxes / arrays (dietary, permissions)
		$fields['allowed'] = isset($postData['allowed'])
			? implode(',', array_filter($postData['allowed']))
			: '';
	
		// Handle capacity[...] posted structure and write to `capacity` JSON column
		$capacityOut = [];
	
		if (isset($postData['capacity']) && is_array($postData['capacity'])) {
			foreach ($postData['capacity'] as $memberTypeRaw => $vals) {
				// Normalize key
				$memberType = trim((string)$memberTypeRaw);
				if ($memberType === '') {
					continue;
				}
	
				// Extract posted values, default to 0 if missing
				$mainRaw = $vals['capacity'] ?? 0;
				$dessertRaw = $vals['dessert_capacity'] ?? 0;
				$guestsRaw = $vals['guests'] ?? 0;
	
				// Basic validation & normalization
				$main = is_numeric($mainRaw) && intval($mainRaw) >= 0 ? (int)$mainRaw : 0;
				$dessert = is_numeric($dessertRaw) && intval($dessertRaw) >= 0 ? (int)$dessertRaw : 0;
				$guests = is_numeric($guestsRaw) && intval($guestsRaw) >= 0 ? (int)$guestsRaw : 0;
	
				$capacityOut[$memberType] = [
					'seating' => [
						'main' => $main,
						'dessert' => $dessert,
					],
					'guests' => [
						'max_per_member' => $guests,
					],
				];
			}
		}
	
		// Store capacity JSON into the dedicated 'capacity' DB column.
		// Add to the $fields array so the same $db->update handles it.
		$fields['capacity'] = json_encode($capacityOut, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $this->uid],
			'logs'
		);
	
		// write the log
		$log->add('Meal updated for ' . $this->name, Log::SUCCESS);
	
		toast('Meal Updated', 'Meal successfully updated', 'text-success');
	
		return $updatedRows;
	}
	
	public function bookings(?string $memberType = null): array {
		global $db, $user;
		
		// Default to current user's member type if not specified
		if ($memberType === null || trim($memberType) === '') {
			$memberType = $user->getMemberType();
		}
	
		$bookings = [];
	
		$params = ['uid' => $this->uid];
		$memberTypeSql = '';
	
		if ($memberType !== null && $memberType !== 'all' && trim($memberType) !== '') {
			$memberTypeSql = ' AND LOWER(members.type) = :member_type ';
			$params['member_type'] = strtolower(trim($memberType));
		}
	
		$sql = "
			SELECT bookings.uid AS uid
			FROM bookings
			LEFT JOIN meals   ON bookings.meal_uid = meals.uid
			LEFT JOIN members ON bookings.member_ldap = members.ldap
			WHERE meals.uid = :uid
			{$memberTypeSql}
			ORDER BY members.precedence ASC
		";
	
		$rows = $db->fetchAll($sql, $params);
	
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
	
	public function totalDiners(?string $memberType = null): int {
		global $user;
	
		// Default to current user's member type if not specified
		if ($memberType === null || trim($memberType) === '') {
			$memberType = $user->getMemberType();
		}
	
		$memberType = strtolower(trim($memberType));
		$count = 0;
	
		// Use the new filtered bookings() method
		foreach ($this->bookings($memberType) as $booking) {
	
			// Each booking counts as one member
			$count++;
	
			// Guests stored as JSON / array via $booking->guests()
			$guests = $booking->guests();
	
			if (is_array($guests)) {
				$count += count($guests);
			}
		}
	
		return $count;
	}
	
	public function totalDessertDiners(?string $memberType = null): int {
		global $user;
		
		$count = 0;
		
		if (!$this->allowed_dessert) {
			return $count;
		}
		
		// Default to current user's member type if not specified
		if ($memberType === null || trim($memberType) === '') {
			$memberType = $user->getMemberType();
		}
		
		$memberType = strtolower(trim($memberType));
	
		foreach ($this->bookings($memberType) as $booking) {
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
		$urlPath  = '/uploads/meal_cards/';
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
		$output  = "<a href=\"javascript:void(0)\" class=\"load-remote-menu\"
		data-url=\"./ajax/menu_modal.php?mealUID=" . $this->uid . "\"
		data-bs-toggle=\"modal\"
		data-bs-target=\"#menuModal\">";
		$output .= "<i class=\"bi bi-info-circle\"></i>";
		$output .= "</a>";
		
		return $output;
	}
	
	public function cleanMenu(): string {
		if ($this->menu === null) {
			return '';
		}
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
	
	public function getCapacityForMemberType(?string $memberType = null): array {
		// defensive defaults
		$defaults = [
			'main'    => 0,
			'dessert' => 0,
			'guests'  => 0,
		];
		
		// Determine member type: explicit argument wins, else current user
		if ($memberType === null || trim($memberType) === '') {
			// Adjust this line if $user is accessed differently in your app
			global $user;
			if (!$user || !method_exists($user, 'getMemberType')) {
				return $defaults;
			}
			$memberType = $user->getMemberType();
		}
		
		$lookup = strtolower(trim((string)$memberType));
		if ($lookup === '') {
			return $defaults;
		}
	
		// Obtain capacity as array (handle either string JSON or already-decoded array)
		if (is_string($this->capacity)) {
			$capacityArr = json_decode($this->capacity, true);
			if ($capacityArr === null && json_last_error() !== JSON_ERROR_NONE) {
				// malformed JSON -> return defaults
				return $defaults;
			}
		} elseif (is_array($this->capacity)) {
			$capacityArr = $this->capacity;
		} else {
			return $defaults;
		}
	
		if (!is_array($capacityArr) || empty($capacityArr)) {
			return $defaults;
		}
	
		// Recursive lower-case keys helper
		$lowercaseKeysRecursive = function ($arr) use (&$lowercaseKeysRecursive) {
			if (!is_array($arr)) return $arr;
			$res = [];
			foreach ($arr as $k => $v) {
				$res[strtolower((string)$k)] = $lowercaseKeysRecursive($v);
			}
			return $res;
		};
	
		$capacityArr = $lowercaseKeysRecursive($capacityArr);
	
		// Try exact lookup, then try singular fallback if key ends with 's'
		$member = $capacityArr[$lookup] ?? null;
		if (!is_array($member) && substr($lookup, -1) === 's') {
			$alt = rtrim($lookup, 's');
			$member = $capacityArr[$alt] ?? $member;
		}
	
		// If still not an array, return defaults
		if (!is_array($member)) {
			return $defaults;
		}
	
		// Safely extract values with defaults
		$main = (int)($member['seating']['main'] ?? 0);
		$dessert = (int)($member['seating']['dessert'] ?? 0);
		$guests = (int)($member['guests']['max_per_member'] ?? 0);
	
		return [
			'main'    => $main,
			'dessert' => $dessert,
			'guests'  => $guests,
		];
	}
	
	public function card() {
		global $user, $settings;
		
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
			
			// Progress bar
			$output .= $this->progressBar();
			
			$output .= "</div>"; // end card-body
			
			$output .= "<div class=\"card-footer bg-transparent border-0 pt-0\">";
				$output .= $this->bookingButton();
			$output .= "</div>"; // end footer
			
		$output .= "</div>"; // end card
		$output .= "</div>"; // end column
		
		return $output;
	}
	
	public function progressBar(?string $memberType = null): string {
		global $user;
	
		// Resolve the effective member type
		$userMemberType = $user?->getMemberType();
		$effectiveType  = $memberType ?: $userMemberType;
	
		// Determine number booked for this member type
		$booked = (int) $this->totalDiners($effectiveType);
	
		// Determine capacity for this member type
		$capacity = (int) ($this->getCapacityForMemberType($effectiveType)['main'] ?? 0);
	
		// 🔑 Only hide if capacity is 0 AND this is NOT the user's own member type
		if ($capacity <= 0 && $effectiveType !== $userMemberType) {
			return '';
		}
	
		// Percentage (safe even when capacity = 0)
		$percentage = $capacity > 0
			? (int) round(($booked / $capacity) * 100)
			: 0;
	
		$percentage = max(0, min(100, $percentage));
	
		// Choose colour class
		if ($percentage >= 100) {
			$barClass = 'bg-danger';
		} elseif ($percentage >= 80) {
			$barClass = 'bg-warning';
		} else {
			$barClass = 'bg-info';
		}
	
		// Title / label
		$memberLabel = htmlspecialchars(ucfirst((string) $effectiveType));
		if ($this->allowed_dessert == 1 && !$this->hasDessertCapacity(false)) {
			$titleHtml = '<span class="text-danger">Dessert Capacity Reached</span>';
		} else {
			$titleHtml = '<span>' . $memberLabel . '</span>';
		}
	
		// Build output
		$output  = '<div class="d-flex align-items-center justify-content-between">';
		$output .= $titleHtml;
		$output .= '<span class="booking-count" data-capacity="' . $capacity . '">';
		$output .= htmlspecialchars("{$booked} of {$capacity}");
		$output .= '</span>';
		$output .= '</div>';
	
		$output .= '<div class="progress mb-3" style="height: 6px;" role="progressbar" ';
		$output .= 'aria-valuenow="' . $booked . '" ';
		$output .= 'aria-valuemin="0" ';
		$output .= 'aria-valuemax="' . max(0, $capacity) . '" ';
		$output .= 'aria-label="' . $memberLabel . ' capacity">';
	
		$output .= '<div class="progress-bar ' . $barClass . '" ';
		$output .= 'style="width: ' . $percentage . '%"></div>';
	
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
		
		$capacity = $this->getCapacityForMemberType()['main'];
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
	
		return $this->totalDiners() < $capacity;
	}
	
	public function hasDessertCapacity(bool $factorInAdmin = true): bool {
		global $user;
		
		$capacity = $this->getCapacityForMemberType()['dessert'];
		
		if (!$this->allowed_dessert) {
			return false;
		}
		
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
		
		return $this->totalDessertDiners() < $capacity;
	}
	
	public function hasGuestDessertCapacity(int $guests, bool $factorInAdmin = true): bool {
		global $user;
		
		$capacity = $this->getCapacityForMemberType()['dessert'];
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
	
		if (!$this->allowed_dessert) {
			return false;
		}
		
		$totalIfAdded = $this->totalDessertDiners() + $guests + 1;
		
		// true only if adding would NOT exceed capacity
		return $totalIfAdded <= $capacity;
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
		
		$capacity = $this->getCapacityForMemberType()['main'];
		$guest_capacity = $this->getCapacityForMemberType()['guests'];
	
		if ($factorInAdmin && $user->hasPermission("bookings")) {
			return true;
		}
		
		if ($this->totalDiners() < $capacity) {
			if ($existingGuests < $guest_capacity) {
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
	
	public function dinersList(): string {
		global $user;
	
		$showBookingDetails = (bool) $user->hasPermission('bookings');
		$showMembers = (bool) $user->hasPermission('members');
		$allowedWine = ($this->allowed_wine === "1");
	
		$output = '<ul>';
	
		foreach ($this->bookings() as $booking) {
			$member = Member::fromLDAP($booking->member_ldap);
			$memberName = $member->public_displayName();
	
			$output .= '<li>';
			$output .= $memberName . ' ';
	
			if ($showBookingDetails) {
				$output .= $this->renderWineDessertIcons($booking->wineChoice(), $booking->dessertChoice());
			}
	
			$guests = $booking->guests();
			if (!empty($guests)) {
				$output .= '<ul>';
				$output .= $this->renderGuestList($guests, $member, $showMembers, $showBookingDetails, $allowedWine, $booking);
				$output .= '</ul>';
			}
	
			$output .= '</li>';
		}
	
		$output .= '</ul>';
	
		return $output;
	}
	
	/**
	 * Render the <li> items for guests and return the HTML string.
	 */
	private function renderGuestList(array $guests, $member, bool $showMembers, bool $showBookingDetails, bool $allowedWine, $booking): string {
		$html = '';
	
		foreach ($guests as $guest) {
			// guest name might be missing: use empty string as fallback then escape
			$guestName = htmlspecialchars((string) ($guest['guest_name'] ?? ''));
	
			// Original behaviour: hide guest name when user lacks members perm AND member hasn't opted in
			if (!$showMembers && ($member->opt_in != 1)) {
				$guestName = 'Hidden';
			}
	
			$html .= '<li>';
			$html .= $guestName . ' ';
	
			if ($showBookingDetails) {
				$html .= $this->renderWineDessertIcons(
					$guest['guest_wine_choice'] ?? null,
					$booking->dessertChoice(),
					$allowedWine
				);
			}
	
			$html .= '</li>';
		}
	
		return $html;
	}
	
	private function renderWineDessertIcons($wineChoice, $dessertChoice, $mealAllowedWine = true) {
		$icons = [];
	
		if ($mealAllowedWine && $wineChoice && $wineChoice !== "None") {
			$icons[] = '<svg class="bi" width="1em" height="1em" aria-hidden="true"><use xlink:href="assets/images/icons.svg#wine-glass"></use></svg>';
		}
	
		if ($dessertChoice == "1") {
			$icons[] = '<i class="bi bi-cookie icon-size"></i>';
		}
	
		return implode(' ', $icons);
	}
}
