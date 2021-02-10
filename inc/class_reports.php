<?php
class reports {
  protected static $table_name = "reports";

  public $uid;
  public $type;
  public $name;
  public $description;
  public $columns;
  public $query;

  public function all() {
    global $db;


    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY name DESC";

    $reports = $db->query($sql)->fetchAll();

    return $reports;
  }

  public function one($uid = null) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $uid . "'";
    $sql .= " LIMIT 1";

    $report = $db->query($sql)->fetchAll()[0];

    return $report;
  }

  public function update($array = null) {
    global $db, $logsClass, $settingsClass;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'reportUID') {
        $sqlUpdate[] = $updateItem ." = '" . escape($value) . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $array['reportUID'] . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);
    echo $settingsClass->alert("success", "Success!", "Report successfully updated");

    $logArray['category'] = "admin";
    $logArray['result'] = "success";
    $logArray['description'] = "[reportUID:" . $array['reportUID'] . "] created";
    $logsClass->create($logArray);

    return $update;
  }

  public function create($type = "unknown", $description = null) {
    global $db;

    $description = escape($description);

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (ip, type, username, description) ";
    $sql .= " VALUES ('" . ip2long($_SERVER['REMOTE_ADDR']) . "', '" . $type . "', '" . $_SESSION['username'] . "', '" . $description . "')";

    $report = $db->query($sql);
    echo $settingsClass->alert("success", "Success!", "Report successfully created");

    $logArray['category'] = "admin";
    $logArray['result'] = "success";
    $logArray['description'] = "[reportUID:" . $report->lastInsertID() . "] created";
    $logsClass->create($logArray);

  }
}
?>
