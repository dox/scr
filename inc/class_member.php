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
      $name = "Name hidden";
    }

    return $name;
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
