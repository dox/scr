<?php
pageAccessCheck("wine");

$title = "Wine Management";
$subtitle = "BETA FEATURE!";
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#basket\"/></svg> Add Posting", "value" => "onclick=\"location.href='index.php?n=wine_posting'\"");

echo makeTitle($title, $subtitle, $icons);

$wineClass = new wineClass();
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
	</ol>
</nav>

<hr class="pb-3" />

<div class="row pb-3">
	<div class="col">
		<input type="text" id="wine_search" class="form-control form-control-lg" placeholder="Quick search" autocomplete="off" spellcheck="false" aria-describedby="wine_searchHelp">
		<ul id="wine_search_results" class="list-group"></ul>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup" akhi="<?php echo $wineClass->getAllWineBottlesTotal(); ?>">0</h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Bottles</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup" akhi="<?php echo count($wineClass->getAllWinesByFilter("type", "White")); ?>">0</h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">White Wines</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup" akhi="<?php echo count($wineClass->getAllWinesByFilter("type", "Red")); ?>">0</h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Red Wines</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup" akhi="<?php echo count($wineClass->getAllWinesByFilter("type", "Sparkling")); ?>">0</h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Sparkling Wines</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup" akhi="<?php echo count($wineClass->getAllWinesByFilter("type", "Port")); ?>">0</h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Port</h6>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title countup" akhi="<?php echo count($wineClass->getAllWinesByFilter("type", "Other")); ?>">0</h5>
				<h6 class="card-subtitle mb-2 text-body-secondary">Other</h6>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<?php
	foreach ($wineClass->getAllCellars() AS $cellar) {
		$cellar = new cellar($cellar['uid']);
		
		$output  = "<div class=\"col mb-3\">";
		$output .= "<div class=\"card shadow-sm\">";
		$output .= "<img src=\"" . $cellar->photograph . "\" class=\"card-img-top\" alt=\"Cellar photograph\">";
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

<div class="row">
	<div class="col-6">
		<h1>Recent Transactions</h1>
		<?php
		$wineTransactions = new wine_transactions();
		
		$output = "<ul class=\"list-group\">";
		foreach ($wineTransactions->getAllTransactions() AS $transaction) {
			
			
			$output .= "<li  class=\"list-group-item\">";
			$output .= "<a href=\"index.php?n=wine_wine&uid=" . $transaction['wine_uid'] . "\">";
			$output .= dateDisplay($transaction['date']) . " " . $transaction['wine_uid'] . " " . $transaction['cellar_uid'];
			$output .= $transaction['description'];
			$output .= "</a>";
			
			$output .= "</li>";
		}
		$output .= "</ul>";
		echo $output;
		
		?>
	</div>
	<div class="col-6">
		<h1>My Lists</h1>
		<?php
		$output = "<ul class=\"list-group\">";
		foreach ($wineClass->getAllLists() AS $list) {
			$list = new wine_list($list['uid']);
			
			$output .= "<li  class=\"list-group-item\">";
			$output .= "<svg width=\"1em\" height=\"1em\" class=\"text-muted\"><use xlink:href=\"img/icons.svg#heart-full\"/></svg> ";
			$output .= "<a href=\"index.php?n=wine_search&filter=list&value=" . $list->uid . "\">";
			$output .= $list->name_full();
			$output .= "</a>";
			
			$output .= "</li>";
		}
		$output .= "</ul>";
		echo $output;
		
		?>
	</div>
</div>


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
					var link = document.createElement("a");
					link.href = "index.php?n=wine_wine&uid=" + item.uid;
					link.textContent = item.name;
					listItem.appendChild(link);
					
					resultsDiv.appendChild(listItem);
				});
			}
		}
	};
	
	// Send the request
	xhr.send();
});
</script>


<script>
const counters = document.querySelectorAll('.countup');
const duration = 2000;  // Duration of the animation in milliseconds (2 seconds)
const frameRate = 30;   // Time between updates in milliseconds (~33 frames per second)
const totalFrames = duration / frameRate;  // Total number of frames/steps

counters.forEach(counter => {
   const value = +counter.getAttribute('akhi');  // Target value
   let currentFrame = 0;  // Keep track of the current frame
   const animate = () => {
	  currentFrame++;  // Increment frame count

	  // Calculate the progress as a percentage of total frames (0 to 1)
	  const progress = Math.min(currentFrame / totalFrames, 1);  // Clamp progress at 1

	  // Use progress to interpolate between 0 and the target value
	  const currentValue = Math.ceil(progress * value);
	  
	  counter.innerText = currentValue.toLocaleString();

	  // Continue animating until progress reaches 100%
	  if (progress < 1) {
		 setTimeout(animate, frameRate);
	  }
   };
   
   animate();
});
</script>