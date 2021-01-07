<?php
class members extends member {
  public function all() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " ORDER BY precedence ASC, lastname ASC, firstname ASC";

    $terms = $db->query($sql)->fetchAll();

    return $terms;
  }

  public function allEnabled($type = 'all') {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    if ($type == "scr") {
      $sql .= " WHERE type = 'SCR'";
    } elseif ($type == "mcr") {
      $sql .= " WHERE type = 'MCR'";
    } else {
      $sql .= " WHERE type = type";
    }
    $sql .= " AND enabled = 1";
    $sql .= " ORDER BY precedence ASC, lastname ASC, firstname ASC";

    $terms = $db->query($sql)->fetchAll();

    return $terms;
  }

  public function allDisabled($type = 'all') {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    if ($type == "scr") {
      $sql .= " WHERE type = 'SCR'";
    } elseif ($type == "mcr") {
      $sql .= " WHERE type = 'MCR'";
    } else {
      $sql .= " WHERE type = type";
    }
    $sql .= " AND enabled = 0";
    $sql .= " ORDER BY precedence ASC, lastname ASC, firstname ASC";

    $terms = $db->query($sql)->fetchAll();

    return $terms;
  }

  public function memberCategories() {
    global $settingsClass;

    $memberCategoriesMandatory = array("");
    $memberCategoriesSettings = explode(",", $settingsClass->value('member_categories'));
    $memberCategories = array_merge($memberCategoriesMandatory, $memberCategoriesSettings);

    return $memberCategories;
  }

  public function memberTitles() {
    global $settingsClass;

    $memberTitlesMandatory = array("");
    $memberTitlesSettings = explode(",", $settingsClass->value('member_titles'));
    $memberTitles = array_merge($memberTitlesMandatory, $memberTitlesSettings);

    return $memberTitles;
  }

  public function dietaryOptions() {
    global $settingsClass;

    $dietaryOptions = explode(",", $settingsClass->value('meal_dietary'));
    sort($dietaryOptions);

    return $dietaryOptions;
  }
}
?>
