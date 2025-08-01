<?php
pageAccessCheck("wine");

$wineClass = new wineClass();

$wineUID = filter_var($_GET['wine_uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($wineUID);
$bin = new bin($wine->bin_uid);
$cellar = new cellar($bin->cellar_uid);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// 1. Handle attachment deletion (if requested)
	if (!empty($_POST['delete_attachment'])) {
		$storedFilename = $_POST['delete_attachment'];  // sanitized below
		$storedFilename = basename($storedFilename);   // basic sanitization
		
		$deleted = $wine->deleteAttachment($storedFilename);
		
		if ($deleted) {
			// Optionally set a success message
			$message = "Attachment deleted successfully.";
		} else {
			$message = "Failed to delete attachment.";
		}
		
		$wine = new wine($wineUID);
	}
	
	// 2. Handle attachment upload (if any)
	if (isset($_FILES['attachment'])) {
		$attachmentData = $wine->uploadAttachment('attachment');
		if ($attachmentData) {
			// You can reload the wine object or update $wine->attachments as needed
			$message = "Attachment uploaded successfully.";
		}
		
		$wine = new wine($wineUID);
	}
}


$title = $wine->name;

if (!empty($wine->grape)) {
	$subtitleArray[] = "<a href=\"index.php?n=wine_search&filter=grape&value=" . $wine->grape . "\">" . $wine->grape . "</a>";
}
if (!empty($wine->region_of_origin)) {
	$subtitleArray[] = "<a href=\"index.php?n=wine_search&filter=region_of_origin&value=" . $wine->region_of_origin . "\">" . $wine->region_of_origin . "</a>";
}
if (!empty($wine->category)) {
	$subtitleArray[] = "<a href=\"index.php?n=wine_search&filter=category&value=" . $wine->category . "\">" . $wine->category . "</a>";
}
$subtitle = implode(", ", $subtitleArray);

$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Transaction", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#transactionModal\"");
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Multi-Transaction", "value" => "onclick=\"location.href='index.php?n=wine_transaction_multi'\"");
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#journal-text\"/></svg> Edit Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=edit&uid=" . $wine->uid . "'\"");
$icons[] = array("name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#heart-empty\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#listModal\"");
if (count($wine->transactions()) == 0) {
	$icons[] = array("class" => "text-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete Wine", "value" => "onclick=\"wineDelete(" . $wine->uid . ")\"");
}

echo makeTitle($title, $subtitle, $icons, true, $wine->favButton());
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
echo $wine->statusBanner();
?>

<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="nav-link active" id="information-tab" data-bs-toggle="tab" data-bs-target="#information-tab-pane" type="button" role="tab" aria-controls="information-tab-pane" aria-selected="true">Information</button>
	</li>
	<li class="nav-item" role="presentation">
		<?php
		$attachmentsTitle = "Attachments";
		if (count($wine->attachments()) > 0) {
			$attachmentsTitle .= " <span class=\"badge text-bg-secondary\">" . count($wine->attachments()) . "</span>";
		}
		?>
		<button class="nav-link" id="attachments-tab" data-bs-toggle="tab" data-bs-target="#attachments-tab-pane" type="button" role="tab" aria-controls="attachments-tab-pane" aria-selected="false"><?php echo $attachmentsTitle; ?></button>
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
					  <h5 class="card-title"><?php echo $wine->currentQty(); ?></h5>
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
					  <h5 class="card-title"><a href="index.php?n=wine_search&filter=vintage&value=<?php echo $wine->vintage; ?>"><?php echo $wine->vintage(); ?></a></h5>
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
					  <?php
					  $transaction = new transaction();
					  echo $transaction->transactionsTable($wine->transactions());
					  ?>
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
							echo "<p class=\"card-text\"><a href=\"index.php?n=wine_search&filter=supplier&value=" . $wine->supplier . "\">" . $wine->supplier . "</a></p>";
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
  
  <div class="tab-pane fade" id="attachments-tab-pane" role="tabpanel" aria-labelledby="attachments-tab" tabindex="0">
	  <ul class="list-group mb-3">
	  <?php
	  foreach ($wine->attachments() AS $attachment) {
		  $fileURL = "uploads/" . $attachment['stored'];
		  
		  $output  = "<form method=\"POST\" style=\"margin:0;\">";
		  $output .= "<input type=\"hidden\" name=\"delete_attachment\" value=\"" . $attachment['stored'] . "\" />";
		  $output .= "<li class=\"list-group-item d-flex justify-content-between align-items-center\">";
		  $output .= "<a href=\"" . $fileURL . "\" target=\"_blank\">" . $attachment['original']  . "</a>";
		  $output .= "<button type=\"submit\" class=\"btn btn-sm btn-danger\" onclick=\"return confirm('Are you sure you want to delete this file?  This action cannot be undone!')\"><svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Delete</button>";
		  $output .= "</form>";
		  $output .= "</li>";
		  
		  echo $output;
	  }
	  ?>
	  </ul>

	  <div class="mb-3">
		  <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']); ?>" enctype="multipart/form-data">
		   <div class="input-group mb-3">
			 <input class="form-control" required type="file" name="attachment" id="formFile">
			 <button class="btn btn-outline-secondary" type="submit" id="button-addon1">Upload</button>
		   </div>
		   <div id="emailHelp" class="form-text">
			 Allowed file types: <?php echo $settingsClass->value('uploads_allowed_filetypes'); ?>
		   </div>
		 </form>

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
						  <label for="transaction_bottles" class="form-label">Bottles</label>
						  <input type="number" class="form-control" id="transaction_bottles" value="1">
						  <div id="transaction_bottlesHelp" class="form-text">
							  <?php echo $wine->currentQty() . " available"; ?>
						  </div>
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
					<div class="col-12">
						<div class="mb-3">
						  <label for="transaction_date_posted" class="form-label">Posting Date</label>
						  <div class="input-group">
								<span class="input-group-text" id="transaction_date_posted-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
								<input type="date" class="form-control" name="transaction_date_posted" id="transaction_date_posted" value="<?php echo date('Y-m-d'); ?>" aria-describedby="transaction_date_posted">
							</div>
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

<div class="modal fade" id="listModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h1 class="modal-title fs-5" id="exampleModalLabel"> Lists</h1>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	  </div>
	  <div class="modal-body">
		  <h6>My Lists</h6>
		  <ul class="list-group list-group-flush">
			  <?php
			  $myListsFilter[] = array("field" => "type", "operator" => "=", "value" => "private");
			  $myListsFilter[] = array("field" => "member_ldap", "operator" => "=", "value" => $_SESSION['username']);
			  foreach ($wineClass->allLists($myListsFilter) AS $list) {
				  $list = new wine_list($list['uid']);
				  
				  echo $list->liItem($wine->uid);
			  }
			  ?>
		  </ul>
		  
		  <h6 class="pt-3">Public Lists</h6>
			<ul class="list-group list-group-flush">
				<?php
				$publicListsFilter[] = array("field" => "type", "operator" => "=", "value" => "public");
				$publicListsFilter[] = array("field" => "member_ldap", "operator" => "!=", "value" => $_SESSION['username']);
				foreach ($wineClass->allLists($publicListsFilter) AS $list) {
					$list = new wine_list($list['uid']);
					
					echo $list->liItem($wine->uid);
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
function toggleListButton(button) {
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
			//alert(xhr.responseText);
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
	formData.append('date_posted', document.getElementById('transaction_date_posted').value);
	formData.append('name', document.getElementById('transaction_name').value);
	formData.append('price_per_bottle', document.getElementById('transaction_price_per_bottle').value);
	formData.append('type', document.getElementById('transaction_type').value);
	formData.append('description', document.getElementById('transaction_description').value);
	
	// Send the request over the network
	xhr.send(formData);
};

function wineDelete(wineUID) {
  var wine_uid = (wineUID);
  
  if (window.confirm("Are you really sure you want to delete this wine (and photograph)?  Transactions will not be deleted.  WARNING!  This action cannot be undone!")) {
	var xhr = new XMLHttpRequest();

	var formData = new FormData();
	formData.append("wine_uid", wine_uid);
	
	xhr.onload = async function() {
	  location.href = 'index.php?n=wine_index';
	}

	xhr.onerror = function(){
	  // failure case
	  alert (xhr.responseText);
	}

	xhr.open ("POST", "../actions/wine_delete.php", true);
	xhr.send (formData);

	return false;
  }
}
</script>