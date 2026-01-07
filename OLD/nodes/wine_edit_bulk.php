<?php
pageAccessCheck("wine");

$title = "Wine Bulk Edit";
$subtitle = "Manage wine stock and create transactions";
echo makeTitle($title, $subtitle, $icons, true);

$wineClass = new wineClass();
$anyOldWine = new wine();
$class_vars = get_class_vars(get_class($anyOldWine));
foreach ($class_vars AS $name => $value) {
	$classVars[$name] = $name;
}
unset($classVars['uid']);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Bulk Edit</li>
	</ol>
</nav>

<div class="alert alert-danger" role="alert">
	<strong>Warning!</strong> Using this bulk updater will make <u>immediate</u> changes and these actions cannot be undone.  Proceed with extreme caution.
</div>

<hr class="pb-3" />

<form method="post" id="bulk_submit" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="row pb-3">
	<div class="mb-3">
	  <label for="where_criteria" class="form-label">Selection Criteria</label>
	  <?php
	  $where_default = '{"wine_wines.category":"White","wine_wines.vintage":"' . date('Y')-2 . '"}';
	  if (isset($_POST['where_criteria'])) {
		  $where_default = $_POST['where_criteria'];
	  }
	  ?>
	  <textarea class="form-control" id="where_criteria" name="where_criteria" rows="3"><?php echo $where_default; ?></textarea>
	  <div id="where_criteriaHelp" class="form-text">JSON format.  e.g.: {"wine_wines.category":"White","wine_wines.vintage":"<?php echo date('Y')-2; ?>"}</div>
	</div>
</div>

<hr />

<div class="row pb-3">
	<div class="mb-3">
	  <label for="where_criteria" class="form-label">Change Instructions</label>
	  <div class="row align-items-start">
		  <div class="col">
			  <select class="form-select" id="change_key" name="change_key" aria-label="Default select example">
				  <?php
					foreach ($classVars AS $var) {
						$selected = "";
						if (isset($_POST['change_key']) && $_POST['change_key'] == $var) {
							$selected = " selected ";
						}
						echo "<option " . $selected . " value=\"" . $var . "\">" . $var . "</option>";
					}
					?>
			  </select>
		  </div>
		  <div class="col">
			  <?php
				  $change_value = "";
					if (isset($_POST['change_value'])) {
						$change_value = $_POST['change_value'];
					}
				?>
			  <input type="text" class="form-control" id="change_value" name="change_value" value="<?php echo $change_value; ?>" aria-describedby="emailHelp">
		  </div>
	  </div>
	</div>
</div>

<div class="row pb-3">
	<div class="mb-3">
		<div class="alert alert-warning" role="alert">
			<select class="form-select" id="execute" name="execute" aria-label="Default select example">
				  <option selected value="dry_run">Dry Run (No actions are performed)</option>
				  <option value="execute">Execute (WARNING! Actions for each wine that matches the criteria will be updated)</option>
			  </select>
		</div>
	</div>
</div>

<button type="submit" class="btn btn-primary">Submit</button>
</form>

<hr />

<?php
$wines = array();
if (isset($_POST['where_criteria'])) {
	$whereCriteria = json_decode($_POST['where_criteria'], true);

	$wines = $wineClass->allWines($whereCriteria);
}
?>

<div class="row">
	<?php echo count($wines) . " wines"; ?>
	
	<table class="table">
	  <thead>
		<tr>
		  <th scope="col">UID</th>
		  <th scope="col">Status</th>
		  <th scope="col">Cellar</th>
		  <th scope="col">Bin</th>
		  <th scope="col">Name</th>
		  <th scope="col">Vintage</th>
		  <th scope="col">Price Purchase</th>
		  <th scope="col">Price Internal</th>
		  <th scope="col">Price External</th>
		  <th scope="col">Qty.</th>
		</tr>
	  </thead>
	  <tbody>
		  <?php
		  
		  
		  foreach ($wines AS $wine) {
			  $wine = new wine($wine['uid']);
			  $bin = new bin($wine->bin_uid);
			  $cellar = new cellar($bin->cellar_uid);
			  
			  $output  = "<tr>";
			  $output .= "<th scope=\"row\">" . $wine->uid . "</th>";
			  $output .= "<td>" . $wine->status . "</td>";
			  $output .= "<td>" . $cellar->name . "</td>";
			  $output .= "<td>" . $bin->name . "</td>";
			  $output .= "<td><a href=\"index.php?n=wine_wine&wine_uid=" . $wine->uid . "\">" . $wine->clean_name() . "<a/></td>";
			  $output .= "<td>" . $wine->vintage() . "</td>";
			  $output .= "<td>" . $wine->price_purchase . "</td>";
			  $output .= "<td>" . $wine->price_internal . "</td>";
			  $output .= "<td>" . $wine->price_external . "</td>";
			  $output .= "<td>" . $wine->currentQty() . "</td>";
			  $output .= "</tr>";
			  
			  echo $output;
		  }
		  ?>
	  </tbody>
	</table>
</div>

<h6>Actions to be executed...</h6>
<?php
$changeInstructions = json_decode($_POST['change_instruction']);

foreach ($wines AS $wine) {
	  $wine = new wine($wine['uid']);
	  
	  $sql = "UPDATE wine_wines SET " . $_POST['change_key'] .  " = " . $_POST['change_value'] . " WHERE uid = " . $wine->uid . " LIMIT 1;";
	  
	  if ($_POST['execute'] == "execute") {
		  $originalValue = $wine->{$_POST['change_key']};
		  
		  echo "Executing: " . $sql . "<br />";
		  
		  $db->query($sql);
		  
		  $wine = new wine($wine->uid);
		  $newValue = $wine->{$_POST['change_key']};
		  
		  $logArray['category'] = "wine";
		  $logArray['result'] = "success";
		  $logArray['description'] = "Bulk updated [wineUID:" . $wine->uid . "] with field " . $_POST['change_key'] . " from " . $originalValue . " to " . $newValue;
		  $logsClass->create($logArray);
	  } else {
		  echo $sql . "<br />";
	  }
	  
}
?>