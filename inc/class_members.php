<?php
class members extends member {
  public function all() {
      global $db;
  
      $sql = "SELECT * FROM " . self::$table_name;
      $sql .= " ORDER BY precedence ASC, lastname ASC, firstname ASC";
  
      $members = $db->query($sql)->fetchAll();
  
      return $members;
  }
  
  public function allEnabled($type = 'all') {
      global $db;
  
      $sql = "SELECT * FROM " . self::$table_name;
      $conditions = ["enabled = 1"];
  
      if (in_array(strtoupper($type), ['SCR', 'MCR'])) {
          $conditions[] = "type = '" . strtoupper($type) . "'";
      }
      
      $sql .= " WHERE " . implode(" AND ", $conditions);
      $sql .= " ORDER BY precedence ASC, lastname ASC, firstname ASC";
  
      $members = $db->query($sql)->fetchAll();
  
      return $members;
  }

  public function allDisabled($type = 'all') {
      global $db;
  
      $sql = "SELECT * FROM " . self::$table_name;
      $conditions = ["enabled = 0"];
  
      if (in_array(strtoupper($type), ['SCR', 'MCR'])) {
          $conditions[] = "type = '" . strtoupper($type) . "'";
      }
      
      $sql .= " WHERE " . implode(" AND ", $conditions);
      $sql .= " ORDER BY precedence DESC, lastname ASC, firstname ASC";
  
      $members = $db->query($sql)->fetchAll();
  
      return $members;
  }

  public function memberCategories() {
      global $settingsClass;
  
      $raw = $settingsClass->value('member_categories');
      $memberCategoriesSettings = array_filter(
          array_map('trim', explode(',', $raw)),
          fn($v) => $v !== ''
      );
  
      // Always start with mandatory empty string
      $memberCategories = array_merge([''], $memberCategoriesSettings);
  
      return $memberCategories;
  }
  
  public function memberTitles() {
      global $settingsClass;
  
      $raw = $settingsClass->value('member_titles');
      $memberTitlesSettings = array_filter(
          array_map('trim', explode(',', $raw)),
          fn($v) => $v !== ''
      );
  
      // Always start with mandatory empty string
      $memberTitles = array_merge([''], $memberTitlesSettings);
  
      return $memberTitles;
  }
  
  public function dietaryOptions() {
      global $settingsClass;
  
      $raw = $settingsClass->value('meal_dietary');
      $dietaryOptionsSettings = array_filter(
          array_map('trim', explode(',', $raw)),
          fn($v) => $v !== ''
      );
  
      // Sort dietary options alphabetically
      sort($dietaryOptionsSettings);
  
      return $dietaryOptionsSettings;
  }
}
?>
