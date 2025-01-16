<?php
pageAccessCheck("wine");

$transactionUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$wineClass = new wineClass();
$transaction = new transaction($transactionUID);
$wine = new wine($transaction->wine_uid);
$bin = new bin($wine->bin_uid);
$cellar = new cellar($transaction->cellar_uid);


$title = "Multi-Transaction";
$subtitle = $transaction->name;
if (!empty($transaction->description)) {
	$subtitle .= " <i>(" . $transaction->description . ")</i>";
}

echo makeTitle($title, $subtitle, $icons, true);
?>



<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active">Multi-Transaction</li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row">
	<div class="col">
		<div class="input-group mb-3">
		  <input type="text" class="form-control form-control-lg" id="wine_search" placeholder="Quick search all cellars" autocomplete="off" spellcheck="false">
		  <span class="input-group-text" id="basic-addon2"><input class="form-check-input mt-0 me-2" type="checkbox" id="wine_search_include_closed" value="true">include closed</span>
		</div>
		<ul id="wine_search_results" class="list-group"></ul>
	</div>
</div>
<hr class="pb-3" />

<form method="post" id="wines" class="needs-validation" novalidate>
<?php
if (isset($_POST['wine_uid'])) {
	printArray($_POST);
}

?>

<div class="row">
	<div class="col-xl-8">
		<label for="selected-items" class="form-label">Wines</label>
		<div id="selected-items"></div>
		
		<button type="submit" class="btn btn-primary" onClick="submitAllForms()">Save</button>
		
	</div>
	<div class="col-xl-4">
		<div class="col-12">
			<div class="mb-3">
			  <label for="name" class="form-label">Name</label>
			  <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Formal Hall">
			</div>
		</div>
		
		<div class="col-12">
			<div class="mb-3">
			  <label for="date_posted" class="form-label">Posting Date</label>
			  <div class="input-group">
					<span class="input-group-text" id="date_posted-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
					<input type="date" class="form-control" name="date_posted" id="date_posted"  value="<?php echo date('Y-m-d'); ?>" aria-describedby="date_posted">
				</div>
			</div>
		</div>
		
		<div class="mb-3">
			<label for="transaction_description" class="form-label">Description</label>
			<textarea class="form-control" id="description" name="description" rows="3"></textarea>
		</div>
	</div>
</div>
</form>


<script>
document.getElementById('wine_search').addEventListener('keyup', function() {
	let query = this.value;
	let resultsDiv = document.getElementById('wine_search_results');
	
	let include_closed = document.getElementById('wine_search_include_closed').checked;
	
	// Clear the results if the input is empty
	if (query.trim() === '') {
		resultsDiv.innerHTML = '';
		return;
	}
	
	// Create a new XMLHttpRequest object
	let xhr = new XMLHttpRequest();
	
	// Configure it: GET-request for the URL with the query
	xhr.open('GET', 'actions/wine_search.php?q=' + encodeURIComponent(query) + '&include=' + include_closed, true);
	
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
					listItem.textContent = item.name;
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

function fetchDetails(uid) {
	
	let xhr = new XMLHttpRequest();
	xhr.open('GET', 'nodes/widgets/_wineCheckout.php?uid=' + encodeURIComponent(uid), true);
	xhr.onload = function() {
		if (xhr.status === 200) {
			
			// Create a container for the new content
			let container = document.createElement('div');
			container.innerHTML = xhr.responseText;
			
			// Append the new content container to the selected-items element
			document.getElementById('selected-items').appendChild(container);
		}
	};
	xhr.send();
}
</script>

<script>
function submitAllForms() {
	// Create a new XMLHttpRequest object
	var xhr = new XMLHttpRequest();

	// Configure the request: POST-request for the remote URL
	xhr.open('POST', 'actions/wine_transactionCreate.php', true);

	// Create a new FormData object
	var formData = new FormData();

	// Collect all form data from the forms on the page
	var forms = document.querySelectorAll('form');
	forms.forEach(form => {
		// Append all form elements to the FormData object
		new FormData(form).forEach((value, key) => {
			if (value.trim() !== '') {
				// Handle duplicate keys by appending values to an array-like structure
				if (formData.has(key)) {
					formData.append(key, value);
				} else {
					formData.set(key, value);
				}
			}
		});
	});

	// Debugging: Log the FormData keys and values
	console.log('Data being sent:');
	for (var pair of formData.entries()) {
		console.log(pair[0] + ':', pair[1]);
	}

	// Send the FormData object
	xhr.send(formData);

	// Add event listeners for the response
	xhr.onload = function () {
		if (xhr.status != 200) { // Analyze HTTP response status
			alert('Error ' + xhr.status + ': ' + xhr.statusText); // e.g. 404: Not Found
		} else {
			alert('Success: ' + xhr.responseText); // response from the server
		}
	};

	// Handle network errors
	xhr.onerror = function () {
		alert('Request failed. Please check your network connection.');
	};

	// Prevent default behavior (if this function is tied to a button click)
	return false;
}
</script>