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
		
		if ($row) return $row;
	}
	
	public function getAll() {
		$query = "SELECT * FROM " . static::$table;
		$rows = $this->db->fetchAll($query);
		return $rows;
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
	public const INFO    = 'INFO';
	public const WARNING = 'WARNING';
	public const ERROR   = 'ERROR';
	public const DEBUG   = 'DEBUG';
	  
	
	public function add(string $description, string $category = self::INFO): bool {
		global $user;
		
		$type = strtoupper($category);
	
		$sql = "INSERT INTO " . static::$table . " (username, ip, description, category, result, date)
				VALUES (:username, :ip, :description, :category, :result, NOW())";
	
		$params = [
			':username'		=> $user->getUsername(),
			':ip'			=> ip2long($this->detectIp()),
			':description'	=> $description,
			':category'		=> $type, // use the uppercase version
			':result'		=> "result",
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
		return $_SERVER['REMOTE_ADDR'] 
			?? $_SERVER['HTTP_CLIENT_IP'] 
			?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
			?? 'UNKNOWN';
	}
}

class Settings extends Model {
	protected static string $table = 'settings';
	
	public function get($name) {
		$query = "SELECT * FROM " . static::$table . " WHERE name = ?";
		$row = $this->db->fetch($query, [$name]);
		
		if ($row) return $row['value'];
	}
	
	public function getUID($name) {
		$query = "SELECT uid FROM " . static::$table . " WHERE name = ?";
		$row = $this->db->fetch($query, [$name]);
		
		if ($row) return $row['uid'];
	}
	
	public function update(array $postData) {
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
			'logs'
		);
		
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
}

class Meals extends Model {
	protected static string $table = 'meals';
	
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
			$start = $start->format('Y-m-d');
		}
		if ($end instanceof DateTime) {
			$end = $end->format('Y-m-d');
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
}

class Bookings extends Model {
	protected static string $table = 'bookings';
	
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
	
	public function currentTerm(): object {
		global $db;
		
		$sql = "
			SELECT uid
			FROM " . self::$table . "
			WHERE CURDATE() BETWEEN date_start AND date_end
			   OR date_end = (
					SELECT MAX(date_end)
					FROM " . self::$table . "
					WHERE date_end < CURDATE()
			   )
			ORDER BY date_end DESC
			LIMIT 1";
		
		$row = $db->query($sql)->fetch();
		
		if ($row) {
			return new Term($row['uid']);
		}
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
	
		return ($given >= $start && $given <= $end);
	}
}
