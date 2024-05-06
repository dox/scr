<?php
$title = "Wine Management";
$subtitle = "BETA FEATURE!";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#journal-text\"/></svg> Add Order", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteTermModal\"");

echo makeTitle($title, $subtitle, $icons);

$wineClass = new wineClass();
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row">
	<?php
	foreach ($wineClass->getAllCellars() AS $cellar) {
		$cellar = new cellar($cellar['uid']);
		
		$output  = "<div class=\"col mb-3\">";
		$output .= "<div class=\"card shadow-sm\">";
		$output .= "<img src=\"img/cards/formal.png\" class=\"card-img-top\" alt=\"Cellar photograph\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<p class=\"card-text\">" . $cellar->name . "</p>";
		$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
		$output .= "<div class=\"btn-group\">";
		$output .= "<a href=\"index.php?n=wine_cellar&uid=" . $cellar->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
		$output .= "<a href=\"index.php?n=wine_cellar&uid=" . $cellar->uid . "\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">Edit</a>";
		$output .= "</div>";
		$output .= "<small class=\"text-body-secondary\">";
		$output .= count($cellar->getBins()) . autoPluralise(" bin", " bins", count($cellar->getBins()));
		$output .= " / ";
		$output .= $cellar->getAllWineBottlesTotal() . autoPluralise(" wine", " wines", $cellar->getAllWineBottlesTotal());
		$output .= "</small>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		echo $output;
	}
	?>
</div>

<style>
	#outputList {
		z-index: 1;
		max-height: 200px;
		overflow-y: auto;
	}
</style>

<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-body">
				<input type="text" id="wine_search" class="form-control form-control-lg" placeholder="Quick search" autocomplete="off" spellcheck="false" aria-describedby="wine_searchHelp" oninput="searchData()">
			</div>
			<div id="outputContainer" class="bg-body">Search results...</div>
		</div>
	</div>
</div>


	
  
  
<div class="row">
	<h1>Lists</h1>
	<?php printArray($wineClass->getAllPublicLists()); ?>
	
	<h2>My Lists</h2>
	<?php printArray($wineClass->getAllMemberLists(1)); ?>

	<h1>Recent Additions?</h1>
	<?php printArray($wines); ?>
	
	<h1>Recent Changes?</h1>
	<?php printArray($wines); ?>
</div>





<script>
	var jsonData; // Variable to store the loaded JSON data

	function loadData() {
		// Load data from JSON file
		var xhr = new XMLHttpRequest();
		xhr.open("GET", "actions/wine_search.php", true);
		xhr.setRequestHeader("Content-Type", "application/json");
		xhr.send();

		// Process loaded data
		xhr.onload = function() {
			if (xhr.status === 200) {
				jsonData = JSON.parse(xhr.responseText);
				searchData(jsonData);
			}
		};
	}

	
	
	function displayData(data) {
            var outputList = document.createElement("ul");
            outputList.id = "outputList";
            outputList.innerHTML = ""; // Clear previous data

            // Loop through each item in the JSON array
            data.forEach(function(item) {
                if (item && item.name) { // Check for null or empty values
                    var listItem = document.createElement("li");
                    var link = document.createElement("a");
					link.href = "index.php?n=wine_wine&uid=" + item.uid; // Replace "https://example.com" with your actual URL
                    link.textContent = item.name;
                    listItem.appendChild(link);
                    outputList.appendChild(listItem);
                }
            });

            var outputContainer = document.getElementById("outputContainer");
            outputContainer.innerHTML = "";
            outputContainer.appendChild(outputList);
            outputContainer.style.display = data.length > 0 ? "block" : "none";
        }

	function searchData() {
		var searchInput = document.getElementById("wine_search").value.trim().toLowerCase();
		var filteredData = [];
		
		if (jsonData) {
			if (searchInput !== "" && jsonData.length > 0) {
				filteredData = jsonData.filter(function(item) {
					return item.name && item.name.toLowerCase().includes(searchInput); // Check for null or empty values
				});
			} else {
				filteredData = jsonData; // Show all data if search input is empty or if JSON data is empty
			}
		}
	
		displayData(filteredData);
	}
	
	// Trigger search on each key press in the search input box
	document.getElementById("wine_search").addEventListener("keyup", function() {
		loadData();
	});

	// Load data when the page is loaded
	//window.onload = loadData;
</script>

