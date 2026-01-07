<?php
$user->pageCheck('wine');

$wines = new Wines();
$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
$wine = new Wine($cleanUID);
$bin = new Bin($wine->bin_uid);
$cellar = new Cellar($bin->cellar_uid);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Upload attachment
	if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
		$wine->updateAttachment($_FILES['attachment']);
	}

	// Delete attachment
	if (isset($_POST['delete_attachment'])) {
		$filename = $_POST['delete_attachment'];
		$wine->removeAttachment($filename);
	}
}



$fields = ['grape', 'region_of_origin', 'category'];
$subtitleArray = [];

foreach ($fields as $field) {
	if (!empty($wine->$field)) {
		$url = "index.php?page=wine_filter
		&conditions[0][field]=wine_wines." . $field . "
		&conditions[0][operator]==
		&conditions[0][value]=" . $wine->$field . "
		&conditions[1][field]=wine_wines.status
		&conditions[1][operator]=!=
		&conditions[1][value]=Closed";
		
		$subtitleArray[] = "<a href=\"" . htmlspecialchars($url) . "\">" . $wine->$field . "</a>";
	}
}

$subtitle = implode(", ", $subtitleArray);

echo pageTitle(
	$wine->clean_name(),
	implode(", ", $subtitleArray),
	[
		[
			'permission' => 'wine',
			'title' => 'Edit Wine',
			'class' => '',
			'event' => 'index.php?page=wine_wine_edit&uid=' . $wine->uid,
			'icon' => 'pencil'
		],
		[
			'permission' => 'wine',
			'title' => 'Add Transaction',
			'class' => '',
			'event' => 'index.php?page=wine_transaction_add&wine_uid=' . $wine->uid,
			'icon' => 'receipt'
		],
		[
			'permission' => 'wine',
			'title' => 'Delete Wine',
			'class' => 'text-danger',
			'event' => '',
			'icon' => 'trash3',
			'data' => [
				'bs-toggle' => 'modal',
				'bs-target' => '#deleteTermModal'
			]
		]
	],
	$wine->uid
);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?page=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?page=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<li class="breadcrumb-item"><a href="index.php?page=wine_bin&uid=<?php echo $bin->uid?>"><?php echo $bin->name; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $wine->name; ?></li>
	</ol>
</nav>

<hr/>

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
					  <div class="subheader text-nowrap text-truncate">Bottles</div>
					  <div class="h1 text-truncate"><?php echo $wine->currentQty(); ?></div>
				  </div>
			  </div>
		  </div>
		  <div class="col">
			  <div class="card mb-3">
				  <div class="card-body">
					  <div class="subheader text-nowrap text-truncate">Purchase Price</div>
					  <div class="h1 text-truncate"><?php echo formatMoney($wine->pricePerBottle("Purchase")); ?></div>
				  </div>
			  </div>
		  </div>
		  <div class="col">
			  <div class="card mb-3">
				  <div class="card-body">
					  <div class="subheader text-nowrap text-truncate">Internal/External Price</div>
					  <div class="h1 text-truncate"><?php echo formatMoney($wine->pricePerBottle("Internal")) . " / " . formatMoney($wine->pricePerBottle("External")); ?></div>
				  </div>
			  </div>
		  </div>
		  <div class="col">
			  <div class="card mb-3">
				  <div class="card-body">
					  <div class="subheader text-nowrap text-truncate">Vintage</div>
					 <?php
					 $url =
						 'index.php?page=wine_filter'
						 . '&conditions[0][field]=wine_wines.vintage'
						 . '&conditions[0][operator]=='
						 . '&conditions[0][value]=' . rawurlencode($wine->vintage())
						 . '&conditions[1][field]=wine_wines.status'
						 . '&conditions[1][operator]=!='
						 . '&conditions[1][value]=Closed';
					 ?>
					  <div class="h1 text-truncate"><a href="<?= htmlspecialchars($url) ?>"><?php echo htmlspecialchars($wine->vintage()); ?></a></div>
				  </div>
			  </div>
		  </div>
		  <div class="col">
			  <div class="card mb-3">
				  <div class="card-body">
					  <div class="subheader text-nowrap text-truncate">Wine Code</div>
					  <?php
					   $url =
						   'index.php?page=wine_filter'
						   . '&conditions[0][field]=wine_wines.code'
						   . '&conditions[0][operator]=='
						   . '&conditions[0][value]=' . rawurlencode($wine->code())
						   . '&conditions[1][field]=wine_wines.status'
						   . '&conditions[1][operator]=!='
						   . '&conditions[1][value]=Closed';
					   ?>
					  <div class="h1 text-truncate"><a href="<?= htmlspecialchars($url) ?>"><?php echo htmlspecialchars($wine->code()); ?></a></div>
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
						<h5 class="card-title">Notes</h5>
						<?php echo $wine->notes; ?>
					</div>
			</div>
			<div class="card mb-3">
				  <div class="card-body">
					  <h5 class="card-title">Transactions</h5>
					  
					  <table class="table mt-3">
						<thead>
						  <tr>
							<th scope="col">Date</th>
							<th scope="col">Username</th>
							<th scope="col">Bottles</th>
							<th scope="col">£</th>
							<th scope="col">Name</th>
						  </tr>
						</thead>
						<tbody>
							<?php
							$transactions = $wines->transactions([
								'wine_uid' => ['=', $wine->uid]
							]);
							
							foreach ($transactions as $transaction) {
								$url = "index.php?page=wine_transaction&uid=" . $transaction->uid;
								
								$output  = "<tr>";
								$output .= "<th scope=\"row\">" . formatDate($transaction->date_posted, 'short') . "</th>";
								$output .= "<td>" . ($transaction->username) . "</td>";
								$output .= "<td class=\"" . ($transaction->bottles < 0 ? 'text-danger' : 'text-success') . "\">" . $transaction->bottles . "</td>";
								$output .= "<td>" . formatMoney($transaction->value()) . "</td>";
								$output .= "<td><a href=\"" . $url . "\">" . htmlspecialchars($transaction->name) . "</a></td>";
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
							 $url =
								 'index.php?page=wine_filter'
								 . '&conditions[0][field]=wine_wines.supplier'
								 . '&conditions[0][operator]=='
								 . '&conditions[0][value]=' . rawurlencode($wine->supplier)
								 . '&conditions[1][field]=wine_wines.status'
								 . '&conditions[1][operator]=!='
								 . '&conditions[1][value]=Closed';
								 
							echo "<p class=\"card-text\"><a href=\"" . htmlspecialchars($url) . "\">" . htmlspecialchars($wine->supplier) . "</a></p>";
							}
							?>
					  </div>
					  <?php
					  if (!empty($wine->supplier)) {
						  echo "<ul class=\"list-group list-group-flush\"><li class=\"list-group-item\">Ref: " . htmlspecialchars($wine->supplier_ref) . "</li></ul>";
						}
						?>
				  </div>
		  </div>
	  </div>
  </div>
  
  <div class="tab-pane fade" id="attachments-tab-pane" role="tabpanel" aria-labelledby="attachments-tab" tabindex="0">
	  
	  <div class="mb-3">
			<form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" enctype="multipart/form-data">
				<div class="input-group mb-3">
					<input class="form-control" required type="file" name="attachment" id="formFile">
					<button class="btn btn-outline-secondary" type="submit">Upload</button>
				</div>
				<div id="emailHelp" class="form-text">
					Allowed file types: <?= implode(', ', explode(',', $settings->get('uploads_allowed_filetypes'))) ?>
				</div>
			</form>
		</div>
		
		<hr>
		
		<ul class="list-group">
			<?php foreach ($wine->attachments() as $att): ?>
				<li class="list-group-item d-flex justify-content-between align-items-center">
					<div>
						<strong><a target="_blank" rel="noopener noreferrer" href="uploads/wines/<?= htmlspecialchars($att['stored']) ?>"><?= htmlspecialchars($att['original']) ?></a></strong><br>
						<small class="text-muted">
							Uploaded by <?= htmlspecialchars($att['username']) ?>
							on <?= htmlspecialchars($att['uploaded_at']) ?>
						</small>
					</div>
					
					<form method="POST" class="ms-3">
						<input type="hidden"
						name="delete_attachment"
						value="<?= htmlspecialchars($att['stored']) ?>">
						<button type="submit"
						class="btn btn-sm btn-outline-danger"
						onclick="return confirm('Delete this attachment?');">
						Delete
						</button>
					</form>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	
  <div class="tab-pane fade" id="logs-tab-pane" role="tabpanel" aria-labelledby="logs-tab" tabindex="0">
	  <?php
	  echo "Coming soon";
	  //echo $logsClass->displayTable($wine->logs());
	  ?>
  </div>
</div>



<div class="modal fade" id="wineFavouriteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Wine Favourite Lists</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
		</div>
		<div id="modal-ajax"></div>
	</div>
  </div>
</div>

<script>
// Load AJAX menu
remoteModalLoader('.load-remote-winefav', '#wineFavouriteModal', '#modal-ajax');
</script>