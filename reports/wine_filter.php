<?php
$wineClass = new wineClass();
?>

<form method="post" id="filter" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="row">
  <div class="col">
    <h5 class="mb-3">Cellar</h5>
    <?php
    foreach ($wineClass->allCellars() AS $cellar) {
      $cellar = new cellar($cellar['uid']);
      
      $checked = "";
      if (isset($_POST['filter'])) {
        if (isset($_POST['cellar_uid']) && in_array($cellar->uid, $_POST['cellar_uid'])) {
          $checked = " checked ";
        }
      } else {
        $checked = " checked ";
      }
      
      $output  = "<div class=\"form-check\">";
      $output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $cellar->uid . "\" name=\"cellar_uid[]\" " . $checked . ">";
      $output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
      $output .= $cellar->name;
      $output .= "</label>";
      $output .= "</div>";
      
      echo $output;
    }
    ?>
  </div>
  <div class="col">
    <h5 class="mb-3">Status</h5>
    <?php
    foreach (explode(",", $settingsClass->value('wine_status')) AS $wine_status) {
      $checked = "";
      if (isset($_POST['filter'])) {
        if (isset($_POST['status']) && in_array($wine_status, $_POST['status'])) {
          $checked = " checked ";
        }
      } else {
        $checked = " checked ";
      }
      
      $output  = "<div class=\"form-check\">";
      $output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $wine_status . "\" name=\"status[]\"" . $checked . ">";
      $output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
      $output .= $wine_status;
      $output .= "</label>";
      $output .= "</div>";
      
      echo $output;
    }
    ?>
  </div>
  <div class="col">
    <h5 class="mb-3">Category</h5>
    <?php
    foreach (explode(",", $settingsClass->value('wine_category')) AS $wine_category) {
      $checked = "";
      if (isset($_POST['filter'])) {
        if (isset($_POST['category']) && in_array($wine_category, $_POST['category'])) {
          $checked = " checked ";
        }
      } else {
        $checked = " checked ";
      }
      
      $output  = "<div class=\"form-check\">";
      $output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $wine_category . "\" name=\"category[]\"" . $checked . ">";
      $output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
      $output .= $wine_category;
      $output .= "</label>";
      $output .= "</div>";
      
      echo $output;
    }
    ?>
  </div>
  <div class="col">
    <h5 class="mb-3">Supplier</h5>
    <?php
    foreach ($wineClass->listFromWines("supplier") AS $supplier) {
      $checked = "";
      if (isset($_POST['filter'])) {
        if (isset($_POST['supplier']) && in_array($supplier['supplier'], $_POST['supplier'])) {
          $checked = " checked ";
        }
      } else {
        $checked = " checked ";
      }
      
      $output  = "<div class=\"form-check\">";
      $output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $supplier['supplier'] . "\" name=\"supplier[]\" " . $checked . ">";
      $output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
      $output .= $supplier['supplier'];
      $output .= "</label>";
      $output .= "</div>";
      
      echo $output;
    }
    ?>
  </div>
  
  
  <div class="col">
    <h5 class="mb-3">Grape</h5>
    <?php
    foreach ($wineClass->listFromWines('grape') AS $grape) {
      $checked = "";
      if (isset($_POST['filter'])) {
        if (isset($_POST['grape']) && in_array($grape['grape'], $_POST['grape'])) {
          $checked = " checked ";
        }
      } else {
        $checked = " checked ";
      }
      
      $output  = "<div class=\"form-check\">";
      $output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $grape['grape'] . "\" name=\"grape[]\" " . $checked . ">";
      $output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
      $output .= $grape['grape'];
      $output .= "</label>";
      $output .= "</div>";
      
      echo $output;
    }
    ?>
  </div>
  
  <input type="hidden" name="filter" value="true">
  <button type="submit" class="btn btn-primary">Filter</button>
</div>
</form>

<hr class="pb-3" />

<h5 class="mb-3">Filter Results</h5>
<?php

if (isset($_POST['cellar_uid'])) {
  $whereConditions[] = ['field' => 'wine_bins.cellar_uid', 'operator' => 'IN', 'value' => $_POST['cellar_uid']];
}

if (isset($_POST['status'])) {
  $whereConditions[] = ['field' => 'wine_wines.status', 'operator' => 'IN', 'value' => $_POST['status']];
}

if (isset($_POST['category'])) {
  $whereConditions[] = ['field' => 'wine_wines.category', 'operator' => 'IN', 'value' => $_POST['category']];
}

if (isset($_POST['supplier'])) {
  $whereConditions[] = ['field' => 'wine_wines.supplier', 'operator' => 'IN', 'value' => $_POST['supplier']];
}

if (isset($_POST['grape'])) {
  $whereConditions[] = ['field' => 'wine_wines.grape', 'operator' => 'IN', 'value' => $_POST['grape']];
}



$wines = $wineClass->allWinesSearch($whereConditions);
echo "<h5>Wines found: " . count($wines) . "</h5>";
?>


<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
  <?php
  foreach ($wines AS $wine) {
    $wine = new wine($wine['uid']);
    
    echo $wine->card();
  }
  ?>
</div>