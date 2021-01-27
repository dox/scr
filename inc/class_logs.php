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

  public function purge() {
		global $db;
    global $settingsClass;

    $logs_retention = $settingsClass->value('logs_retention');

		$sql = "SELECT * FROM " . self::$table_name . " WHERE type = 'purge' AND DATE(date) = '" . date('Y-m-d') . "' LIMIT 1";
		$lastPurge = $db->query($sql)->fetchAll();

		if (empty($lastPurge)) {
			$sql = "SELECT * FROM " . self::$table_name . " WHERE DATE(date) < '" . date('Y-m-d', strtotime('-' . $logs_retention . ' days')) . "'";
			$logsToDelete = $db->query($sql)->fetchAll();

			if (count($logsToDelete) > 0) {
				$sql = "DELETE FROM " . self::$table_name . " WHERE DATE(date) < '" . date('Y-m-d', strtotime('-' . $logs_retention . ' days')) . "'";
				$logsToDelete = $db->query($sql);

        $this->create("purge", count($logsToDelete) . " log(s) purged");
			}
		}
	}

  public function list_group_item($log = null) {
    //$string = 'Some text [mealUID:123] here';
    $string = $log['description'];
    $patternArray['/\[mealUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=admin_meal&mealUID=$1\" class=\"text-decoration-none\">[mealUID:$1]</a></code>";
    $patternArray['/\[memberUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=member&memberUID=$1\" class=\"text-decoration-none\">[memberUID:$1]</a></code>";
    //$patternArray['/\[bookingUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=booking&bookingUID=$1\" class=\"text-decoration-none\">[bookingUID:$1]</a></code>";
    $patternArray['/\[notificationUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=admin_notification&notificationUID=$1\" class=\"text-decoration-none\">[notificationUID:$1]</a></code>";

    foreach ($patternArray AS $pattern => $replace) {
      //echo $pattern . $replace;
      $log['description'] = preg_replace($pattern, $replace, $log['description']);
    }

    $preg_string = preg_replace($pattern, $replace, $string);

    $output  = "<div class=\"list-group-item list-group-item-action filterRow\">";
    $output .= "<div class=\"d-flex w-100 justify-content-between\">";
    $output .= "<h5 class=\"mb-1 filterDescription\">" . $log['username'] . " - " . $log['description'] . "</h5>";
    $output .= "<small class=\"text-muted\">" . dateDisplay($log['date']) . " " . date('H:i:s', strtotime($log['date'])) . "</small>";
    //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
    $output .= "</div>";
    //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
    $output .= "<small class=\"text-muted\"><span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span> " . $log['ip'] . "</small>";
    $output .= "</div>";

    return $output;
  }
}

$logsClass = new logs();
?>
