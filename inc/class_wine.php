<?php
class wineClass {
  public function getAllCellars() {
    global $db;
  
    $sql  = "SELECT * FROM wine_cellars";
    $sql .= " ORDER BY name ASC";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllBins($includeEmpty = false) {
    global $db;
  
    $sql  = "SELECT * FROM wine_bins";
    if ($includeEmpty != true) {
      $sql .= " WHERE qty > 0";
    }
    $sql .= " ORDER BY name ASC";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllWines($includeEmpty = false) {
    global $db;
  
    $sql  = "SELECT * FROM wine_wines";
    
    if ($includeEmpty != true) {
      $sql .= " WHERE qty > 0";
    }
    $sql .= " ORDER BY name ASC";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllSuppliers() {
    global $db;
  
    $sql  = "SELECT DISTINCT supplier FROM wine_wines";
    $sql .= " ORDER BY supplier ASC";
  
    $results = $db->query($sql)->fetchAll();
    
    foreach ($results AS $result) {
      $returnArray[] = $result['supplier'];
    }
  
    return $returnArray;
  }
  
  public function getAllWinesByFilter($filter, $value, $includeEmpty = false) {
    global $db;
  
    $sql  = "SELECT * FROM wine_wines";
    $sql .= " WHERE " . $filter . " = '" . $value . "'";
    if ($includeEmpty != true) {
      $sql .= " AND qty > 0";
    }
    $sql .= " ORDER BY name ASC";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllWineBottlesTotal() {
    global $db;
    
    $sql  = "SELECT SUM(qty) AS total FROM wine_wines";
    
    $result = $db->query($sql)->fetchArray();
    
    return $result['total'];
  }
  
  public function searchAllWines($searchArray = null, $cellarUID = null, $limit = null) {
    global $db;
    
    foreach ($searchArray AS $searchKey => $searchString) {
      $searchStatements[] = $searchKey . " LIKE '%" . $searchString . "%'";
    }
    
    $sql  = "SELECT * FROM wine_wines WHERE";
    if (isset($cellarUID)) {
      $sql .= " cellar_uid = '" . $cellarUID . "' AND ";
    }
    $sql .= "(" . implode(" OR ", $searchStatements) . ")";
    $sql .= " ORDER BY name ASC";
    
    if ($limit) {
      $sql .= " LIMIT " . $limit;
    }
    
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllLists() {
    global $db;
    
    // return private lists first, then public
    // return more recently updated lists at the top
    // then return by name
  
    $sql  = "SELECT * FROM wine_lists";
    $sql .= " WHERE type = 'public' OR member_ldap = '" . $_SESSION['username'] . "'";
    $sql .= " ORDER BY type ASC, last_updated DESC, name ASC";
    
    $results = $db->query($sql)->fetchAll();
    
    foreach ($results AS $result) {
      if ($result['type'] == "public" && $result['member_ldap'] != $_SESSION['username']) {
        $listName = $result['member_ldap'] . " " . $result['name'];
        $returnArray[] = array("uid" => $result['uid'], "name" => $listName, "type" => $result['type'], "member_ldap" => $result['member_ldap'], "wine_uids" => $result['wine_uids']);
      } else {
        $returnArray[] = array("uid" => $result['uid'], "name" => $result['name'], "type" => $result['type'], "member_ldap" => $result['member_ldap'], "wine_uids" => $result['wine_uids']);
      }
    }
  
    return $returnArray;
  }
  
  public function getAllMemberLists($memberLDAP) {
    global $db;
  
    $sql  = "SELECT * FROM wine_lists";
    $sql .= " WHERE member_uid = '" . $memberLDAP . "'";
    $sql .= " ORDER BY name ASC";
    
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllWinesFromList($wine_uids_array) {
    global $db;
    
    
    
    if (!empty($wine_uids_array)){ 
      $sql  = "SELECT * FROM wine_wines";
      $sql .= " WHERE uid IN (" . $wine_uids_array . ")";
      $sql .= " ORDER BY name ASC";
      
      $results = $db->query($sql)->fetchAll();
    } else {
      return array();
    }
    
  
    return $results;
  }
  
  public function stats_winesByGrape($cellarUID = null, $includeEmpty = false) {
    global $db;
  
    $sql  = "SELECT grape, COUNT(*) AS totalBins FROM wine_wines";
    $sql .= " WHERE grape <> ''";
    if ($cellarUID != null) {
      $sql .= " AND cellar_uid = '" . $cellarUID . "'";
    }
    if ($includeEmpty != true) {
      $sql .= " AND qty > 0";
    }
    $sql .= " GROUP BY grape";
  
    $results = $db->query($sql)->fetchAll();
        
    foreach ($results AS $result) {
      $returnArray[$result['grape']] = $result['totalBins'];
    }
  
    return $returnArray;
  }
  
  public function stats_winesByCode($includeEmpty = false) {
    global $db;
  
    $sql  = "SELECT code, COUNT(*) AS totalBins FROM wine_wines";
    $sql .= " WHERE code <> ''";
    if ($includeEmpty != true) {
      $sql .= " AND qty > 0";
    }
    $sql .= " GROUP BY code";
  
    $results = $db->query($sql)->fetchAll();
        
    foreach ($results AS $result) {
      $returnArray[$result['code']] = $result['totalBins'];
    }
  
    return $returnArray;
  }
  
  public function stats_winesByCountry() {
    global $db;
  
    $sql  = "SELECT country_of_origin, COUNT(*) AS totalBins FROM wine_wines";
    $sql .= " WHERE country_of_origin <> ''";
    $sql .= " GROUP BY country_of_origin";
    $sql .= " ORDER BY country_of_origin ASC";
  
    $results = $db->query($sql)->fetchAll();
        
    foreach ($results AS $result) {
      $returnArray[$result['country_of_origin']] = $result['totalBins'];
    }
  
    return $returnArray;
  }
}


  


class wine_list extends wineClass {
  protected static $table_name = "wine_lists";
  
  function __construct($listUID = null) {
    global $db;
  
    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $listUID . "'";
  
    $results = $db->query($sql)->fetchArray();
  
    foreach ($results AS $key => $value) {
      $this->$key = $value;
    }
  }
  
  public function name_full() {
    $output  = $this->name;
    $output .= " <small>(" . count($this->winesInList()) . autoPluralise(" wine", " wines", count($this->winesInList())) . ")</small>";
    
    if ($this->member_ldap != $_SESSION['username']) {
      $output .= " <span class=\"badge text-bg-info\">" . $this->member_ldap . "</span>";
    }
    if ($this->type == "private") {
      $output .= " <span class=\"badge text-bg-secondary\">Private</span>";
    }
    
    return $output;
  }
  
  public function winesInList() {
    
    if (!empty($this->wine_uids) ){
      $winesInList = explode(",", $this->wine_uids);
    } else {
      $winesInList = array();
    }
    
    return $winesInList;
  }
  
  public function isWineInList($wineUID = null) {
    $inList = false;
    
    if (in_array($wineUID, explode(",", $this->wine_uids))) {
      $inList = true;
    }
    
    return $inList;
  }
  
  public function addToList($wineUID = null) {
    global $db;
    
    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET wine_uids = CONCAT(wine_uids, '," . $wineUID . "')";
    $sql .= ", last_updated = '" . date(c) . "'";
    $sql .= " WHERE uid = '" . $this->uid . "'";
    
    echo $sql;
    
    $results = $db->query($sql)->fetchArray();
  }
  
  public function updateList($wineUIDSArray = null) {
    global $db;
    
    $sql  = "UPDATE " . self::$table_name;
    $sql .= " SET wine_uids = '" . implode(",", $wineUIDSArray) . "'";
    $sql .= ", last_updated = '" . date('c') . "'";
    $sql .= " WHERE uid = '" . $this->uid . "'";
    
    echo $sql;
    
    $results = $db->query($sql);
  }  
}


?>
