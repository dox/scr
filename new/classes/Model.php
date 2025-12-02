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
			':username'		=> (isset($user) && $user?->getUsername()) ?? null,
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
	
	public function getName($uid) {
		$query = "SELECT name FROM " . static::$table . " WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);
		
		if ($row) return $row['name'];
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
	
	public function wineBottlesTotal() {
		global $db;
		
		$sql = "SELECT SUM(wine_total) AS total_bottles_in_cellar
		FROM (
			SELECT cellar_uid, wine_uid, GREATEST(0, SUM(bottles)) AS wine_total
			FROM wine_transactions
			GROUP BY cellar_uid, wine_uid
		) AS wine_sums";
		
		$result = $db->query($sql)->fetch();
		
		return $result['total_bottles_in_cellar'];
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
	
		$sql .= " ORDER BY wine_bins.name ASC";
	
		$rows = $db->query($sql)->fetchAll();
	
		$wines = [];
		foreach ($rows as $row) {
			$wines[] = new Wine($row['uid']);
		}
	
		return $wines;
	}
	
	public function bins(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT *
				FROM " . self::$table_bins . " ";
	
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
	
		$sql .= " ORDER BY wine_bins.name ASC";
	
		$rows = $db->query($sql)->fetchAll();
	
		$bins = [];
		foreach ($rows as $row) {
			$bins[] = new Bin($row['uid']);
		}
	
		return $bins;
	}
	
	public function transactions(array $whereFilterArray = []) : array {
		global $db;
	
		$sql = "SELECT *
				FROM " . self::$table_transactions . " ";
	
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
	
		$sql .= " ORDER BY date ASC";
	
		$rows = $db->query($sql)->fetchAll();
	
		$transactions = [];
		foreach ($rows as $row) {
			$transactions[] = new Transaction($row['uid']);
		}
	
		return $transactions;
	}

	
	
	
	
	
	
	
	
	
	
	public function allWinesSearch($whereFilterArray = null) {
		global $db;
	
		$sql  = "SELECT wine_wines.*, wine_bins.cellar_uid FROM " . self::$table_wines;
		$sql .= " LEFT JOIN wine_bins ON wine_wines.bin_uid = wine_bins.uid";
	
		$conditions = [];
		
		// Process the array of where conditions
		if (!empty($whereFilterArray)) {
			foreach ($whereFilterArray as $condition) {
				if (
					is_array($condition) &&
					isset($condition['field'], $condition['operator'], $condition['value'])
				) {
					// Safely escape the field name
					$escapedField = addslashes($condition['field']);
					$operator = strtoupper(trim($condition['operator']));
					
					// Handle the 'IN' operator specially
					if ($operator === 'IN' && is_array($condition['value'])) {
						$escapedValues = array_map('addslashes', $condition['value']);
						$inClause = "'" . implode("','", $escapedValues) . "'";
						$conditions[] = "$escapedField IN ($inClause)";
					} else {
						// Safely escape the value
						$escapedValue = addslashes($condition['value']);
	
						// Ensure the operator is valid
						$allowedOperators = ['=', 'LIKE', '>', '<', '>=', '<=', '<>', '!='];
						if (in_array($operator, $allowedOperators, true)) {
							$conditions[] = "$escapedField $operator '$escapedValue'";
						}
					}
				}
			}
		}
	
		// Append the conditions to the SQL query
		if (!empty($conditions)) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY name ASC";
		
		echo $sql;
		
		// Execute the query and fetch results
		$wines = $db->query($sql)->fetchAll();
	
		return $wines;
	}
	
	
	
	
	public function allLists($whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT * FROM " . self::$table_lists;
		
		$conditions = [];
		
		// Process the array of where conditions
		if (!empty($whereFilterArray)) {
			foreach ($whereFilterArray as $condition) {
				if (
					is_array($condition) &&
					isset($condition['field'], $condition['operator'], $condition['value'])
				) {
					// Safely escape the field name
					$escapedField = addslashes($condition['field']);
					$operator = strtoupper(trim($condition['operator']));
					
					// Handle the 'IN' operator specially
					if ($operator === 'IN' && is_array($condition['value'])) {
						$escapedValues = array_map('addslashes', $condition['value']);
						$inClause = "'" . implode("','", $escapedValues) . "'";
						$conditions[] = "$escapedField IN ($inClause)";
					} else {
						// Safely escape the value
						$escapedValue = addslashes($condition['value']);
	
						// Ensure the operator is valid
						$allowedOperators = ['=', 'LIKE', '>', '<', '>=', '<=', '<>', '!='];
						if (in_array($operator, $allowedOperators, true)) {
							$conditions[] = "$escapedField $operator '$escapedValue'";
						}
					}
				}
			}
		}
	
		// Append the conditions to the SQL query
		if (!empty($conditions)) {
			$sql .= " WHERE " . implode(' AND ', $conditions);
		}
	
		$sql .= " ORDER BY last_updated DESC, name ASC";
		
		// Execute the query and fetch results
		$lists = $db->query($sql)->fetchAll();
	
		return $lists;
	}
	
	public function winesByUIDs($wine_uids_array) {
		global $db;
		
		if (!empty($wine_uids_array)){ 
		  $sql  = "SELECT * FROM wine_wines";
		  $sql .= " WHERE uid IN (" . $wine_uids_array . ")";
		  $sql .= " ORDER BY name ASC";
		  
		  $results = $db->query($sql)->fetchAll();
		} else {
		  return array();
		}
	  
		return $results;
	}
	
	public function weightedSearch($searchTerm, $cellarUID = null, $closed = false) {
		global $db;
		
		$sql  = "SELECT wine_wines.*, wine_bins.cellar_uid, CASE 
			WHEN wine_wines.name LIKE \"%" . $searchTerm . "%\" THEN 20
			WHEN wine_bins.name LIKE \"%" . $searchTerm . "%\" THEN 15
			WHEN wine_wines.code LIKE \"%" . $searchTerm . "%\" THEN 10
			WHEN wine_wines.grape LIKE \"%" . $searchTerm . "%\" THEN 5
			WHEN wine_wines.region_of_origin LIKE \"%" . $searchTerm . "%\" THEN 1
			ELSE 0
		END AS weight ";
		//$sql .= "FROM `wine_wines` ";
		$sql .= " FROM wine_wines LEFT JOIN wine_bins ON wine_wines.bin_uid = wine_bins.uid ";
		$sql .= "WHERE (";
			$sql .= "wine_wines.name LIKE \"%" . $searchTerm . "%\" ";
			$sql .= "OR wine_bins.name LIKE \"%" . $searchTerm . "%\" ";
			$sql .= "OR wine_wines.code LIKE \"%" . $searchTerm . "%\" ";
			$sql .= "OR wine_wines.grape LIKE \"%" . $searchTerm . "%\" ";
			$sql .= "OR wine_wines.region_of_origin LIKE \"%" . $searchTerm . "%\"";
		$sql .= ") ";
		
		if (isset($cellarUID)) {
			$sql .= "AND wine_bins.cellar_uid = '" . $cellarUID . "' ";
		}
		
		if ($closed == true) {
		} else {
			$sql .= "AND wine_wines.status != 'Closed' ";
		}
		
		$sql .= "ORDER BY weight DESC ";
		$sql .= "LIMIT 20";
		
		//echo $sql;
		
		$results = $db->query($sql)->fetchAll();
		
		return $results;
	}
	
	
	
	public function listFromWines($columnName, $whereFilterArray = null) {
		global $db;
		
		$sql  = "SELECT DISTINCT " . $columnName . " FROM " . self::$table_wines;
		if ($columnName != "status") {
			$sql .= " WHERE status != 'Closed'";
		}
		if (!empty($whereFilterArray)) {
			$conditions = [];
			foreach ($whereFilterArray as $key => $value) {
				// Escaping the key and value for safety
				$escapedKey = addslashes($key);
				$escapedValue = addslashes($value);
				$conditions[] = "$escapedKey = '$escapedValue'";
			}
			$sql .= " AND " . implode(' AND ', $conditions);
		}
		
		$sql .= " ORDER BY " . $columnName . " ASC";
		
		$results = $db->query($sql)->fetchAll();
		
		return $results;
	}
	
	public function transactionsTypes() {
		$array['Transaction'] = "deduct";
		$array['Import'] = "import";
		$array['Stock Adjustment (Deduction)'] = "deduct";
		$array['Stock Adjustment (Addition)'] = "import";
		$array['Wastage'] = "deduct";
		
		return $array;
	}
}
