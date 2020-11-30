<?php
class logs {
  protected static $table_name = "logs";

  public $uid;
  public $ip;
  public $username;
  public $date;
  public $type;
  public $description;

  public function all() {
    global $db;
    global $settingsClass;

    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_retention') . ' days'));

    $sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, type, description  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    $sql .= " ORDER BY date DESC";

    $logs = $db->query($sql)->fetchAll();

    return $logs;
  }

  public function allByTypeByDay($type = null, $date = null) {
    global $db;

    $sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, type, description  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) = '" . $date . "'";
    $sql .= " AND type = '" . $type . "'";
    $sql .= " ORDER BY date DESC";

    $logs = $db->query($sql)->fetchAll();

    return $logs;
  }

  public function create($type = "unknown", $description = null) {
    global $db;

    $description = escape($description);

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (ip, type, username, description) ";
    $sql .= " VALUES ('" . ip2long($_SERVER['REMOTE_ADDR']) . "', '" . $type . "', '" . $_SESSION['username'] . "', '" . $description . "')";

    $logs = $db->query($sql);

  }

  public function types() {
    global $db;
    global $settingsClass;

    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_retention') . ' days'));

    $sql  = "SELECT type  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    $sql .= " GROUP BY type";

    $types = $db->query($sql)->fetchAll();

    return $types;
  }
}

$logsClass = new logs();
?>
