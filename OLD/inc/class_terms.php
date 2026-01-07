<?php
class terms extends term {
  public function all() {
      global $db;
  
      $sql = "SELECT * FROM " . self::$table_name;
      $sql .= " ORDER BY date_start DESC";
  
      $terms = $db->query($sql)->fetchAll();
  
      return $terms;
  }
  
  public function checkIsInTerm($date = null) {
    // accounts for 0th week automatically
    // so the term dates on the site should start on 1st week (as given by Oxford)

    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE DATE_SUB(date_start, INTERVAL 7 DAY) <= '" . $date . "' AND date_end >= '" . $date . "'";
    $sql .= " LIMIT 1";

    $term = $db->query($sql)->fetchAll();

    return $term;
  }

  public function arrayWindowOfWeeks() {
    global $settingsClass;

    $weeksBefore = $settingsClass->value('meal_weeks_navbar_before');
    $weeksAfter = $settingsClass->value('meal_weeks_navbar_after');
    
    $thisWeekStartDate = firstDayOfWeek();
    $i = $weeksBefore;
    do {
      $weekDate = date('Y-m-d', strtotime($thisWeekStartDate . " -" . $i . " week"));

      $weeksArray[] = $weekDate;

      $i--;
    } while ($i > 0);

    $i = 0;
    do {
      $weekDate = date('Y-m-d', strtotime($thisWeekStartDate . " +" . $i . " week"));

      $weeksArray[] = $weekDate;

      $i++;
    } while ($i <= $weeksAfter);

    return $weeksArray;
  }
}
?>
