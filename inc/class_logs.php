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

  public function all() {
    global $db, $settingsClass;
  
    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_retention') . ' days'));
  
    $sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, result, category, description  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    $sql .= " ORDER BY date DESC";
  
    $logs = $db->query($sql)->fetchAll();
  
    return $logs;
  }
  
  public function allWhereMatch($string = null) {
     global $db;
     global $settingsClass;
  
     $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_retention') . ' days'));
  
     $sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, result, category, description  FROM " . self::$table_name;
     $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
     $sql .= " AND description LIKE '%" . $string . "%' ";
     $sql .= " ORDER BY date DESC";
  
     $logs = $db->query($sql)->fetchAll();
  
     return $logs;
   }
  
  public function paginatedResults($offset = 0, $search = null) {
    global $db, $settingsClass;
    
    $offset = $offset * $this->logsPerPage;
  
    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_retention') . ' days'));
  
    $sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, result, category, description  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    
    if ($search != null) {
      $sql .= " AND (";
      $sql.= "description LIKE '%" . $search . "%' ";
      $sql .= "OR username LIKE '%" . $search . "%'";
      
      if (filter_var($search, FILTER_VALIDATE_IP)) {
        $sql .= "OR ip LIKE '%" . ip2long($search) . "%'";
      }
      
      $sql .= ") ";
    }
    
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT " . $offset . ", " . $this->logsPerPage;
  
    $logs = $db->query($sql)->fetchAll();
    
    return $logs;
  }
  
  public function paginatedResultsTotal($search = null) {
    global $db, $settingsClass;
  
    $maximumLogsAge = date('Y-m-d', strtotime('-' . $settingsClass->value('logs_retention') . ' days'));
  
    $sql  = "SELECT count(*) AS total FROM " . self::$table_name;
    $sql .= " WHERE DATE(date) > '" . $maximumLogsAge . "' ";
    
    if ($search != null) {
      $sql .= " AND (";
      $sql.= "description LIKE '%" . $search . "%' ";
      $sql .= "OR username LIKE '%" . $search . "%'";
      
      if (filter_var($search, FILTER_VALIDATE_IP)) {
        $sql .= "OR ip LIKE '%" . ip2long($search) . "%'";
      }
      
      $sql .= ") ";
    }
    
    $sql .= " ORDER BY date DESC";
    
    $logs = $db->query($sql)->fetchAll();
    $logsCount = $logs[0]['total'];
    
    return $logsCount;
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
    
    // log if the person doing this is impersonating someone else
    if ($_SESSION['impersonating'] == true) {
      $array['description'] = $array['description'] . " (impersonated by " . $_SESSION['username_original'] . ")";
    }

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
    $output .= "<th scope=\"row\">" . date('Y-m-d H:i:s', strtotime($array['date'])) . "</th>";
    $output .= "<td>" . $array['ip'] . "</td>";
    $output .= "<td>" . $array['username'] . "</td>";
    $output .= "<td style=\"word-wrap: break-word;max-width: 500px;\" class=\"filterDescription\">" . $array['description'] . $this->displayCategoryBadge($array['category']) . "</td>";
    //$output .= "<td class=\"text-truncate\">Description</td>";
    $output .= "</tr>";

    return $output;
  }

  public function displayTable($logs = null) {
    $output  = "<table id=\"logsTable\" class=\"table table-sm\">";
    $output .= "<thead>";
    $output .= "<tr>";
    $output .= "<th scope=\"col\">" . "Date" . "</th>";
    $output .= "<th scope=\"col\">" . "IP" . "</th>";
    $output .= "<th scope=\"col\">" . "Username" . "</th>";
    $output .= "<th scope=\"col\">" . "Description" . "</th>";
    $output .= "</tr>";
    $output .= "</thead>";

    $output .= "<tbody>";
    
    foreach ($logs AS $log) {
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
  
  private function calculate_percentile($percent, $numbers) {
      sort($numbers);
      $count = count($numbers);
      
      $index = ($percent / 100) * ($count - 1);
      $fractional_part = $index - floor($index);
      
      $lower_index = (int) $index;
      $lower_value = $numbers[$lower_index];
      
      if ($lower_index + 1 < $count) {
          $upper_value = $numbers[$lower_index + 1];
      } else {
          $upper_value = $lower_value;
      }
      
      $result = $lower_value + ($upper_value - $lower_value) * $fractional_part;
      return $result;
  }


  public function paginatedBar() {
    // % of keys to remove 1 - 99, ideally about 30
    $percentage = 30;
    
    // Get the current page from the query string, default to 1
    $currentPage = isset($_GET['p']) ? intval($_GET['p']) : 1;
    
    // calculate how many pages of logs exist
    $totallogspages = count($this->all()) / $this->logsPerPage;
    
    // Generate array with keys from 1 to $totallogspages
    $array = range(1, $totallogspages);
    
    if (count($array) > $percentage) {
      // Calculate the range of keys to be removed
      $startKey = $this->calculate_percentile(30, $array);
      $endKey = $this->calculate_percentile(70, $array);
      
      // Remove the middle $percentage of keys from the array
      for ($i = $startKey; $i <= $endKey; $i++) {
          if (abs($i - $currentPage) > 2) {
              unset($array[$i]);
          }
      }
    }
    
    return $array;
  }
  
  public function paginatedLogs() {
      // Get current page from query string or set default to 1
      $page = isset($_GET['p']) ? $_GET['p'] : 1;
      
      // Calculate total number of pages
      $totalPages = ceil(count($this->all()) / $this->logsPerPage);
      
      // Make sure the page is within bounds
      $page = max(1, min($totalPages, intval($page)));
      
      // Calculate start and end indexes for the current page
      $startIndex = ($page - 1) * $this->logsPerPage;
      $endIndex = min($startIndex + $this->logsPerPage - 1, count($this->all()) - 1);
      
      // Extract items for the current page
      $currentPageItems = array_slice($this->all(), $startIndex, $endIndex - $startIndex + 1);
      
      return [
          'currentPageItems' => $currentPageItems,
          'currentPage' => $page,
          'totalPages' => $totalPages
      ];
  }
  
  public function paginationResults($totalLogs = 0) {
    $currentPage = isset($_GET['p']) ? $_GET['p'] : 1; // Get current page from query string, default to 1
    
    $totalLogPages = number_format($totalLogs/$this->logsPerPage,0) - 1;
    
    $output  = "<nav aria-label=\"Page pagination\">";
    $output .= "<ul class=\"pagination\">";
    $output .= $this->paginationPreviousButton();
    
    $i = 1;
    foreach ($this->paginatedBar() AS $barNum) {
      $active = "";
      if ($barNum == $_GET['p']) {
        $active = "active";
      }
      
      if ($barNum != $i) {
        $output .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\">...</a></li>";
        $i = $barNum;
      } else {
        $output .= "<li class=\"page-item " . $active . "\"><a class=\"page-link\" href=\"index.php?n=admin_logs&p=" . $barNum . "\">" . $barNum . "</a></li>";
      }
      $i++;
      
    }
    
    $output .= $this->paginationNextButton();
    $output .= "</ul>";
    $output .= "</nav>";
    
    return $output;
  }
  
  private function paginationPreviousButton() {
    $currentPage = isset($_GET['p']) ? $_GET['p'] : 1; // Get current page from query string, default to 1
    $totalLogPages = floor(count($this->all()) / $this->logsPerPage);
    
    $disabled = "";
    
    if ($currentPage <= 1) {
      $previousPageNumber = 1;
      $disabled = " disabled";
    } else {
      $previousPageNumber = $currentPage - 1;
    }
    $output = "<li class=\"page-item " . $disabled . "\"><a class=\"page-link\" href=\"index.php?n=admin_logs&p=" . $previousPageNumber . "\">Previous</a></li>";
    
    return $output;
  }
  
  private function paginationNextButton() {
    $currentPage = isset($_GET['p']) ? $_GET['p'] : 1; // Get current page from query string, default to 1
    $totalLogPages = floor(count($this->all()) / $this->logsPerPage);
    
    $disabled = "";
    
    if ($currentPage >= $totalLogPages) {
      $nextPageNumber = $currentPage;
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
