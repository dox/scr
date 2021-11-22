<?php
class terms extends term {
  public function all() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " ORDER BY date_start DESC";

    $terms = $db->query($sql)->fetchAll();

    return $terms;
  }

  public function checkIsInTerm($date = null) {
    // accounts for 0th week automatically
    // so the term dates on the site should start on 1st week (as given by Oxford)

    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " WHERE DATE_SUB(date_start, INTERVAL 7 DAY) <= '" . $date . "' AND date_end >= '" . $date . "'";
    $sql .= " LIMIT 1";

    //echo $sql;

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


/*
 * Oxford Dates of Term
 * Version 2014-01
 *
 * A small function that will pass a date ('YYYY-mm-dd') and
 * providing that date falls within an Oxford term, it will
 * return the start date, end date and term name.
 *
 * Dates taken from http://www.ox.ac.uk/about_the_university/university_year/dates_of_term.html
 *
 * Made by Andrew Breakspear @ St Edmund Hall
 * Feel free to use this in anyway you want, with or without credit
*/

function check_in_range($start_date, $end_date, $date_from_user) {
	// Convert to timestamp
	$start_ts = strtotime($start_date);
	$end_ts = strtotime($end_date);
	$user_ts = strtotime($date_from_user);

	// Check that user date is between start & end
	return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}

?>
