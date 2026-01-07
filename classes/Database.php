<?php
class Database {
	private static $instance = null;
	private $pdo;

	private function __construct() {
		try {
			$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
			$this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
			]);
		} catch (PDOException $e) {
			die("Database connection failed: " . $e->getMessage());
		}
	}

	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function query($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt;
	}

	public function fetch($sql, $params = []) {
		$stmt = $this->query($sql, $params);
		return $stmt->fetch();
	}

	public function fetchAll($sql, $params = []) {
		$stmt = $this->query($sql, $params);
		return $stmt->fetchAll();
	}
	
	public function fetchColumn(string $sql, array $params = []): array {
		$stmt = $this->query($sql, $params);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}

	public function beginTransaction() {
		$this->pdo->beginTransaction();
	}

	public function commit() {
		$this->pdo->commit();
	}

	public function rollBack() {
		$this->pdo->rollBack();
	}

	/**
	 * Generic update method.
	 *
	 * @param string $table Table name
	 * @param array $fields Associative array of fields to update ['column' => 'value']
	 * @param array $where Associative array for WHERE clause ['uid' => 123]
	 * @param string|null $logTable Optional table name to log changes
	 * @return int Number of affected rows
	 */
	public function update(string $table, array $fields, array $where, bool $logChanges = false): int {
		if (empty($fields) || empty($where)) {
			throw new InvalidArgumentException("Fields and WHERE conditions cannot be empty.");
		}
	
		// Fetch existing record
		$whereSql = implode(" AND ", array_map(fn($k) => "$k = :where_$k", array_keys($where)));
		$existing = $this->fetch("SELECT * FROM `$table` WHERE $whereSql", array_combine(
			array_map(fn($k) => ":where_$k", array_keys($where)),
			array_values($where)
		));
	
		if (!$existing) {
			throw new RuntimeException("Record not found for update.");
		}
	
		// Build SET clause
		$setSql = implode(", ", array_map(fn($k) => "$k = :$k", array_keys($fields)));
		$params = $fields;
		foreach ($where as $k => $v) {
			$params["where_$k"] = $v;
		}
	
		// Execute update
		$stmt = $this->query("UPDATE `$table` SET $setSql WHERE $whereSql", $params);
		$affected = $stmt->rowCount();
	
		// Log changes via Log class
		if ($logChanges) {
			$logger = new Log();
			foreach ($fields as $k => $v) {
				$old = $existing[$k] ?? null;
				if ($old != $v) {
					$description = sprintf(
						"Updated %s.%s for UID %s: '%s' → '%s'",
						$table,
						$k,
						$where['uid'] ?? json_encode($where),
						$old,
						$v
					);
					$logger->add($description, Log::INFO);
				}
			}
		}
	
		return $affected;
	}
	
	public function delete(string $table, array $where, bool $logChanges = false): int {
		if (empty($where)) {
			throw new InvalidArgumentException("WHERE conditions cannot be empty for delete.");
		}
	
		// Build WHERE clause
		$whereSql = implode(" AND ", array_map(fn($k) => "$k = :where_$k", array_keys($where)));
	
		// Fetch existing record before removal
		$existing = $this->fetch(
			"SELECT * FROM `$table` WHERE $whereSql",
			array_combine(
				array_map(fn($k) => ":where_$k", array_keys($where)),
				array_values($where)
			)
		);
	
		// Execute delete
		$stmt = $this->query("DELETE FROM `$table` WHERE $whereSql", array_combine(
			array_map(fn($k) => ":where_$k", array_keys($where)),
			array_values($where)
		));
	
		$affected = $stmt->rowCount();
	
		// Optional logging
		if ($logChanges && $affected) {
			$logger = new Log();
			$description = sprintf(
				"Deleted record from %s for %s",
				$table,
				json_encode($where)
			);
			$logger->add($description, Log::WARNING);
		}
	
		return $affected;
	}

}
