<?php
class settings {
  protected static $table_name = "settings";
  public $uid;
  public $name;
  public $description;
  public $value;

  public function all() {
    global $db;

    $settingsToExclude = array("'scr_information'", "'scr_accessibility'");

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE name NOT IN (" . implode("," , $settingsToExclude) . ")";
    $sql .= " ORDER BY name ASC";

    $settings = $db->query($sql)->fetchAll();

    return $settings;
  }

  public function value($name = null) {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE name = '" . $name . "'";

    $setting = $db->query($sql)->fetchAll();
    $settingValue = $setting[0]['value'];

    return $settingValue;
  }

  public function update($array = null) {
    global $db;
    global $logsClass;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'memberUID') {
        $sqlUpdate[] = $updateItem ." = '" . $value . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $array['uid'] . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);
    $logsClass->create("admin", "Setting [settingUID:" . $array['uid'] . "] updated to '" . $array['value'] . "'");

    return $update;
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
    $logsClass->create("admin", "Setting [settingUID:" . $create->lastInsertID() . "] created with '" . $array['value'] . "'");


    return $create;
  }

  public function templates() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE name LIKE 'template%'";

    $templates = $db->query($sql)->fetchAll();

    return $templates;
  }

  public function alert ($type = null, $title = null, $description = null) {
	  $alertClasses = array("primary", "secondary", "success" , "danger" , "warning", "info");
	  if (in_array($type, $alertClasses)) {
		  $class = "alert-" . $type;
	  } else {
		  $class = "alert-secondary";
	  }

	  $output  = "<div class=\"container\">";
	  $output .= "<div class=\"alert " . $class . " alert-dismissible show\" role=\"alert\">";
	  $output .= "<strong>" . $title . "</strong> " . $description;
	  $output .= "<button type=\"button\" class=\"btn-close\" data-dismiss=\"alert\" aria-label=\"Close\"></button>";
	  $output .= "</div>";
	  $output .= "</div>";

	  return $output;
  }
}

$settingsClass = new settings();
?>
