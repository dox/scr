<?php
class meals extends meal {
  public function all() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " ORDER BY uid ASC";

    $meals = $db->query($sql)->fetchAll();

    return $meals;
  }

  public function allByDate($date = null) {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date_meal) = '" . $date . "'";
    $sql .= " ORDER BY uid ASC";

    $meals = $db->query($sql)->fetchAll();

    return $meals;
  }

  public function mealTypes() {
    global $settingsClass;

    $mealTypesSettings = explode(",", $settingsClass->value('meal_types'));

    return $mealTypesSettings;
  }

  public function mealLocations() {
    global $db;

    $sql  = "SELECT location  FROM " . self::$table_name;
    $sql .= " WHERE DATE(date_meal) > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
    $sql .= " GROUP BY location";
    $sql .= " ORDER BY location ASC";

    $locations = $db->query($sql)->fetchAll();

    return $locations;
  }

}
?>
