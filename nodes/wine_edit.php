<?php
pageAccessCheck("wine");

$pageType = filter_var($_GET['edit'], FILTER_SANITIZE_STRING);
$wineClass = new wineClass();

if ($pageType == "add") {
	$cleanCellarUID = filter_var($_GET['cellar_uid'], FILTER_SANITIZE_NUMBER_INT);
	$cleanBinUID = filter_var($_GET['bin_uid'], FILTER_SANITIZE_NUMBER_INT);
	$wine = new wine();
	$bin = new bin($cleanBinUID);
	
	$title = "Add New Wine";
	//$subtitle = $wine->grape . ", " . $wine->country_of_origin;
	//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
	echo makeTitle($title, $subtitle, $icons);
	
	$cellar = new cellar($cleanCellarUID);
	
} else {
	$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
	$wine = new wine($cleanUID);
	$bin = new bin($wine->bin_uid);
	$cellar = new cellar($bin->cellar_uid);
	
	$title = "Edit " . $wine->name;
	$subtitle = $wine->grape . ", " . $wine->country_of_origin;
	//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
	echo makeTitle($title, $subtitle, $icons, true);
}
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<?php
		if ($pageType == "edit") {
			echo "<li class=\"breadcrumb-item\"><a href=\"index.php?n=wine_bin&bin_uid=" . $bin->uid . "\">" . $bin->name . "</a></li>";
		}
		?>
		<li class="breadcrumb-item active"><?php echo ucwords($pageType); ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<form class="needs-validation" id="wine_addEdit" novalidate>

<div class="card mb-3">
	<div class="card-body">
		<div class="row">
			<div class="col-4 mb-3">
				<label for="cellar_uid" class="form-label">Cellar</label>
				<select class="form-select" disabled readonly id="cellar_uid" name="cellar_uid" required>
					<?php
					foreach ($wineClass->allCellars() AS $cellarOption) {
						if ($cellarOption['uid'] == $cellar->uid) {
							echo "<option value=\"" . $cellarOption['uid'] . "\" selected>" . $cellarOption['name'] . "</option>";
						} else {
							echo "<option value=\"" . $cellarOption['uid'] . "\">" . $cellarOption['name'] . "</option>";
						}
					}
					?>
				</select>
			</div>
			<div class="col-4 mb-3">
				<label for="cellar_uid" class="form-label">Bin</label>
				<select class="form-select" id="bin_uid" name="bin_uid" required>
					<option></option>
					<?php
					if (isset($_GET['bin_uid'])) {
						$wine->bin_uid = $_GET['bin_uid'];
					}
					
					$categories = explode(",", $settingsClass->value('wine_category'));
					
					foreach ($cellar->binTypes() AS $binType) {
						$output = "<optgroup label=\"" . $binType . "\">";
						
						$filter = array(
							'cellar_uid' => $cellar->uid,
							'category' => $binType
						);
						foreach ($wineClass->allBins($filter) AS $binOption) {
							if ($binOption['uid'] == $wine->bin_uid) {
								$output .= "<option value=\"" . $binOption['uid'] . "\" selected>" . $binOption['name'] . "</option>";
							} else {
								$output .= "<option value=\"" . $binOption['uid'] . "\">" . $binOption['name'] . "</option>";
							}
						}
						
						$output .= "</optgroup>";
						
						
						echo $output;
					}
					?>
				</select>
			</div>
			<div class="col-4 mb-3">
				<label for="status" class="form-label">Status</label>
				<select class="form-select" id="status" name="status" required>
					<?php
					foreach (explode(",", $settingsClass->value('wine_status')) AS $wine_status) {
						if (isset($wine->status) && $wine->status == $wine_status) {
							echo "<option selected>" . $wine_status . "</option>";
						} else {
							echo "<option>" . $wine_status . "</option>";
						}
					}
					?>
				</select>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<div class="col-4 mb-3">
						<label for="category" class="form-label">Category</label>
						<select class="form-select" id="category" name="category" required>
							<?php
							foreach (explode(",", $settingsClass->value('wine_category')) AS $wine_category) {
								$selected = "";
								if ($pageType == "add") {
									if ($wine_category == $bin->category) {
										$selected = "selected";
									}
								} else {
									if ($wine_category == $wine->category) {
										$selected = "selected";
									}
								}
								
								echo "<option " . $selected . ">" . $wine_category . "</option>";
							}
							?>
						</select>
					</div>
					<div class="col-8 mb-3">
						<label for="name" class="form-label">Wine Name</label>
						<input type="text" class="form-control" id="name" name="name" value="<?php echo $wine->name; ?>" required>
					</div>
				</div>
				<div class="row">
					<div class="col mb-3">
						<label for="name" class="form-label">Supplier</label>
						<input type="text" class="form-control" id="supplier" name="supplier" value="<?php echo $wine->supplier; ?>" list="suppliers">
						<datalist id="suppliers">
							<?php
							foreach ($wineClass->listFromWines("supplier") AS $supplier) {
								echo "<option id=\"" . $supplier['supplier'] . "\" value=\"" . $supplier['supplier'] . "\"></option>";
							}
							?>
						</datalist>
					</div>
					<div class="col mb-3">
						<label for="name" class="form-label">Supplier Order Reference</label>
						<input type="text" class="form-control" id="supplier_ref" name="supplier_ref" value="<?php echo $wine->supplier_ref; ?>">
					</div>
				</div>
				<div class="row">
					<div class="col-4 mb-3">
						<label for="name" class="form-label">Country of Origin</label>
						<input type="text" class="form-control" id="country_of_origin" name="country_of_origin" list="codes-countries" value="<?php echo $wine->country_of_origin; ?>">
						<datalist id="codes-countries">
							<?php
							foreach ($wineClass->listFromWines('country_of_origin') AS $country_of_origin) {
								echo "<option id=\"" . $country_of_origin['country_of_origin'] . "\" value=\"" . $country_of_origin['country_of_origin'] . "\"></option>";
							}
							?>
						</datalist>
					</div>
					<div class="col-4 mb-3">
						<label for="name" class="form-label">Region of Origin</label>
						<input type="text" class="form-control" id="region_of_origin" name="region_of_origin" list="codes-regions" value="<?php echo $wine->region_of_origin; ?>">
						<datalist id="codes-regions">
							<?php
							foreach ($wineClass->listFromWines('region_of_origin') AS $region_of_origin) {
								echo "<option id=\"" . $region_of_origin['region_of_origin'] . "\" value=\"" . $region_of_origin['region_of_origin'] . "\"></option>";
							}
							?>
						</datalist>
					</div>
					<div class="col-4 mb-3">
						<label for="name" class="form-label">Grape</label>
						<input type="text" class="form-control" id="grape" name="grape" list="codes-grapes" value="<?php echo $wine->grape; ?>">
						<datalist id="codes-grapes">
							<?php
							foreach ($wineClass->listFromWines('grape') AS $grape) {
								echo "<option id=\"" . $grape['grape'] . "\" value=\"" . $grape['grape'] . "\"></option>";
							}
							?>
						</datalist>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>

<?php
if ($pageType == "add") {
?>
<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<label for="posting_date" class="form-label">Import Date</label>
				<input type="date" class="form-control" id="posting_date" name="posting_date" value="<?php echo date('Y-m-d'); ?>">
			</div>
		</div>
	</div>
</div>
<?php
}
?>
<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<?php
				if ($pageType == "edit") {
					$disabledCheck = " disabled";	
				}
				?>
				<input type="text" class="form-control" id="qty" name="qty" value="<?php echo $wine->currentQty(); ?>" <?php echo $disabledCheck; ?> required pattern="[0-9]*">
				<label for="qty" class="form-label">Bottles Qty.</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="price_purchase" name="price_purchase" value="<?php echo $wine->pricePerBottle("Purchase"); ?>" pattern="[0-9]+([\.,][0-9]+)?" required>
				<label for="price_purchase" class="form-label">Purchase Price <a href="#" data-bs-toggle="tooltip" data-bs-title="All prices are ex VAT"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#info-circle"></use></svg></a></label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<div class="col-sm">
						<input type="text" class="form-control" id="price_internal" name="price_internal" value="<?php echo $wine->pricePerBottle("Internal"); ?>" required pattern="[0-9]+([\.,][0-9]+)?">
					</div>
					<div class="col-sm">
						<input type="text" class="form-control" id="price_external" name="price_external" value="<?php echo $wine->pricePerBottle("External"); ?>" required pattern="[0-9]+([\.,][0-9]+)?">
					</div>
				</div>
				<label for="price_internal" class="form-label">Internal/External Price <a href="#" data-bs-toggle="tooltip" data-bs-title="All prices are ex VAT"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#info-circle"></use></svg></a></label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="vintage" name="vintage" value="<?php echo $wine->vintage; ?>" pattern="[0-9]*">
				<label for="price_purchvintagease" class="form-label">Vintage</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="code" name="code" list="codes-list" value="<?php echo $wine->code; ?>" required>
				<label for="code" class="form-label">Wine Code</label>
				<datalist id="codes-list">
					<?php
					foreach ($wineClass->listFromWines('code') AS $code) {
						echo "<option id=\"" . $code['code'] . "\" value=\"" . $code['code'] . "\"></option>";
					}
					?>
				</datalist>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xl-8">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Tasting Notes</h5>
				<textarea class="form-control" id="tasting" name="tasting" rows="3"><?php echo $wine->tasting; ?></textarea>
			</div>
		</div>
		
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Private Notes</h5>
				<textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $wine->notes; ?></textarea>
			</div>
		</div>
	</div>
	<div class="col-xl-4">
		<div class="card mb-3">
			<img src="<?php echo $wine->photographURL(); ?>" class="card-img-top" alt="...">
			<div class="card-body">
				<input class="form-control" type="file" id="photograph" name="photograph">
			</div>
		</div>
	</div>
	
	<button type="button" class="btn btn-lg btn-primary" data-wineuid="<?php echo $wine->uid; ?>" onClick="submitWine(this)">Save</button>
	
	<?php
	if ($pageType == "edit") {
		echo "<input type=\"hidden\" id=\"uid\" name=\"uid\" value=\"" . $wine->uid . "\">";
	}
	?>
</div>
</form>

<script>
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

function submitWine(button) {
	button.disabled = true;
	
	var safeToContinue = true;
	
	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	const forms = document.querySelectorAll('.needs-validation')
	
	// Loop over them and prevent submission
	Array.from(forms).forEach(form => {
		if (!form.checkValidity()) {
			event.preventDefault()
			event.stopPropagation()
			
			safeToContinue = false;
		}
		
		form.classList.add('was-validated')
	})
	if (!safeToContinue) {
		button.disabled = false;
		return false;
	}
	
	
	// Retrieve the data attributes
	var wine_uid = button.getAttribute('data-wineuid');
	var bin_uid = document.getElementById('bin_uid').value;
	
	// Create a new XMLHttpRequest object
	var xhr = new XMLHttpRequest();

	// Configure it: POST-request for the URL /submit-data
	xhr.open('POST', 'actions/wine_edit.php', true);

	// Get the form element
	var form = document.getElementById('wine_addEdit');
	
	// add additional form data to submission
	var formData = new FormData(wine_addEdit);
	
	// Send the request with the FormData object
	xhr.send(formData);

	// This will be called after the request is completed
	xhr.onload = function() {
		//alert(xhr.responseText);
		
		if (xhr.status != 200) { // analyze HTTP response status
			alert('Error ' + xhr.status + ': ' + xhr.statusText); // e.g. 404: Not Found
		} else {
			//alert('Success: ' + xhr.responseText); // response is the server
			location.href = "index.php?n=wine_bin&bin_uid=" + bin_uid;
		}
	};

	// This will be called in case of a network error
	xhr.onerror = function() {
		button.disabled = false;
		alert('Request failed');
	};
};

</script>