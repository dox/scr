<?php
class notifications {
  protected static $table_name = "notifications";

  public $uid;
  public $type;
  public $dismissible;
  public $name;
  public $message;
  public $members_array;
  public $date_start;
  public $date_end;

  public function all() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " ORDER BY date_start ASC";

    $notifications = $db->query($sql)->fetchAll();

    return $notifications;
  }

  public function one($uid = null) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $uid . "'";
    $sql .= " LIMIT 1";

    $notification = $db->query($sql)->fetchAll()[0];

    return $notification;
  }

  public function allCurrentForMember($member = null) {
    global $db;

    if ($member == null) {
      $member = $_SESSION['username'];
    }

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE CURDATE() BETWEEN DATE(date_start) AND DATE(date_end)";
    $sql .= " AND JSON_EXTRACT(members_array, '$.\"" . $member . "\"') IS null";
    $sql .= " ORDER BY date_start DESC";

    $notifications = $db->query($sql)->fetchAll();

    return $notifications;
  }

  public function display($array = null) {
    if ($array['type'] == "Primary") {
      $class = "alert-primary";
    } elseif ($array['type'] == "Secondary") {
      $class = "alert-secondary";
    } elseif ($array['type'] == "Success") {
      $class = "alert-success";
    } elseif ($array['type'] == "Danger") {
      $class = "alert-danger";
    } elseif ($array['type'] == "Warning") {
      $class = "alert-warning";
    } elseif ($array['type'] == "Infomation") {
      $class = "alert-information";
    } elseif ($array['type'] == "Light") {
      $class = "alert-light";
    } elseif ($array['type'] == "Dark") {
      $class = "alert-dark";
    } else {
      $class = "alert-primary";
    }

    if ($array['dismissible'] == 1) {
      $dismissClass = "alert-dismissible";
      $dismissButton = "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>";
    } else {
      $dismissClass = "";
      $dismissButton = "";
    }

    $notificationIcon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#chat-dots\"/></svg>";

    $output  = "<div class=\"alert notificationAlert " . $class . " " . $dismissClass . " fade show\" id=\"" . $array['uid'] . "\" role=\"alert\">";
    $output .= $notificationIcon . " " . $array['message'];
    $output .= $dismissButton;
    $output .= "</div>";

    return $output;
  }

  public function create($array = null) {
    global $db, $logsClass, $settingsClass;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'notificationADD') {
        $sqlColumns[] = $updateItem;
        $sqlValues[] = "'" . $value . "' ";
      }
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);
    echo $settingsClass->alert("success", "Success!", "Notification successfully creted");

    $logArray['category'] = "notification";
    $logArray['result'] = "success";
    $logArray['description'] = "[notificationUID:" . $create->lastInsertID() . "] created";
    $logsClass->create($logArray);

    return $create;
  }

  public function markAsRead($notificationUID = null) {
    global $db;

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET members_array = JSON_SET(COALESCE(members_array, '{}'), '$.\"" . $_SESSION['username'] . "\"', '" . date('Y-m-d H:i:s') . "')";
	  $sql .= " WHERE uid = '" . $notificationUID . "' LIMIT 1";

    $update = $db->query($sql);

    return $update;
  }

  public function update($array = null) {
    global $db, $logsClass, $settingsClass;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'notificationUID') {
        $sqlUpdate[] = $updateItem ." = '" . $value . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $array['notificationUID'] . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);
    echo $settingsClass->alert("success", "Success!", "Notification successfully updated");

    $logArray['category'] = "notification";
    $logArray['result'] = "success";
    $logArray['description'] = "[notificationUID:" . $array['notificationUID'] . "] updated";
    $logsClass->create($logArray);

    return $update;
  }

  public function delete($uid = null) {
    global $db, $logsClass, $settingsClass;

    $sql  = "DELETE FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $uid . "' ";
    $sql .= " LIMIT 1";

    $deleteNotification = $db->query($sql);
    echo $settingsClass->alert("success", "Success!", "Notification successfully deleted");

    $logArray['category'] = "notification";
    $logArray['result'] = "success";
    $logArray['description'] = "[notificationUID:" . $uid . "] deleted";
    $logsClass->create($logArray);

    return $deleteNotification;
  }

  public function deleteDismiss($notificationUID = null, $memberLDAP = null) {
    global $db, $logsClass;

    $memberObject = new member($memberLDAP);

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET members_array = JSON_REMOVE(members_array, '$." . $memberObject->ldap . "')";
    $sql .= " WHERE uid = '" . $notificationUID . "' ";
    $sql .= " LIMIT 1";

    $delete = $db->query($sql);

    $logArray['category'] = "notification";
    $logArray['result'] = "success";
    $logArray['description'] = "Notifcation dismissal deleted from [notificationUID:" .  $notificationUID  . "] for [memberUID:" . $memberObject->uid . "]";
    $logsClass->create($logArray);

    return $delete;
  }

}
?>
