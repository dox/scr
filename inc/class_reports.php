<?php
class reports {
  protected static $table_name = "reports";

  public $uid;
  public $type;
  public $admin_only;
  public $name;
  public $file;
  public $description;
  public $date_lastrun;

  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY name ASC";

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

  private function displayRow($array = null) {
    $dropdownButton  = "<div class=\"dropdown float-end\">";
    $dropdownButton .= "<button class=\"btn btn-sm btn-primary dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton1\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">";
    $dropdownButton .= "Dropdown button";
    $dropdownButton .= "</button>";
    $dropdownButton .= "<ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton1\">";
    $dropdownButton .= "<li><a class=\"dropdown-item\" href=\"report.php?reportUID=" . $array['uid'] . "\">Run Now</a></li>";
    $dropdownButton .= "<li><a class=\"dropdown-item\" href=\"index.php?n=admin_report&reportUID=" . $array['uid'] . "\">Edit</a></li>";
    $dropdownButton .= "</ul>";
    $dropdownButton .= "</div>";

    $output  = "<tr>";
    //$output .= "<td>" . dateDisplay($array['date']) . " " . timeDisplay($array['date']) . "</td>";
    $output .= "<td><a href=\"index.php?n=admin_report&reportUID=" . $array['uid'] . "\">" . $array['name'] . "</a></td>";
    $output .= "<td>" . $array['type'] . "</td>";
    $output .= "<td>" . $this->accessBadge($array['admin_only']) . "</td>";
    $output .= "<td>" . $array['date_lastrun'] . "</td>";
    $output .= "<td>" . $dropdownButton . "</td>";
    $output .= "</tr>";

    return $output;
  }

  public function displayTable() {
    $output  = "<table id=\"myTable\" class=\"table\">";
    $output .= "<thead>";
    $output .= "<th>" . "Name" . "</th>";
    $output .= "<th>" . "Type" . "</th>";
    $output .= "<th>" . "Access" . "</th>";
    $output .= "<th>" . "Last Run" . "</th>";
    $output .= "<th>" . "Action" . "</th>";
    $output .= "</thead>";

    $output .= "<tbody>";

    foreach ($this->all() AS $report) {
      $output .= $this->displayRow($report);
    }

    $output .= "</tbody>";
    $output .= "</table>";

    return $output;
  }

  public function accessBadge($access = null) {
    if ($access == 1) {
      $badgeClass = "bg-danger";
      $badgeTitle = "ADMIN";
    } elseif ($access == 0) {
      $badgeClass = "bg-info";
      $badgeTitle = "USER";
    } else {
      $badgeClass = "bg-secondary";
      $badgeTitle = "UNKNOWN";
    }

    $badge = "<span class=\"badge " . $badgeClass . "\" >" . $badgeTitle . "</span>";

    return $badge;
  }

  public function update_lastrun($reportUID = null) {
    global $db;

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET date_lastrun = '" . date('Y-m-d H:i:s') . "'";
    $sql .= "WHERE uid = '" . $reportUID . "'";

    $update = $db->query($sql);

    return true;

  }
}
?>
