<?php
class member {
  protected static $table_name = "members";

  public $uid;
  public $ldap;
  public $title;
  public $firstname;
  public $lastname;
  public $type;
  public $email;
  public $dietary;
  public $opt_in;

  function __construct($memberUID = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $memberUID . "'";
    $sql .= " OR ldap = '" . $memberUID . "'";

		$member = $db->query($sql)->fetchArray();

		foreach ($member AS $key => $value) {
			$this->$key = $value;
		}
  }

  public function displayName() {
    if (!empty($this->title)) {
      $title = $this->title . " ";
    }

    if (!empty($this->firstname)) {
      $firstname = $this->firstname . " ";
    }

    if (!empty($this->lastname)) {
      $lastname = $this->lastname . " ";
    }

    $name = $title . $firstname . $lastname;

    return $name;
  }

  public function public_displayName() {
    if ($this->opt_in == 1) {
      $name = $this->displayName();
    } else {
      $name = "HIDDEN";
    }

    if ($this->ldap == $_SESSION['username']) {
      $name = $name . " <i>(You)</i>";
    }

    return $name;
  }

  public function memberBadge() {
    global $settingsClass;

    $scrStewardLDAP = $settingsClass->value('member_steward');

    $starIcon = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-star-fill\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">
      <path d=\"M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z\"/>
    </svg>";

    if ($this->ldap == $scrStewardLDAP) {
      $badge = " <span class=\"badge bg-warning\">" . $starIcon . " SCR Steward</span>";
    } else {
      $badge = "";
    }

    return $badge;
  }

  public function updateMemberPrecendece($memberUID = null, $order = null) {
    global $db;
    global $logsClass;

    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET precedence = '" . $order . "' ";
    $sql .= " WHERE uid = '" . $memberUID . "' ";
    $sql .= " LIMIT 1";

    $terms = $db->query($sql);

    return $terms;
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
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);

    return $update;
  }




}
?>
