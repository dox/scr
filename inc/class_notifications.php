<?php
class notifications {
  protected static $table_name = "notifications";

  public $uid;
  public $type;
  public $dismissible;
  public $name;
  public $message;
  public $members_array;

  public function all() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    //$sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    $sql .= " ORDER BY date DESC";

    $notifications = $db->query($sql)->fetchAll();

    return $notifications;
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
?>
