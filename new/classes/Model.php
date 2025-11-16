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
	public function insert(array $data, bool $log = true) {
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
	
	/**
	 * Retrieve recent log entries.
	 *
	 * @param int $limit Number of entries to return.
	 * @return array
	 */
	public function getRecent(int $limit = 50): array {
		$sql = "SELECT * FROM " . static::$table . "
				ORDER BY date DESC
				LIMIT :limit";
	
		// Because PDO doesn’t allow named parameters for LIMIT with emulated prepares off,
		// we’ll use a positional placeholder instead.
		$sql = str_replace(':limit', '?', $sql);
	
		return $this->db->fetchAll($sql, [$limit]);
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
}

class Terms extends Model {
	protected static string $table = 'terms';
	
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
	
	/*
	UNUSED?
	public function nextTerm(): ?array {
		global $db;
		
		$stmt = $db->prepare("
			SELECT *
			FROM " . self::$table . "
			WHERE date_start > :end
			ORDER BY date_start ASC
			LIMIT 1
		");
		
		$stmt->execute([':end' => $this->date_end]);
		return $stmt->fetch() ?: null;
	}*/
	
	function firstDayOfWeek(?string $inputDate = null): string {
		$date = new DateTime($inputDate ?? 'now');
		// Subtract days until we reach Sunday (0 = Sunday)
		$dayOfWeek = (int)$date->format('w'); // 0 (Sun) to 6 (Sat)
		if ($dayOfWeek !== 0) {
			$date->modify("-{$dayOfWeek} days");
		}
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
		$start = new DateTime('monday this week');
		$end   = new DateTime('sunday this week 23:59:59');
	
		return ($given >= $start && $given <= $end);
	}
}
