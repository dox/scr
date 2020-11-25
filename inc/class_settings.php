<?php
class settings {
  protected static $table_name = "settings";

  public $uid;
  public $name;
  public $description;
  public $value;

  public function all() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " ORDER BY name DESC";

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

    return $update;
  }
  
  public function create($array = null) {
	global $db;

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

    return $create;
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
