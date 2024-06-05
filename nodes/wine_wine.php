<?php
pageAccessCheck("wine");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($cleanUID);
$cellar = new cellar($wine->cellar_uid);

$title = $wine->name;
$subtitle = $wine->grape . ", " . $wine->country_of_origin;
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
echo makeTitle($title, $subtitle, $icons);


?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $wine->bin; ?></li>
	</ol>
</nav>

<hr class="pb-3" />


<div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add to list</button>
  <ul class="dropdown-menu">
	  <?php
	  foreach ($wine->getAllLists() AS $list) {
		  $list = new wine_list($list['uid']);
		  
		  $listIcon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#heart-empty\"/></svg> ";
		  if ($list->isWineInList($wine->uid)) {
			  $listIcon = "<svg width=\"1em\" height=\"1em\" style=\"color: red;\"><use xlink:href=\"img/icons.svg#heart-full\"/></svg> ";
		  }
		  
		  echo "<li><a class=\"dropdown-item\" href\"#\" onClick=\"handleButtonClick(this)\" data-listuid=\"" . $list->uid . "\" data-wineuid=\"" . $wine->uid . "\">" . $listIcon . $list->name . "</a></li>";
	  }
	  ?>
  </ul>
</div>

<?php
if ($wine->bond == 1) {
	echo "<div class=\"alert alert-warning text-center\" role=\"alert\">WINE IN-BOND</div>";
}
?>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="btn-group-vertical btn-group-sm float-end" role="group" aria-label="Vertical radio toggle button group">
				  <button type="button" class="btn btn-outline-primary">+1</button>
				  <button type="button" class="btn btn-outline-primary">-1</button>
				</div>
				
				<h5 class="card-title"><?php echo $wine->qty; ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?php echo currencyDisplay($wine->pricePerBottle("Purchase")); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Purchase Price</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?php echo currencyDisplay($wine->pricePerBottle("Internal")) . " / " . currencyDisplay($wine->pricePerBottle("External")); ?></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Internal/External Price</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><a href="index.php?n=wine_search&filter=vintage&value=<?php echo $wine->vintage; ?>"><?php echo $wine->vintage; ?></a></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Vintage</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><a href="index.php?n=wine_search&filter=code&value=<?php echo $wine->code; ?>"><?php echo $wine->code; ?></a></h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Wine Code</h6>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xl-8">
		<div class="card mb-3">
			<div class="card-body">
				<?php
				printArray($wine);
				?>
			</div>
		</div>
		
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Tasting Notes</h5>
				<?php echo $wine->tasting; ?>
			</div>
		</div>
		
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Private Notes</h5>
				<?php echo $wine->notes; ?>
			</div>
		</div>
		
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">History</h5>
				
				<ul class="list-unstyled">
				<?php
				foreach($wine->logs() AS $log) {
					echo "<li>" . dateDisplay($log['date']) . " - " .  $log['username'] . " " . $log['description'] . "</li>";
				}
				?>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-xl-4">
		<div class="card mb-3">
			<img src="<?php echo $wine->photograph; ?>" class="card-img-top" alt="...">
			<div class="card-body">
				Image
			</div>
		</div>
		
		<div class="card mb-3">
			<div id="map" class="card-img-top" style="height: 200px;" alt="World map"></div>
			<div class="card-body">
				Wine Origin
			</div>
		</div>
	</div>
</div>

<?php
$list = new wine_list(1);
printArray($list);
?>
<script>
function handleButtonClick(button) {
	// Retrieve the data attributes
	var list_uid = button.getAttribute('data-listuid');
	var wine_uid = button.getAttribute('data-wineuid');
	
	// Create a new XMLHttpRequest object
	var xhr = new XMLHttpRequest();

	// Configure it: POST-request for the URL /submit-data
	xhr.open('POST', 'actions/wine_addToList.php', true);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

	// Prepare the data to be sent
	var params = 'list_uid=' + encodeURIComponent(list_uid) + '&wine_uid=' + encodeURIComponent(wine_uid);

	// Send the request over the network
	xhr.send(params);

	// This will be called after the request is completed
	xhr.onload = function() {
		if (xhr.status != 200) { // analyze HTTP response status
			alert('Error ' + xhr.status + ': ' + xhr.statusText); // e.g. 404: Not Found
		} else {
			//alert('Success: ' + xhr.responseText); // response is the server
			location.reload();
		}
	};

	// This will be called in case of a network error
	xhr.onerror = function() {
		alert('Request failed');
	};
};

</script>
