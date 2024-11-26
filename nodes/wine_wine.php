<?php
pageAccessCheck("wine");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($cleanUID);

$array = array(
	"type" => "test",
	"cellar_uid" => "1",
	"wine_uid" => $wine->uid,
	"value" => "-1",
	"description" => "test"
);
//$wine->create_transaction($array);

$cellar = new cellar($wine->cellar_uid);

$title = $wine->bin . ": " . $wine->name;
$subtitle = "<a href=\"index.php?n=wine_search&filter=grape&value=" . $wine->grape . "\">" . $wine->grape . "</a>, " . "<a href=\"index.php?n=wine_search&filter=country_of_origin&value=" . $wine->country_of_origin . "\">" . $wine->country_of_origin . "</a>";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#favListModal\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Edit Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=edit&uid=" . $wine->uid . "'\"");
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

<?php
if ($wine->status <> 1) {
	echo "<div class=\"alert alert-warning text-center\" role=\"alert\">WINE IN-STATUS</div>";
}
?>

<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="nav-link active" id="information-tab" data-bs-toggle="tab" data-bs-target="#information-tab-pane" type="button" role="tab" aria-controls="information-tab-pane" aria-selected="true">Information</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions-tab-pane" type="button" role="tab" aria-controls="transactions-tab-pane" aria-selected="false">Transactions</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs-tab-pane" type="button" role="tab" aria-controls="logs-tab-pane" aria-selected="false">Logs</button>
	</li>
</ul>

<div class="tab-content pt-3" id="myTabContent">
  <div class="tab-pane fade show active" id="information-tab-pane" role="tabpanel" aria-labelledby="information-tab" tabindex="0">
	  <div class="row">
		  <div class="col">
			  <div class="card mb-3">
				  <div class="card-body">
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
					  <?php
					  printArray($wine);
					  ?>
				  </div>
			  </div>
		  </div>
		  <div class="col-xl-4">
			  <div class="card mb-3">
					<img src="<?php echo $wine->photograph(); ?>" class="card-img-top" alt="...">
					<div class="card-body">
						Image
					</div>
			   </div>
				
				<div class="card mb-3">
					  <div class="card-body">
						  <h5 class="card-title">Supplier</h5>
						  <?php
						  if (!empty($wine->supplier)) {
							echo "<p class=\"card-text\">" . $wine->supplier . "</p>";
							}
							?>
					  </div>
					  <?php
					  if (!empty($wine->supplier)) {
						  echo "<ul class=\"list-group list-group-flush\"><li class=\"list-group-item\">Ref: " . $wine->supplier_ref . "</li></ul>";
						}
						?>
				  </div>
			  
			  <div class="card mb-3">
				  <img src="https://as1.ftcdn.net/v2/jpg/00/91/94/40/1000_F_91944058_7lRk2ok81t7vS6JsVP81BL9Jp6VajrSh.jpg" class="card-img-top" alt="...">
				  <div class="card-body">
					  Wine Origin
				  </div>
			  </div>
		  </div>
	  </div>
  </div>
  <div class="tab-pane fade" id="transactions-tab-pane" role="tabpanel" aria-labelledby="transactions-tab" tabindex="0">
	  <div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Transactions</h5>
				
				<table class="table">
				<thead>
				  <tr>
					<th scope="col">Date</th>
					<th scope="col">Username</th>
					<th scope="col">Type</th>
					<th scope="col">Value</th>
					<th scope="col">Description</th>
				  </tr>
				</thead>
				<tbody>
					<?php
					foreach($wine->transactions() AS $transaction) {
						$valueClass = "";
						if ($transaction['value'] < 0) {
							$valueClass = "text-danger";
						} elseif($transaction['value'] > 0) {
							$valueClass = "text-success";
						}
						$output  = "<tr>";
						$output .= "<td>" . dateDisplay($transaction['date']) . " " . timeDisplay($transaction['date']) . "</td>";
						$output .= "<td>" . $transaction['username'] . "</td>";
						$output .= "<td>" . $transaction['type'] . "</td>";
						$output .= "<td class=\"" . $valueClass . "\">" . $transaction['value'] . "</td>";
						$output .= "<td>" . $transaction['description'] . "</td>";
						$output .= "";
						$output .= "</tr>";
						
						echo $output;
					}
					?>
				</tbody>
				</table>
			</div>
		</div>
  </div>
  <div class="tab-pane fade" id="logs-tab-pane" role="tabpanel" aria-labelledby="logs-tab" tabindex="0">
	  <?php
	  foreach ($wine->logs() AS $log) {
		  printArray($log);
	  }
	  ?>
  </div>
</div>



<div class="modal fade" id="favListModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h1 class="modal-title fs-5" id="exampleModalLabel">Wine Lists</h1>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body">
		<ul class="list-group list-group-flush">
			  <?php
			  foreach ($wine->getAllLists() AS $list) {
				  $list = new wine_list($list['uid']);
				  
				  $listIcon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#heart-empty\"/></svg> ";
				  if ($list->isWineInList($wine->uid)) {
					  $listIcon = "<svg width=\"1em\" height=\"1em\" style=\"color: red;\"><use xlink:href=\"img/icons.svg#heart-full\"/></svg> ";
				  }
				  
				  echo "<li class=\"list-group-item\"><a class=\"dropdown-item\" href\"#\" onClick=\"handleButtonClick(this)\" data-listuid=\"" . $list->uid . "\" data-wineuid=\"" . $wine->uid . "\">" . $listIcon . $list->name . "</a></li>";
			  }
			  ?>
		  </ul>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

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
