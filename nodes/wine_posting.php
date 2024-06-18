<?php
pageAccessCheck("wine");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wine = new wine($cleanUID);
$cellar = new cellar($wine->cellar_uid);

$title = "Wine Posting";
//$subtitle = $wine->grape . ", " . $wine->country_of_origin;
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add To List", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#favListModal\"");
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Edit Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=edit&uid=" . $wine->uid . "'\"");
echo makeTitle($title, $subtitle, $icons);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Posting</li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row">
	<div class="col">
		<input type="text" id="wine_search" class="form-control form-control-lg" placeholder="Quick search" autocomplete="off" spellcheck="false" aria-describedby="wine_searchHelp">
		<ul id="wine_search_results" class="list-group"></ul>
		
	</div>
</div>
<hr class="pb-3" />

<form method="post" id="termUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
<?php
if (isset($_POST['uid'])) {
	printArray($_POST);
	$transaction = new wine_transactions();
	
	$transaction->create($_POST);
}

?>

<div class="row">
	<div class="col-xl-8">
		<label for="selected-items" class="form-label">Wines</label>
		<div id="selected-items"></div>
		
		<button type="submit" class="btn btn-primary">Save</button>
		
	</div>
	<div class="col-xl-4">
		<div class="mb-3">
			<label for="reference" class="form-label">Checkout Reference</label>
			<input type="text" class="form-control" id="reference" name="reference">
		</div>
		
		<div class="mb-3">
			<label for="customer" class="form-label">Customer Name</label>
			<input type="text" class="form-control" id="customer" name="customer">
		</div>
		
		<div class="mb-3">
			<label for="notes" class="form-label">Notes</label>
			<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
		</div>
	</div>
</div>
</form>

<script>
document.getElementById('wine_search').addEventListener('keyup', function() {
	let query = this.value;
	let resultsDiv = document.getElementById('wine_search_results');

	// Clear the results if the input is empty
	if (query.trim() === '') {
		resultsDiv.innerHTML = '';
		return;
	}
	
	// Create a new XMLHttpRequest object
	let xhr = new XMLHttpRequest();
	
	// Configure it: GET-request for the URL with the query
	xhr.open('GET', 'actions/wine_search.php?q=' + encodeURIComponent(query), true);
	
	// Set up the callback function
	xhr.onload = function() {
		if (xhr.status === 200) {
			// Parse JSON response
			let response = JSON.parse(xhr.responseText);
			
			// Display the results
			resultsDiv.innerHTML = '';

			if (response.data.length === 0) {
				let listItem = document.createElement('li');
				listItem.className = "list-group-item";
				listItem.textContent = 'No results found';
				
				resultsDiv.appendChild(listItem);
			} else {
				response.data.forEach(function(item) {
					
					let listItem = document.createElement('li');
					listItem.className = "list-group-item";
					listItem.textContent = `${item.uid}: ${item.name}`;
					listItem.style.cursor = 'pointer';
					listItem.addEventListener('click', function() {
						fetchDetails(item.uid);
						resultsDiv.innerHTML = ''; // Clear results on click
						document.getElementById('wine_search').value = '';
					});
					resultsDiv.appendChild(listItem);
				});
			}
		}
	};
	
	// Send the request
	xhr.send();
});

let selectedUIDS = [];

function fetchDetails(uid) {
	selectedUIDS.push(uid);
	
	let xhr = new XMLHttpRequest();
	xhr.open('GET', 'nodes/widgets/_wineCheckout.php?uids=' + encodeURIComponent(selectedUIDS.join(',')), true);
	xhr.onload = function() {
		if (xhr.status === 200) {
			document.getElementById('selected-items').innerHTML = xhr.responseText;
		}
	};
	xhr.send();
}
</script>


