<?php
pageAccessCheck("wine");

$pageType = filter_var($_GET['edit'], FILTER_SANITIZE_STRING);

if ($pageType == "add") {
	$cleanCellarUID = filter_var($_GET['cellar_uid'], FILTER_SANITIZE_NUMBER_INT);
	$wine = new wine();
	
	$title = "Add New Wine";
	//$subtitle = $wine->grape . ", " . $wine->country_of_origin;
	//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
	echo makeTitle($title, $subtitle, $icons);
	
	$cellar = new cellar($cleanCellarUID);
	
} else {
	$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);
	$wine = new wine($cleanUID);
	$cellar = new cellar($wine->cellar_uid);
	
	$title = "Edit " . $wine->name;
	$subtitle = $wine->grape . ", " . $wine->country_of_origin;
	//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
	echo makeTitle($title, $subtitle, $icons);
}
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item"><a href="index.php?n=wine_cellar&uid=<?php echo $cellar->uid?>"><?php echo $cellar->name; ?></a></li>
		<?php
		if ($pageType == "edit") {
			echo "<li class=\"breadcrumb-item\"><a href=\"index.php?n=wine_wine&uid=" . $wine->uid . "\">" . $wine->bin . "</a></li>";
		}
		?>
		<li class="breadcrumb-item active"><?php echo ucwords($pageType); ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<form class="row g-3 needs-validation" id="wine_addEdit" novalidate>

<div class="alert alert-warning text-center" role="alert">
	<input type="hidden" value="0" id="bond" name="bond">
	<input class="form-check-input" type="checkbox" <?php if ($wine->bond ==1) { echo "checked"; } ?> id="bond" name="bond" value="1"> WINE IN-BOND
</div>

<div class="row">
	<div class="card mb-3">
		<div class="card-body">
			<div class="row">
				<div class="col-4">
					<label for="name" class="form-label">Bin</label>
					<input type="text" class="form-control" id="bin" name="bin" value="<?php echo $wine->bin; ?>" required>
				</div>
				<div class="col-8">
					<label for="name" class="form-label">Wine Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?php echo $wine->name; ?>" required>
				</div>
			</div>
			<div class="mb-3">
				<label for="name" class="form-label">Grape</label>
				<input type="text" class="form-control" id="grape" name="grape" list="codes-grapes" value="<?php echo $wine->grape; ?>" required>
				<datalist id="codes-grapes">
					<?php
					foreach ($wine->stats_winesByGrape() AS $grape => $value) {
						echo "<option id=\"" . $grape . "\" value=\"" . $grape . "\"></option>";
					}
					?>
				</datalist>
			</div>
			<div class="mb-3">
				<label for="name" class="form-label">Country of Origin</label>
				<input type="text" class="form-control" id="country_of_origin" name="country_of_origin" list="codes-countries" value="<?php echo $wine->country_of_origin; ?>" required>
				<datalist id="codes-countries">
					<?php
					foreach ($wine->stats_winesByCountry() AS $country_of_origin => $value) {
						echo "<option id=\"" . $country_of_origin . "\" value=\"" . $country_of_origin . "\"></option>";
					}
					?>
				</datalist>
			</div>
		</div>
	</div>
	
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="qty" name="qty" value="<?php echo $wine->qty; ?>" required>
				<label for="qty" class="form-label">Bottles Qty.</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="price_purchase" name="price_purchase" value="<?php echo $wine->pricePerBottle("Purchase"); ?>">
				<label for="price_purchase" class="form-label">Purchase Price</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<div class="col-sm">
						<input type="text" class="form-control" id="price_internal" name="price_internal" value="<?php echo $wine->pricePerBottle("Internal"); ?>">
					</div>
					<div class="col-sm">
						<input type="text" class="form-control" id="price_external" name="price_external" value="<?php echo $wine->pricePerBottle("External"); ?>">
					</div>
				</div>
				<label for="price_internal" class="form-label">Internal/External Price</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="vintage" name="vintage" value="<?php echo $wine->vintage; ?>">
				<label for="price_purchvintagease" class="form-label">Vintage</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<input type="text" class="form-control" id="code" name="code" list="codes-list" value="<?php echo $wine->code; ?>">
				<label for="code" class="form-label">Wine Code</label>
				<datalist id="codes-list">
					<?php
					foreach ($wine->stats_winesByCode() AS $code => $value) {
						echo "<option id=\"" . $code . "\" value=\"" . $code . "\"></option>";
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
			<img src="https://as1.ftcdn.net/v2/jpg/00/91/94/40/1000_F_91944058_7lRk2ok81t7vS6JsVP81BL9Jp6VajrSh.jpg" class="card-img-top" alt="...">
			<div class="card-body">
				Wine Origin
			</div>
		</div>
	</div>
	
	<button type="button" class="btn btn-lg btn-primary" data-wineuid="<?php echo $wine->uid; ?>" onClick="submitWine(this)">Save</button>
	
	<input type="hidden" id="cellar_uid" name="cellar_uid" value="<?php echo $cellar->uid; ?>">
</div>
</form>

<script>
function submitWine(button) {
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
		return false;
	}
	
	
	// Retrieve the data attributes
	var wine_uid = button.getAttribute('data-wineuid');
	
	// Prepare the data to be sent
	let params = [];
	
	if (wine_uid) {
		params.push('uid=' + encodeURIComponent(<?php echo $wine->uid; ?>));
	} else {
		
	}
	
	// Create a new XMLHttpRequest object
	var xhr = new XMLHttpRequest();

	// Configure it: POST-request for the URL /submit-data
	xhr.open('POST', 'actions/wine_edit.php', true);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

	
	// add additional form data to submission
	var formData = new FormData(wine_addEdit);
	
	// iterate through the name-value pairs
	for (var pair of formData.entries()) {
		params.push(pair[0] + '=' + encodeURIComponent(pair[1]));
	}
	
	let text = params.join("&");

	
	// Send the request over the network
	xhr.send(text);

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