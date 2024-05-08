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
    
    foreach ($searchArray AS $searchKey => $searchString) {
      $searchStatements[] = $searchKey . " = '" . $searchString . "'";
    }
    
    $sql  = "SELECT * FROM wine_wines";
    $sql .= " WHERE " . implode(" OR ", $searchStatements);
    $sql .= " ORDER BY name ASC";
    
    echo $sql;
    
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllPublicLists() {
    global $db;
  
    $sql  = "SELECT * FROM wine_lists";
    $sql .= " WHERE type = 'public'";
    $sql .= " ORDER BY name ASC";
    
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllMemberLists($memberUID) {
    global $db;
  
    $sql  = "SELECT * FROM wine_lists";
    $sql .= " WHERE member_uid = '" . $memberUID . "'";
    $sql .= " ORDER BY name ASC";
    
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllWinesFromList($listUID) {
    global $db;
  
    $sql  = "SELECT * FROM wine_wines";
    $sql .= " WHERE uid IN (" . $listUID . ")";
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
  
    $sql  = "SELECT * FROM wine_wines";
    $sql .= " WHERE cellar_uid = '" . $this->uid . "'";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllWinesByBin($bin = null) {
    global $db;
  
    $sql  = "SELECT * FROM wine_wines";
    $sql .= " WHERE cellar_uid = '" . $this->uid . "'";
    $sql .= " AND bin = '" . $bin . "'";
    $sql .= " ORDER BY name ASC";
  
    $results = $db->query($sql)->fetchAll();
  
    return $results;
  }
  
  public function getAllWineBottlesTotal() {
    global $db;
  
    $sql  = "SELECT SUM(qty) AS total_wines FROM wine_wines";
    $sql .= " WHERE cellar_uid = '" . $this->uid . "'";
    
    $results = $db->query($sql)->fetchArray();
    
    if (!$results['total_wines'] > 0) {
      $results['total_wines'] = 0;
    }
    
    return $results['total_wines'];
  }
}
  
class wine extends wineClass {
  protected static $table_name = "wine_wines";
  
  function __construct($uid = null) {
    global $db;
    
    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $uid . "'";
    
    $results = $db->query($sql)->fetchArray();
  
    foreach ($results AS $key => $value) {
      $this->$key = $value;
    }
  }
  
  public function totalBottlesInStock() {
    global $db;
    
    $sql  = "SELECT SUM(qty) AS total_wines FROM " . self::$table_name;
    $sql .= " WHERE cellar_uid = '" . $this->cellar_uid . "'";
    $sql .= " AND bin = '" . $this->bin . "'";
    
    $results = $db->query($sql)->fetchArray();
    
    if (!$results['total_wines'] > 0) {
      $results['total_wines'] = 0;
    }
    
    return $results['total_wines'];
  }
  
  public function pricePerBottle($target = "Internal") {
    if ($target == "Internal") {
      $value = $this->price_internal;
    } elseif ($target == "External") {
      if (isset($this->price_external)) {
        $value = $this->price_external;
      } else {
        $value = $this->price_internal;
      }
    } elseif ($target == "Purchase") {
      $value = $this->price_purchase;
    } else {
      $value = 999;
    }
  
    return $value;
  }
  
  public function binCard() {
    $cellar = new cellar($this->cellar_uid);
    
    if ($_GET['n'] != "wine_bin" && count($cellar->getAllWinesByBin($this->bin)) > 1) {
      $url = "index.php?n=wine_bin&cellar_uid=" . $cellar->uid . "&bin=" . $this->bin;
      $description = "Multiple wines in bin (" . count($cellar->getAllWinesByBin($this->bin)) . ")";
    } else {
      $url = "index.php?n=wine_wine&uid=" . $this->uid;
      $description = $this->name;
    }
    
    if ($this->bond == 1) {
      $binName = "<a href=\"" . $url . "\" type=\"button\" class=\"btn btn-primary position-relative\">" . $this->bin . "<span class=\"position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning\">In-Bond</span></a>";
      $cardClass = " border-warning ";
    } else {
      $binName = "<a href=\"" . $url . "\" type=\"button\" class=\"btn btn-primary position-relative\">" . $this->bin . "</a>";
      $cardClass = "";
    }
    
    $output  = "<div class=\"col\">";
    $output .= "<div class=\"card " . $cardClass . " shadow-sm\">";
    $output .= "<div class=\"card-body\">";
    $output .= "<h5 class=\"card-title\">" . $binName . "</h5>";
    $output .= "<p class=\"card-text text-truncate\">" . $description . "</p>";
    $output .= "<div class=\"d-flex justify-content-between align-items-center\">";
    $output .= "<div class=\"btn-group\">";
    $output .= "<a href=\"#\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">" . $this->code . "</a>";
    $output .= "<a href=\"#\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">" . $this->vintage . "</a>";
    $output .= "</div>";
    $output .= "<small class=\"text-body-secondary\">" . $this->totalBottlesInStock() . autoPluralise(" bottle", " bottles", $this->totalBottlesInStock()) . " </small>";
    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";
    
    return $output;
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
  
  
  
}
?>
