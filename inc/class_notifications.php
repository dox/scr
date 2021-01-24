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
    $sql .= " ORDER BY date DESC";

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
    $sql .= " WHERE JSON_EXTRACT(members_array, \"$." . $member . "\") IS null";
    $sql .= " ORDER BY date ASC";

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
    //$output .= "<div class=\"\">";
    $output .= $notificationIcon . " " . $array['message'];
    $output .= $dismissButton;
    $output .= "</div>";

    return $output;
  }

  public function create($type = "unknown", $description = null) {
    global $db;

    $description = escape($description);

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (ip, type, username, description) ";
    $sql .= " VALUES ('" . ip2long($_SERVER['REMOTE_ADDR']) . "', '" . $type . "', '" . $_SESSION['username'] . "', '" . $description . "')";

    $logs = $db->query($sql);

  }

  public function markAsRead($notificationUID = null) {
    global $db;

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET members_array = JSON_SET(COALESCE(members_array, '{}'), '$." . $_SESSION['username'] . "', '" . date('Y-m-d H:i:s') . "')";
	  $sql .= " WHERE uid = '" . $notificationUID . "' LIMIT 1";

    $update = $db->query($sql);

    return $update;
  }

  public function updateJSONTEMP($array = null) {
    global $db;

    $sql  = "UPDATE " . self::$table_name;
	  $sql .= " SET members_array = JSON_SET(COALESCE(members_array, '{}'), '$." . $array['member_ldap'] . "', '" . $array['status'] . "')";
	  $sql .= " WHERE uid = '" . $array['notificationUID'] . "' LIMIT 1";

    echo $sql;

    $update = $db->query($sql);

    return $update;
  }

}
?>
