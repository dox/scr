<?php
class logs {
  protected static $table_name = "logs";

  public $uid;
  public $ip;
  public $username;
  public $date;
  public $result;
  public $category;
  public $description;
  
  public $logsPerPage = 500;

  public function categoryColour($category = null, $alpha = "0.3") {
    $unknownColour = "rgba(0, 0, 0, 0.4)";
    //$randomColour = "rgba(" . rand(0,255) . ", " . rand(0,255) . ", " . rand(0,255) . ", 0.2)";

    $coloursArray = array(
      "admin" => "rgba(255, 235, 0, " . $alpha . ")",
      "booking" => "rgba(255, 99, 132, " . $alpha . ")",
      "ldap" => "rgba(118, 42, 145, " . $alpha . ")",
      "logon" => "rgba(54, 162, 3, " . $alpha . ")",
      "meal" => "rgba(254, 77, 17, " . $alpha . ")",
      "member" => "rgba(54, 162, 235, " . $alpha . ")",
      "notification" => "rgba(29, 143, 177, " . $alpha . ")",
      "report" => "rgba(252, 40, 37, " . $alpha . ")",
      "view" => "rgba(60, 162, 3, " . $alpha . ")"
    );

    if (isset($coloursArray[$category])) {
      return $coloursArray[$category];
    } else {
      return $unknownColour;
    }
  }

  public function all() {
    global $db, $settingsClass;
  
    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_retention') . ' days'));
  
    $sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, result, category, description  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    $sql .= " ORDER BY date DESC";
  
    $logs = $db->query($sql)->fetchAll();
  
    return $logs;
  }
  
  public function paginatedResults($offset = 0, $search = null) {
    global $db, $settingsClass;
  
    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_retention') . ' days'));
  
    $sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, result, category, description  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    
    if ($search != null) {
      $sql .= " AND description LIKE '%" . $search . "%' ";
    }
    
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT " . $offset . ", " . $this->logsPerPage;
  
    $logs = $db->query($sql)->fetchAll();
  
    return $logs;
  }
  
  public function byDay($category = null) {
    global $db, $settingsClass;
    
    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_display') . ' days'));
    
    $sql  = "SELECT DATE(date) AS date, count(*) AS total FROM " . self::$table_name;
    $sql .= " WHERE date >= '" . $maximumLogsAge . "'";
    
    if ($category != null) {
      $sql .= " AND category = '" . $category . "'";
    }
    $sql .= " GROUP BY DATE(date)";
    
    $logs = $db->query($sql)->fetchAll();
    
    $logsArray = array();
    foreach ($logs AS $log) {
      $logsArray[$log['date']] = $log['total']; 
    }
    
    return $logsArray;
  }

  public function create($array = null) {
    global $db;

    $array['description'] = str_replace("'" , "\'", $array['description']);

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (ip, username, category, result, description) ";
    $sql .= " VALUES (";
    $sql .= "'" . ip2long($_SERVER['REMOTE_ADDR']) . "', ";
    $sql .= "'" . $_SESSION['username'] . "', ";
    $sql .= "'" . $array['category'] . "', ";
    $sql .= "'" . $array['result'] . "', ";
    $sql .= "'" . $array['description'] . "'";
    $sql .= ")";

    $logs = $db->query($sql);
  }

  public function categories() {
    global $db;
    global $settingsClass;

    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_display') . ' days'));

    $sql  = "SELECT category FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    $sql .= " GROUP BY category";

    $categories = $db->query($sql)->fetchAll();

    return $categories;
  }

  public function purge() {
		global $db, $settingsClass;

    $logs_retention = $settingsClass->value('logs_retention');

    //fetch COUNT of how many logs we need to delete
		$sql = "SELECT COUNT(*) AS totalLogs FROM " . self::$table_name . " WHERE DATE(date) < '" . date('Y-m-d', strtotime('-' . $logs_retention . ' days')) . "'";
		$logsToDelete = $db->query($sql)->fetchAll()[0]['totalLogs'];

    // if there are logs to delete, delete them!
		if ($logsToDelete > 0) {
			$sql = "DELETE FROM " . self::$table_name . " WHERE DATE(date) < '" . date('Y-m-d', strtotime('-' . $logs_retention . ' days')) . "'";
      $db->query($sql);

      //log that we did this
      $logArray['category'] = "admin";
      $logArray['result'] = "success";
      $logArray['description'] = $logsToDelete . autoPluralise(" log", " logs", $logsToDelete) . " purged";
      $this->create($logArray);
		}
	}

  private function displayRow($array = null) {
    $string = $array['description'];
    $patternArray['/\[mealUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=admin_meal&mealUID=$1\" class=\"text-decoration-none\">[mealUID:$1]</a></code>";
    $patternArray['/\[memberUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=member&memberUID=$1\" class=\"text-decoration-none\">[memberUID:$1]</a></code>";
    //$patternArray['/\[bookingUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=booking&bookingUID=$1\" class=\"text-decoration-none\">[bookingUID:$1]</a></code>";
    $patternArray['/\[notificationUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=admin_notification&notificationUID=$1\" class=\"text-decoration-none\">[notificationUID:$1]</a></code>";
    $patternArray['/\[settingUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=admin_settings&settingUID=$1\" class=\"text-decoration-none\">[settingUID:$1]</a></code>";
    $patternArray['/\[reportUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=admin_report&reportUID=$1\" class=\"text-decoration-none\">[reportUID:$1]</a></code>";

    foreach ($patternArray AS $pattern => $replace) {
      //echo $pattern . $replace;
      $array['description'] = preg_replace($pattern, $replace, $array['description']);
    }

    //$preg_string = preg_replace($pattern, $replace, $string);

    $output  = "<tr class=\"table-" . $array['result'] . " filterRow\">";
    $output .= "<td>" . date('Y-m-d H:i:s', strtotime($array['date'])) . "</td>";
    $output .= "<td>" . $array['ip'] . "</td>";
    $output .= "<td>" . $array['username'] . "</td>";
    $output .= "<td style=\"word-wrap: break-word;max-width: 500px;\" class=\"filterDescription\">" . $array['description'] . $this->displayCategoryBadge($array['category']) . "</td>";
    //$output .= "<td class=\"text-truncate\">Description</td>";
    $output .= "</tr>";

    return $output;
  }

  public function displayTable($offsetPage = 0, $search = null) {
    $offsetPage = $offsetPage * $this->logsPerPage;
    $output  = "<table id=\"logsTable\" class=\"table\">";
    $output .= "<tr class=\"header\">";
    $output .= "<th>" . "Date" . "</th>";
    $output .= "<th>" . "IP" . "</th>";
    $output .= "<th>" . "Username" . "</th>";
    $output .= "<th>" . "Description" . "</th>";
    $output .= "</tr>";

    $output .= "<tbody>";
    
    foreach ($this->paginatedResults($offsetPage, $search) AS $log) {
      $output .= $this->displayRow($log);
    }

    $output .= "</tbody>";
    $output .= "</table>";

    return $output;
  }

  private function displayCategoryBadge($category = null) {
    if (in_array($category, array("admin", "ldap", "meal", "report"))) {
      $class = "bg-primary";
    } elseif (in_array($category, array("member", "view"))) {
      $class = "bg-warning";
    } elseif (in_array($category, array("booking", "notification"))) {
      $class = "bg-info";
    } elseif (in_array($category, array("logon"))) {
      $class = "bg-success";
    } else {
      $class = "bg-dark";
    }

    $output = "<span class=\"badge rounded-pill " . $class . " float-end\">" . $category . "</span>";

    return $output;
  }
  
  public function paginationDisplay($totalLogs = 0, $currentPage = 0) {
    $totalLogPages = number_format($totalLogs/$this->logsPerPage,0);
    
    $output  = "<nav aria-label=\"Page pagination\">";
    $output .= "<ul class=\"pagination\">";
    $output .= $this->paginationPreviousButton($totalLogs, $currentPage);
    
    $i = 0;
    do {
      $active = "";
      if ($i == $_GET['p']) {
        $active = "active";
      }
      $output .= "<li class=\"page-item " . $active . "\"><a class=\"page-link\" href=\"index.php?n=admin_logs&p=" . $i . "\">" . $i . "</a></li>";
      $i++;
    } while($i <= $totalLogPages);
    
    $output .= $this->paginationNextButton($totalLogs, $currentPage);
    $output .= "</ul>";
    $output .= "</nav>";
    
    return $output;
  }
  
  private function paginationPreviousButton($totalLogs = 0, $currentPage = 0) {
    $disabled = "";
    if ($currentPage <= 0) {
      $previousPageNumber = 0;
      $disabled = " disabled";
    } else {
      $previousPageNumber = $currentPage - 1;
    }
    $output = "<li class=\"page-item " . $disabled . "\"><a class=\"page-link\" href=\"index.php?n=admin_logs&p=" . $previousPageNumber . "\">Previous</a></li>";
    
    return $output;
  }
  
  private function paginationNextButton($totalLogs = 0, $currentPage = 0) {
    $totalLogPages = number_format($totalLogs/$this->logsPerPage,0);
    
    $disabled = "";
    if ($currentPage >= $totalLogPages) {
      $nextPageNumber = 0;
      $disabled = " disabled";
    } else {
      $nextPageNumber = $currentPage + 1;
    }
    $output = "<li class=\"page-item " . $disabled . "\"><a class=\"page-link\" href=\"index.php?n=admin_logs&p=" . $nextPageNumber . "\">Next</a></li>";
    
    return $output;
  }
}
$logsClass = new logs();
?>
