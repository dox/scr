<?php
pageAccessCheck("wine");

$cleanUID = filter_var($_GET['uid'], FILTER_SANITIZE_NUMBER_INT);

$cellar = new cellar($cleanUID);

$title = $cellar->name . " Wine Cellar";
$subtitle = "BETA FEATURE!";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#plus-circle\"/></svg> Add Wine", "value" => "onclick=\"location.href='index.php?n=wine_edit&edit=add&cellar_uid=" . $cellar->uid . "'\"");
echo makeTitle($title, $subtitle, $icons);
?>

<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php?n=wine_index">Wine</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?php echo $cellar->name; ?></li>
	</ol>
</nav>

<hr class="pb-3" />

<div id="chart"></div>

<?php
echo "<p>Total purchase value: " . currencyDisplay($cellar->totalPurchaseValue()) . "</p>";
?>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
foreach ($cellar->getBins() AS $bin) {
	$wine = new wine($bin['uid']);
	
	echo $wine->binCard();
}
	?>
</div>

<?php
$wineClass = new wineClass;
foreach ($wineClass->stats_winesByGrape($cellar->uid
) AS $grape => $count) {
	$data[] = "{ x: '" . $grape . "', y: " . $count . "}";
}
?>
<script>
var options = {
  series: [
  {
	data: [<?php echo implode(",", $data); ?>]
  }
],
  legend: {
  show: false
},
chart: {
  height: 350,
  type: 'treemap',
  toolbar: {
	  show: false
  },
  events: {
	  click(event, chartContext, opts) {
		  var clicked_grape = opts.config.series[opts.seriesIndex].data[opts.dataPointIndex].x;
		  window.location.href = 'index.php?n=wine_search&filter=grape&value=' + clicked_grape;
	  }
  }
}
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>
