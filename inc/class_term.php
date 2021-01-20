<?php
class term {
  protected static $table_name = "terms";

  public $uid;
  public $name;
  public $date_start;
  public $date_end;

  function __construct($termUID = null) {
    global $db;

    if ($termUID == null) {
      $currentTermClass = $this->currentTerm();
      $termUID = $currentTermClass['uid'];
    }

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $termUID . "'";

		$term = $db->query($sql)->fetchArray();

		foreach ($term AS $key => $value) {
			$this->$key = $value;
		}
  }



  public function currentTerm() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE CURDATE() > date_start AND CURDATE() < date_end ";
    $sql .= " LIMIT 1";

    $currentTerm = $db->query($sql)->fetchAll();

    if (count($currentTerm) == 1) {
      $currentTerm = $currentTerm[0];
    } else {
      $currentTerm = array("uid"=>"0", "name"=>"Vacation", "date_start"=>"Unknown", "date_end"=>"Unknown");
    }

    return $currentTerm;
  }

  public function nextTerm() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date_start) > '" . $this->date_end . "'";
    $sql .= " ORDER BY date_start ASC";
    $sql .= " LIMIT 1";

    $nextTerm = $db->query($sql)->fetchAll();

    return $nextTerm;
  }

  public function currentWeek() {
    $dateFrom = $this->date_start;
    $dateTo   = date('Y-m-d');

    return datediff('ww', $dateFrom, $dateTo, false) + 1;
  }

  public function whichWeek($date = null) {
    $dateFrom = $this->date_start;
    $dateTo   = $date;

    return datediff('ww', $dateFrom, $dateTo, false) + 1;
  }

  public function weekStartDate($weekNum = null) {
    if ($weekNum == null) {
      $weekNum = $this->currentWeek();
    }

    $weekDiff = $weekNum -1;

    $weekStartDate = date('Y-m-d', strtotime("+$weekDiff week", strtotime($this->date_start)));

    return $weekStartDate;
  }

  public function weeksInTerm() {
    // factor in 0th week
    $dateFrom   = date('Y-m-d', strtotime("-7 days", strtotime($this->date_start)));
    $dateTo   = date('Y-m-d', strtotime($this->date_end));

    return datediff('ww', $dateFrom, $dateTo, false);
  }

  public function isCurrentTerm() {
    $currentTerm = $this->currentTerm();
    if ($this->uid == $currentTerm['uid']) {
      return true;
    } else {
      return false;
    }
  }

  public function create($array = null) {
	   global $db;
     global $logsClass;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'memberUID') {
        $sqlColumns[] = $updateItem;
        $sqlValues[] = "'" . $value . "' ";
      }
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);
    $logsClass->create("admin", "Term [uid:" . $create->lastInsertID() . "] created");

    return $create;
  }

  public function update($array = null) {
    global $db;
    global $logsClass;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'termUID') {
        $sqlUpdate[] = $updateItem ." = '" . $value . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);
    $logsClass->create("admin", "Term [termUID:" . $this->uid . "] updated");

    return $update;
  }

  public function delete() {
    global $db;
    global $logsClass;

    $termUID = $this->uid;
    $termName = $this->uid;

    $sql  = "DELETE FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $termUID . "' ";
    $sql .= " LIMIT 1";

    $deleteMeal = $db->query($sql);
    $logsClass->create("admin", "[termUID:" . $termUID . "] named '" . $termName . "' deleted");

    return $deleteMeal;
  }
}
?>
