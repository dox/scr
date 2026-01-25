<?php
abstract class Model {
	protected $db;
	protected static string $table;

	public function __construct() {
		$this->db = Database::getInstance();
	}

	public function getOne($id) {
		$query = "SELECT * FROM " . static::$table . " WHERE id = ?";
		$row = $this->db->fetch($query, [$id]);
		
		if ($row) {
			return $row;
		}
	}
	
	/**
	 * Generic INSERT for all subclasses
	 *
	 * @param array $data  Associative array of column => value
	 * @return int|false   Inserted row ID or false on failure
	 */
	public function create(array $data, bool $log = true) {
		if (empty($data)) {
			throw new InvalidArgumentException("Insert data cannot be empty.");
		}
	
		// JSON-encode arrays automatically
		$params = [];
		foreach ($data as $col => $val) {
			if (is_array($val)) {
				$val = json_encode($val); // <-- encode arrays
			}
			$params[":$col"] = $val;
		}
	
		$columns = array_keys($data);
		$placeholders = array_map(fn($c) => ':' . $c, $columns);
	
		$sql = sprintf(
			"INSERT INTO %s (%s) VALUES (%s)",
			static::$table,
			implode(', ', $columns),
			implode(', ', $placeholders)
		);
	
		$stmt = $this->db->query($sql, $params);
		$insertId = $stmt ? $this->db->lastInsertId() : false;
	
		// Optional logging
		if ($log && $insertId !== false && static::$table !== 'new_logs') {
			$this->logInsert($insertId, $data);
		}
	
		return $insertId;
	}
	
	private function logInsert(int $id, array $data): void {
		// We reference Log dynamically to avoid recursion
		$log = new Log();
	
		$summary = sprintf(
			'Inserted into %s (ID %d): %s',
			static::$table,
			$id,
			json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
		);
	
		$log->add($summary, Log::INFO);
	}
}

class Log extends Model {
	protected static string $table = 'logs';
	
	// Define standard log levels
	public const SUCCESS = 'SUCCESS';
	public const INFO    = 'INFO';
	public const WARNING = 'WARNING';
	public const ERROR   = 'ERROR';
	public const DEBUG   = 'DEBUG';
	
	public function add(
		string $description,
		string $category = null,
		?string $result = self::INFO
	): bool {
		global $user;
	
		$sql = "INSERT INTO " . static::$table . " 
				(username, ip, description, category, result, date)
				VALUES (:username, :ip, :description, :category, :result, NOW())";
		
		// Log impersonations
		$original_username = ($_SESSION['impersonation_backup']['samaccountname'] ?? 'Unknown');
		if (isset($_SESSION['impersonating'])) {
			$description = $description . " [Impersonated By: " . $original_username . "]";
		}
		
		$params = [
			':username'    => $user?->getUsername() ?? null,
			':ip'          => ip2long($this->detectIp()),
			':description' => $description,
			':category'    => strtoupper($category),
			':result'      => strtoupper($result),
		];
	
		$stmt = $this->db->query($sql, $params);
		return $stmt !== false;
	}
	
	public function getRecent(int $age = 30): array {
		$sql = "SELECT *
				FROM " . static::$table . "
				WHERE date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
				ORDER BY date DESC";
	
		return $this->db->fetchAll($sql, [$age]);
	}
	
	/**
	 * Detect client IP address.
	 *
	 * @return string
	 */
	private function detectIp(): string {
		// Running from CLI
		if (PHP_SAPI === 'cli') {
			return '127.0.0.1';
		}
	
		// Running via web request
		return $_SERVER['REMOTE_ADDR'] 
			?? $_SERVER['HTTP_CLIENT_IP'] 
			?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
			?? 'UNKNOWN';
	}
	
	public function linkify(string $text): string {
		// Map UID types to URL patterns
		$routes = [
			'booking_uid' => 'index.php?page=booking&uid=%d',
			'meal_uid'    => 'index.php?page=meal&uid=%d',
			'member_uid'  => 'index.php?page=member&ldap=%d',
		];
	
		return preg_replace_callback(
			'/\[(\w+_uid):(\d+)\]/',
			function ($matches) use ($routes) {
				[$full, $type, $id] = $matches;
	
				// If we don’t recognise the UID type, leave it untouched
				if (!isset($routes[$type])) {
					return $full;
				}
	
				$url = sprintf($routes[$type], $id);
	
				return sprintf(
					'<a href="%s">[%s:%s]</a>',
					htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
					htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
					htmlspecialchars($id, ENT_QUOTES, 'UTF-8')
				);
			},
			$text
		);
	}
}

class Settings extends Model {
	protected static string $table = 'settings';
	
	public function getAll() {
		$query = "SELECT * FROM " . static::$table . " ORDER BY name ASC";
		$rows = $this->db->fetchAll($query);
		return $rows;
	}
	
	public function get($name) {
		$query = "SELECT * FROM " . static::$table . " WHERE name = ?";
		$row = $this->db->fetch($query, [$name]);
		
		if ($row) {
			return $row['value'];
		}
	}
	
	public function getUID($name) {
		$query = "SELECT uid FROM " . static::$table . " WHERE name = ?";
		$row = $this->db->fetch($query, [$name]);
		
		if ($row) {
			return $row['uid'];
		}
	}
	
	public function getName($uid) {
		$query = "SELECT name FROM " . static::$table . " WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);
		
		if ($row) {
			return $row['name'];
		}
	}
	
	public function update(array $postData, $log = true) {
		global $db;
	
		// Map normal text/select fields
		$fields = [
			'value'       => $postData['value'] ?? null
		];
		
		// Send to database update
		$updatedRows = $db->update(
			static::$table,
			$fields,
			['uid' => $postData['uid']],
			$log
		);
		
		toast('Setting Updated', 'Setting sucesfully updated', 'text-success');
		return $updatedRows;
	}
}

class Members extends Model {
	protected static string $table = 'members';
	
	public static function getAllByType(string $type, bool $enabled = true): array {
		$db = Database::getInstance(); // or $this->db if not static
	
		$sql  = "SELECT uid FROM " . static::$table . " WHERE type = ?";
		if ($enabled) {
			$sql .= " AND enabled = 1";
		}
		$sql .= " ORDER BY precedence ASC, lastname DESC";
		
		$results = $db->fetchAll($sql, [$type]);
		
		$members = array();
		foreach ($results as $result) {
			$members[] = Member::fromUID($result['uid']);
		}
	
		return $members;
	}
	
	public function all(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT *
				FROM " . self::$table . " ";
	
		$conditions = [];
	
		foreach ($whereFilterArray as $key => $rule) {
			$key = addslashes($key);
	
			// Allow [operator, value] style input
			if (is_array($rule)) {
				[$operator, $value] = $rule;
	
				if (strtoupper($operator) === 'IN' && is_array($value)) {
					$value = array_map(fn($v) => "'" . addslashes($v) . "'", $value);
					$conditions[] = "$key IN (" . implode(',', $value) . ")";
				} else {
					$value = addslashes($value);
					$conditions[] = "$key $operator '$value'";
				}
	
			} else {
				// Fallback to simple equals
				$value = addslashes($rule);
				$conditions[] = "$key = '$value'";
			}
		}
	
		if ($conditions) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY enabled DESC, precedence ASC, lastname ASC, firstname ASC";
	
		$rows = $db->query($sql)->fetchAll();
	
		$members = [];
		foreach ($rows as $row) {
			$members[] = Member::fromUID($row['uid']);
		}
	
		return $members;
	}
}

class Meals extends Model {
	protected static string $table = 'meals';
	
	public function all(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT uid
				FROM " . self::$table;
	
		$conditions = [];
	
		foreach ($whereFilterArray as $key => $rule) {
			$key = addslashes($key);
	
			// Allow [operator, value] style input
			if (is_array($rule)) {
				[$operator, $value] = $rule;
	
				if (strtoupper($operator) === 'IN' && is_array($value)) {
					$value = array_map(fn($v) => "'" . addslashes($v) . "'", $value);
					$conditions[] = "$key IN (" . implode(',', $value) . ")";
				} elseif ($operator === 'LIKE') {
					$value = '%' . addslashes($value) . '%';
					$conditions[] = "$key LIKE '$value'";
				} else {
					$value = addslashes($value);
					$conditions[] = "$key $operator '$value'";
				}
	
			} else {
				// Fallback to simple equals
				$value = addslashes($rule);
				$conditions[] = "$key = '$value'";
			}
		}
	
		if ($conditions) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY date_meal ASC";
	
		$rows = $db->query($sql)->fetchAll();
		
		return array_map(fn($row) => new Meal($row['uid']), $rows);
	}
	
	public static function locations(): array {
		global $db;
		
		$locations = array();
			
		$sql = "SELECT DISTINCT location
		FROM " . static::$table . "
		WHERE date_meal > DATE_SUB(NOW(), INTERVAL 2 YEAR)
		ORDER BY location ASC";
		
		$rows = $db->fetchAll($sql);
		
		foreach ($rows as $row) {
			if (!empty($row['location'])) {
				$locations[] = $row['location'];
			}
		}
		
		return $locations;
	}
	
	public static function betweenDates($start, $end): array {
		global $db;
	
		// Normalize to YYYY-MM-DD
		if ($start instanceof DateTime) {
			$start = $start->format('Y-m-d 00:00:00');
		} else {
			$start = date('Y-m-d 00:00:00', strtotime($start));
		}
		
		if ($end instanceof DateTime) {
			$end = $end->format('Y-m-d 23:59:59');
		} else {
			$end = date('Y-m-d 23:59:59', strtotime($end));
		}
	
		$sql = "SELECT uid
				FROM " . static::$table . "
				WHERE date_meal >= ? AND date_meal <= ?
				ORDER BY date_meal ASC";
	
		$rows = $db->fetchAll($sql, [$start, $end]);
	
		return array_map(fn($row) => new Meal($row['uid']), $rows);
	}
	
	public function cardImages(): array {
		$mealCardDirectory = "./uploads/meal_cards/";
		$defaultImage = "./assets/images/card_default.png"; // path to your default image
	
		// Scan directory and remove . and ..
		$files = array_diff(scandir($mealCardDirectory, SCANDIR_SORT_DESCENDING), ['.', '..']);
	
		// Prepend directory path to each file
		$fullPaths = array_map(fn($file) => $mealCardDirectory . $file, $files);
	
		// Include the default image at the beginning
		array_unshift($fullPaths, $defaultImage);
	
		return $fullPaths;
	}
	
	public function oldestMealDate(): DateTimeImmutable {
		global $db;
	
		$sql = "
			SELECT MIN(date_meal) AS date_meal
			FROM " . static::$table;
	
		$row = $db->fetch($sql);
	
		return empty($row['date_meal'])
			? new DateTimeImmutable('@0')   // the dawn
			: new DateTimeImmutable($row['date_meal']);
	}
}

class Bookings extends Model {
	protected static string $table = 'bookings';
	
	public function add(array $fields) {
		global $log;
	
		// Send to database create
		$newBooking = $this->create($fields, false);
	
		if (!$newBooking) {
			return false;
		}
	
		$member = Member::fromLDAP($fields['member_ldap'] ?? null);
	
		$memberName = $member ? $member->name() : 'Unknown member';
	
		$log->add(
			'[booking_uid:' . $newBooking .
			'] created for ' . $memberName .
			'. [meal_uid:' . ($fields['meal_uid'] ?? 'unknown') . ']',
			'Booking',
			Log::SUCCESS
		);
	
		return $newBooking;
	}
	
}

class Terms extends Model {
	protected static string $table = 'terms';
	
	public function all() {
		global $db;
		
		$sql = "SELECT uid
				FROM " . static::$table . "
				ORDER BY date_start DESC";
		
		$rows = $db->fetchAll($sql);
		
		$terms = array();
		foreach ($rows as $row) {
			$terms[] = new Term($row['uid']);
		}
		
		return $terms;
	}
	
	public function previousTerm(): Term {
		global $db;
	
		$sql = "
			SELECT uid
			FROM " . self::$table . "
			WHERE date_end < CURDATE()
			ORDER BY date_end DESC
			LIMIT 1
		";
	
		$row = $db->query($sql)->fetch();
	
		if ($row) {
			return new Term($row['uid']);
		}
	}
	
	public function currentTerm(): Term {
		global $db;
	
		$sql = "
			SELECT uid
			FROM " . self::$table . "
			WHERE CURDATE() BETWEEN date_start AND date_end
			ORDER BY date_end DESC
			LIMIT 1
		";
	
		$row = $db->query($sql)->fetch();
	
		if ($row) {
			return new Term($row['uid']);
		}
	
		// A quiet stand-in for empty days
		$previousTerm = $this->previousTerm();
		$nextTerm = $this->nextTerm();
		
		$term = new Term(null);
		$term->uid = 0;
		$term->name = 'Vacation';
		$term->date_start = $previousTerm->date_end;
		$term->date_end = $nextTerm->date_start;
	
		return $term;
	}
	
	public function nextTerm(): Term {
		global $db;
	
		$sql = "
			SELECT uid
			FROM " . self::$table . "
			WHERE date_start > CURDATE()
			ORDER BY date_start ASC
			LIMIT 1
		";
	
		$row = $db->query($sql)->fetch();
		
		if ($row) {
			return new Term($row['uid']);
		}
	}
	
	public static function getTermForDate(string|DateTime $date): ?Term {
		// Ensure $date is a DateTime object
		if (!$date instanceof DateTime) {
			$date = new DateTime($date);
		}
	
		$db = Database::getInstance();
		$sql = "SELECT * FROM " . self::$table . " WHERE ? BETWEEN date_start AND date_end LIMIT 1";
		$row = $db->fetch($sql, [$date->format('Y-m-d')]);
	
		if ($row) {
			return new Term($row['uid']);
		}
	
		return null; // No term found
	}
	
	public function firstDayOfWeek(?string $inputDate = null): string {
		$date = new DateTime($inputDate ?? 'now');
		// Subtract days until we reach Sunday (0 = Sunday)
		$dayOfWeek = (int)$date->format('w'); // 0 (Sun) to 6 (Sat)
		if ($dayOfWeek !== 0) {
			$date->modify("-{$dayOfWeek} days");
		}
		return $date->format('Y-m-d');
	}
	
	public function lastDayOfWeek(?string $inputDate = null): string {
		$date = new DateTime($inputDate ?? 'now');
	
		// Subtract days to reach Sunday (0 = Sunday)
		$dayOfWeek = (int)$date->format('w'); // 0 (Sun) to 6 (Sat)
		if ($dayOfWeek !== 0) {
			$date->modify("-{$dayOfWeek} days");
		}
	
		// Add 6 days to get Saturday
		$date->modify('+6 days');
	
		return $date->format('Y-m-d');
	}
	
	public function navbarWeeks(): array {
		global $settings;
	
		$before = (int) $settings->get('meal_weeks_navbar_before');
		$after  = (int) $settings->get('meal_weeks_navbar_after');
	
		// Get the Sunday of the current week
		$currentWeek = new DateTime($this->firstDayOfWeek());
		$weeks = [];
	
		// Weeks before
		for ($i = $before; $i > 0; $i--) {
			$weeks[] = (clone $currentWeek)->modify("-{$i} week")->format('Y-m-d');
		}
	
		// Current week (Sunday)
		$weeks[] = $currentWeek->format('Y-m-d');
	
		// Weeks after
		for ($i = 1; $i <= $after; $i++) {
			$weeks[] = (clone $currentWeek)->modify("+{$i} week")->format('Y-m-d');
		}
	
		return $weeks;
	}

	
	public function isCurrentWeek(string $date): bool {
		$given = new DateTime($date);
	
		// Start of the current week (Sunday) based on today
		$start = new DateTime($this->firstDayOfWeek()); 
		$end   = (clone $start)->modify('+6 days 23:59:59'); // Saturday end
	
		return $given >= $start && $given <= $end;
	}
}

class Wines extends Model {
	protected static string $table_cellars = "wine_cellars";
	protected static string $table_bins = "wine_bins";
	protected static string $table_wines = "wine_wines";
	protected static string $table_transactions = "wine_transactions";
	protected static string $table_lists = "wine_lists";
	
	public function cellars($whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT * FROM " . self::$table_cellars;
		
		if (!empty($whereFilterArray)) {
			$conditions = [];
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
		
		$sql .= " ORDER BY name ASC";
		
		$rows = $db->query($sql)->fetchAll();
		
		$cellars = [];
		
		if ($rows) {
			foreach ($rows as $row) {
				$cellars[] = new Cellar($row['uid']);
			}
		}
		
		return $cellars;
	}
	
	public function listFromWines(string $column): array{
		global $db;
	
		// Guard the column name to prevent injection
		$allowed = [
			'supplier', 'category', 'code', 'grape', 'country_of_origin',
			'region_of_origin', 'status', 'bin_uid', 'vintage'
		];
	
		if (!in_array($column, $allowed, true)) {
			throw new InvalidArgumentException("Invalid column: $column");
		}
	
		$sql = "SELECT DISTINCT `$column` FROM " . self::$table_wines . " WHERE 1=1";
		
		$params = [];
		// Default rule: ignore closed, unless querying status itself
		if ($column !== 'status') {
			$sql .= " AND status <> 'Closed' ";
		}
		
		$sql .= " ORDER BY `$column` ASC";
		
		$result = $db->fetchColumn($sql);
		
		return $result;
	}
	
	public function wineBottlesTotal(): int {
		global $db;
	
		$sql = "
			SELECT COALESCE(SUM(t.bottles), 0) AS total
			FROM wine_transactions t
			INNER JOIN wine_wines w ON w.uid = t.wine_uid
			INNER JOIN wine_bins b ON b.uid = w.bin_uid
		";
	
		$result = $db->query($sql)->fetch();
	
		return (int) $result['total'];
	}
	
	public function wines(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT wine_wines.*, wine_bins.name AS bin_name, wine_bins.cellar_uid AS cellar_uid
				FROM " . self::$table_wines . "
				LEFT JOIN wine_bins
				  ON wine_wines.bin_uid = wine_bins.uid";
	
		$conditions = [];
	
		foreach ($whereFilterArray as $key => $rule) {
			$key = addslashes($key);
	
			// Allow [operator, value] style input
			if (is_array($rule)) {
				[$operator, $value] = $rule;
			
				// Handle NULL specially
				if ($value === null || empty($value)) {
					if (strtoupper($operator) === '=') {
						$conditions[] = "$key IS NULL";
					} elseif (strtoupper($operator) === '!=') {
						$conditions[] = "$key IS NOT NULL";
					}
					continue;
				}
			
				// Existing handling
				if (strtoupper($operator) === 'IN') {
					// Ensure $value is an array
					if (!is_array($value)) {
						$value = array_map('trim', explode(',', $value));
					}
					$value = array_map(fn($v) => "'" . addslashes($v) . "'", $value);
					$conditions[] = "$key IN (" . implode(',', $value) . ")";
				} elseif ($operator === 'LIKE') {
					$value = '%' . addslashes($value) . '%';
					$conditions[] = "$key LIKE '$value'";
				} else {
					$value = addslashes($value);
					$conditions[] = "$key $operator '$value'";
				}
			}
		}
	
		if ($conditions) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY wine_bins.name ASC";
		
		$rows = $db->query($sql)->fetchAll();
	
		$wines = [];
		foreach ($rows as $row) {
			$wines[] = new Wine($row['uid']);
		}
	
		return $wines;
	}
	
	public function transactionsGrouped(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT group_head.uid
		FROM (
			SELECT 
				COALESCE(linked, uid) AS group_key,
				MIN(uid) AS uid,
				MAX(date_posted) AS max_date
			FROM wine_transactions ";
	
		$conditions = [];
	
		foreach ($whereFilterArray as $key => $rule) {
			$key = addslashes($key);
		
			// Normalize rule(s) into an array of [operator, value]
			$ruleSet = (isset($rule[0]) && is_array($rule[0])) ? $rule : [$rule];
		
			foreach ($ruleSet as $r) {
				[$operator, $value] = $r;
		
				// Handle NULL specially
				if ($value === null || $value === '') {
					if (strtoupper($operator) === '=') {
						$conditions[] = "$key IS NULL";
					} elseif (strtoupper($operator) === '!=') {
						$conditions[] = "$key IS NOT NULL";
					}
					continue;
				}
		
				if (strtoupper($operator) === 'IN') {
					if (!is_array($value)) {
						$value = array_map('trim', explode(',', $value));
					}
					
					$value = array_map(fn($v) => "'" . addslashes($v) . "'", $value);
					$conditions[] = "$key IN (" . implode(',', $value) . ")";
				} elseif (strtoupper($operator) === 'LIKE') {
					$value = '%' . addslashes($value) . '%';
					$conditions[] = "$key LIKE '$value'";
				} else {
					$value = addslashes($value);
					$conditions[] = "$key $operator '$value'";
				}
			}
		}
	
		if ($conditions) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " GROUP BY group_key
		) AS group_head
		ORDER BY group_head.max_date DESC ";
		
		$rows = $db->query($sql)->fetchAll();
	
		$transactions = [];
		foreach ($rows as $row) {
			$transactions[] = new Transaction($row['uid']);
		}
	
		return $transactions;
	}
	
	public function transactions(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT * FROM " . self::$table_transactions;
	
		$conditions = [];
	
		foreach ($whereFilterArray as $key => $rule) {
			$key = addslashes($key);
	
			// Normalize rule(s) into an array of [operator, value]
			$ruleSet = (isset($rule[0]) && is_array($rule[0])) ? $rule : [$rule];
	
			foreach ($ruleSet as $r) {
				[$operator, $value] = $r;
	
				// Handle NULL specially
				if ($value === null || $value === '') {
					if (strtoupper($operator) === '=') {
						$conditions[] = "$key IS NULL";
					} elseif (strtoupper($operator) === '!=') {
						$conditions[] = "$key IS NOT NULL";
					}
					continue;
				}
	
				if (strtoupper($operator) === 'IN') {
					if (!is_array($value)) {
						$value = array_map('trim', explode(',', $value));
					}
					
					$value = array_map(fn($v) => "'" . addslashes($v) . "'", $value);
					$conditions[] = "$key IN (" . implode(',', $value) . ")";
				} elseif (strtoupper($operator) === 'LIKE') {
					$value = '%' . addslashes($value) . '%';
					$conditions[] = "$key LIKE '$value'";
				} else {
					$value = addslashes($value);
					$conditions[] = "$key $operator '$value'";
				}
			}
		}
	
		if ($conditions) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY date_posted DESC, date DESC";
	
		$rows = $db->query($sql)->fetchAll();
	
		$transactions = [];
		foreach ($rows as $row) {
			$transactions[] = new Transaction($row['uid']);
		}
	
		return $transactions;
	}
	
	public function bins(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT * FROM " . self::$table_bins;
	
		$conditions = [];
	
		foreach ($whereFilterArray as $key => $rule) {
			$key = addslashes($key);
	
			// Normalize rule(s) into an array of [operator, value]
			$ruleSet = (isset($rule[0]) && is_array($rule[0])) ? $rule : [$rule];
	
			foreach ($ruleSet as $r) {
				[$operator, $value] = $r;
	
				// Handle NULL specially
				if ($value === null || $value === '') {
					if (strtoupper($operator) === '=') {
						$conditions[] = "$key IS NULL";
					} elseif (strtoupper($operator) === '!=') {
						$conditions[] = "$key IS NOT NULL";
					}
					continue;
				}
	
				if (strtoupper($operator) === 'IN') {
					if (!is_array($value)) {
						$value = array_map('trim', explode(',', $value));
					}
					
					$value = array_map(fn($v) => "'" . addslashes($v) . "'", $value);
					$conditions[] = "$key IN (" . implode(',', $value) . ")";
				} elseif (strtoupper($operator) === 'LIKE') {
					$value = '%' . addslashes($value) . '%';
					$conditions[] = "$key LIKE '$value'";
				} else {
					$value = addslashes($value);
					$conditions[] = "$key $operator '$value'";
				}
			}
		}
	
		if ($conditions) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY cellar_uid ASC, name ASC";
	
		$rows = $db->query($sql)->fetchAll();
	
		$bins = [];
		foreach ($rows as $row) {
			$bins[] = new Bin($row['uid']);
		}
	
		return $bins;
	}
	
	public function lists(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT *
				FROM " . self::$table_lists . " ";
	
		$conditions = [];
	
		foreach ($whereFilterArray as $key => $rule) {
			$key = addslashes($key);
		
			// Support multiple operator/value rules per field
			$ruleSet = is_array($rule[0] ?? null) ? $rule : [$rule];
		
			foreach ($ruleSet as $r) {
				[$operator, $value] = $r;
		
				// Handle NULL specially
				if ($value === null || $value === '') {
					if (strtoupper($operator) === '=') {
						$conditions[] = "$key IS NULL";
					} elseif (strtoupper($operator) === '!=') {
						$conditions[] = "$key IS NOT NULL";
					}
					continue;
				}
		
				if (strtoupper($operator) === 'IN') {
					if (!is_array($value)) {
						$value = array_map('trim', explode(',', $value));
					}
					
					$value = array_map(fn($v) => "'" . addslashes($v) . "'", $value);
					$conditions[] = "$key IN (" . implode(',', $value) . ")";
				} elseif ($operator === 'LIKE') {
					$value = '%' . addslashes($value) . '%';
					$conditions[] = "$key LIKE '$value'";
				} else {
					$value = addslashes($value);
					$conditions[] = "$key $operator '$value'";
				}
			}
		}
	
		if ($conditions) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY name ASC";
	
		$rows = $db->query($sql)->fetchAll();
	
		$lists = [];
		foreach ($rows as $row) {
			$lists[] = new WineList($row['uid']);
		}
	
		return $lists;
	}
}
