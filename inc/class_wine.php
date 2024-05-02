<?php
class wineClass {
  public function getAllCellars() {
    global $db;
  
    $sql  = "SELECT * FROM wine_cellars";
    $sql .= " ORDER BY name ASC";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllBins() {
    global $db;
  
    $sql  = "SELECT * FROM wine_bins";
    $sql .= " ORDER BY name ASC";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllWines() {
    global $db;
  
    $sql  = "SELECT * FROM wine_wines";
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
  
  public function searchAllWines($searchArray = null) {
    global $db;
  
    $sql  = "SELECT * FROM wine_wines";
    $sql .= " WHERE code = '" . $searchArray['code'] . "'";
    $sql .= " ORDER BY name ASC";
    
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
}

class cellar extends wineClass {
  protected static $table_name = "wine_cellars";
  
  function __construct($cellarUID = null) {
    global $db;
  
    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $cellarUID . "'";
  
    $results = $db->query($sql)->fetchArray();
  
    foreach ($results AS $key => $value) {
      $this->$key = $value;
    }
  }
  
  public function getBins() {
    global $db;
  
    $sql  = "SELECT * FROM wine_bins";
    $sql .= " WHERE cellar_uid = '" . $this->uid . "'";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllWineBottlesTotal() {
    global $db;
  
    $sql  = "SELECT SUM(qty) AS total_wines FROM wine_wines";
    $sql .= " WHERE bin_uid IN (SELECT uid FROM wine_bins WHERE cellar_uid = '" . $this->uid . "')";
    
    $results = $db->query($sql)->fetchArray();
    
    if (!$results['total_wines'] > 0) {
      $results['total_wines'] = 0;
    }
    
    return $results['total_wines'];
  }
}

class bin extends wineClass {
  protected static $table_name = "wine_bins";
  
  function __construct($binUID = null) {
    global $db;
  
    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $binUID . "'";
  
    $results = $db->query($sql)->fetchArray();
  
    foreach ($results AS $key => $value) {
      $this->$key = $value;
    }
  }
  
  public function getWines() {
    global $db;
  
    $sql  = "SELECT * FROM wine_wines";
    $sql .= " WHERE bin_uid = '" . $this->uid . "'";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
}
  
class wine extends wineClass {
  protected static $table_name = "wine_wines";
  
  function __construct($binUID = null) {
    global $db;
  
    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $binUID . "'";
  
    $results = $db->query($sql)->fetchArray();
  
    foreach ($results AS $key => $value) {
      $this->$key = $value;
    }
  }
}
?>
