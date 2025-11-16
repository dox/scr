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
}
