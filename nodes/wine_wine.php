<?php
pageAccessCheck("wine");

$wineClass = new wineClass();

$wineUID = filter_var($_GET['wine_uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($wineUID);
$bin = new bin($wine->bin_uid);
$cellar = new cellar($bin->cellar_uid);

$urlGrape = "index.php?n=wine_search&filter=grape&value=" . $wine->grape;
$urlCategory = "index.php?n=wine_search&filter=category&value=" . $wine->category;
$title = $wine->name;
$subtitle = "<a href=\"" . $urlGrape . "\">" . $wine->grape . "</a>, <a href=\"" . $urlCategory . "\">" . $wine->category . "</a>";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Transaction", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#transactionModal\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#journal-text\"/></svg> Edit Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=edit&uid=" . $wine->uid . "'\"");

echo makeTitle($title, $subtitle, $icons);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_bin&bin_uid=<?php echo $bin->uid?>"><?php echo $bin->name; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $wine->name; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<?php
if ($wine->status <> "In Use") {
	echo "<div class=\"alert alert-warning text-center\" role=\"alert\">WINE " . strtoupper($wine->status) . "</div>";
}
?>

<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="nav-link active" id="information-tab" data-bs-toggle="tab" data-bs-target="#information-tab-pane" type="button" role="tab" aria-controls="information-tab-pane" aria-selected="true">Information</button>
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
					  <h5 class="card-title">Transactions</h5>
					  <table class="table">
					  <thead>
						<tr>
						  <th scope="col">Date</th>
						  <th scope="col">Username</th>
						  <th scope="col">Type</th>
						  <th scope="col">Bottles</th>
						  <th scope="col">£/Bottle</th>
						  <th scope="col">Name</th>
						  <th scope="col">Description</th>
						</tr>
					  </thead>
					  <tbody>
						  <?php
						  foreach($wine->transactions() AS $transaction) {
							  $valueClass = "";
							  if ($transaction['bottles'] < 0) {
								  $bottlesClass = "text-danger";
							  } elseif($transaction['bottles'] > 0) {
								  $bottlesClass = "text-success";
							  }
							  $output  = "<tr>";
							  $output .= "<td>" . dateDisplay($transaction['date']) . " " . timeDisplay($transaction['date']) . "</td>";
							  $output .= "<td>" . $transaction['username'] . "</td>";
							  $output .= "<td><span class=\"badge rounded-pill text-bg-info\">" . $transaction['type'] . "</span></td>";
							  $output .= "<td class=\"" . $bottlesClass . "\">" . $transaction['bottles'] . "</td>";
							  $output .= "<td>" . currencyDisplay($transaction['price_per_bottle']) . "</td>";
							  $output .= "<td>" . $transaction['name'] . "</td>";
							  $output .= "<td>" . $transaction['description'] . "</td>";
							  $output .= "</tr>";
							  
							  echo $output;
						  }
						  ?>
					  </tbody>
					  </table>
				  </div>
			  </div>
		  </div>
		  <div class="col-xl-4">
			   <div class="card mb-3">
				 <img src="<?php echo $wine->photographURL(); ?>" class="card-img" alt="...">
				 <div class="card-img-overlay">
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
		  </div>
	  </div>
  </div>
  
  <div class="tab-pane fade" id="logs-tab-pane" role="tabpanel" aria-labelledby="logs-tab" tabindex="0">
	  <?php
	  echo $logsClass->displayTable($wine->logs());
	  ?>
  </div>
</div>

<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h1 class="modal-title fs-5" id="exampleModalLabel">Create Transaction</h1>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body">
		  <form>
			  <div class="row">
				  <div class="col-4">
					  <div class="mb-3">
						  <label for="transaction_total" class="form-label">Bottles</label>
						  <input type="number" class="form-control" id="transaction_bottles" value="1">
						</div>
				</div>
					
					<div class="col-4">
						<div class="mb-3">
						  <label for="transaction_price" class="form-label">£/bottle</label>
						  <input type="number" class="form-control" id="transaction_price_per_bottle" value="<?php echo $wine->price_internal; ?>">
						  <div id="transaction_price_per_bottleHelp" class="form-text"><?php echo currencyDisplay($wine->price_internal) . " / " . currencyDisplay($wine->price_external); ?></div>
						</div>
					</div>
					<div class="col-4">
						<div class="mb-3">
						  <label for="transaction_type" class="form-label">Type</label>
						  <select class="form-select" id="transaction_type">
							  <?php
							  foreach ($wineClass->transactionsTypes() AS $transactionType => $value) {
								  echo "<option value=\"" . $transactionType . "\">" . $transactionType . "</option>";
							  }
							  ?>
						  </select>
						</div>
					</div>
					<div class="col-12">
						<div class="mb-3">
						  <label for="transaction_event" class="form-label">Name</label>
						  <input type="text" class="form-control" id="transaction_name" placeholder="e.g. Formal Hall">
						</div>
					</div>
			  </div>
			  
			  
			  <div class="mb-3">
				<label for="transaction_description" class="form-label">Description</label>
				<textarea class="form-control" id="transaction_description" rows="3"></textarea>
			  </div>
		  </form>
	  </div>
	  <div class="modal-footer">
		  <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
		<button type="button" class="btn btn-success" onClick="createTransaction(this)" data-bs-dismiss="modal" data-wineuid="<?php echo $wine->uid; ?>">Create</button>
	  </div>
	</div>
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
			  foreach ($wineClass->allLists() AS $list) {
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



function createTransaction(button) {
	// Create a new XMLHttpRequest object
	var xhr = new XMLHttpRequest();
	
	// Configure it: POST-request for the URL
	xhr.open('POST', 'actions/wine_transactionCreate.php', true);
	
	// Optional: Add a callback to handle the response
	xhr.onreadystatechange = function () {
		if (xhr.readyState === 4) { // Request is complete
			alert(xhr.responseText);
			if (xhr.status === 200) {
				// Success! Handle the response
				console.log('Success:', xhr.responseText);
				
				location.reload();
			} else {
				// Error! Handle the error
				console.error('Error:', xhr.status, xhr.statusText);
			}
		}
	};
	
	// Create a new FormData object
	var formData = new FormData();
	
	// Append your parameters to the FormData object
	formData.append('wine_uid', button.getAttribute('data-wineuid'));
	formData.append('bottles', document.getElementById('transaction_bottles').value);
	formData.append('name', document.getElementById('transaction_name').value);
	formData.append('price_per_bottle', document.getElementById('transaction_price_per_bottle').value);
	formData.append('type', document.getElementById('transaction_type').value);
	formData.append('description', document.getElementById('transaction_description').value);
	
	// Send the request over the network
	xhr.send(formData);
};

</script>