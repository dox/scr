<?php
class members extends member {
  public function all() {
    global $db;

    $sql  = "SELECT *  FROM " . self::$table_name;
    $sql .= " ORDER BY precedence ASC, lastname ASC, firstname ASC";

    $terms = $db->query($sql)->fetchAll();

    return $terms;
  }

  public function memberTypes() {
    $memberType[] = "Principal";
    $memberType[] = "Vice Principal";
    $memberType[] = "Fellow";
    $memberType[] = "Research Fellow";
    $memberType[] = "Stipendiary Lecturer";
    $memberType[] = "Non-Stipendiary Lecturer";
    $memberType[] = "Dining Member";
    $memberType[] = "Official Member";
    $memberType[] = "College Staff";
    $memberType[] = "Other";

    return $memberType;
  }

  public function memberTitles() {
    global $settingsClass;

    $memberTitlesMandatory = array("");
    $memberTitlesSettings = explode(",", $settingsClass->value('member_titles'));
    $memberTitles = array_merge($memberTitlesMandatory, $memberTitlesSettings);
    
    return $memberTitles;
  }
}
?>
