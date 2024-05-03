<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/tomickigrzegorz/autocomplete@2.0.1/dist/css/autocomplete.min.css"/>
<script src="https://cdn.jsdelivr.net/gh/tomickigrzegorz/autocomplete@2.0.1/dist/js/autocomplete.min.js"></script>


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

<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-body">
				<div class="auto-search-wrapper">
				  <input type="text" id="wine_search" class="form-control-lg" placeholder="Quick search" autocomplete="off" spellcheck="false" aria-describedby="wine_searchHelp">
				</div>
			</div>
		</div>
	</div>
</div>

	
  
  
<div class="row">
	<h1>Recent Additions?</h1>
	<?php printArray($wines); ?>
	
	<h1>Recent Changes?</h1>
	<?php printArray($wines); ?>
</div>


<script>
new Autocomplete("wine_search", {
	onSearch: ({ currentValue }) => {
		const api = `actions/wine_search.php?string=${encodeURI(currentValue)}`;
		
		return new Promise((resolve) => {
			fetch(api)
			.then((response) => response.json())
			.then((data) => {
				resolve(data);
			})
			.catch((error) => {
				console.error(error);
			});
		});
	},
	onResults: ({ matches }) =>
	matches.map((el) => `<li>${el.name}</li>`).join(""),
	onSubmit: ({ index, element, object }) => {
		const { name, uid } = object;
		
		window.location = "index.php?n=wine_wine&uid=" + uid;
	},
});
</script>