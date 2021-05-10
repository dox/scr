<?php
admin_gatekeeper();
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<?php
$logsClass->purge();
$logs = $logsClass->all();
$logsCategories = $logsClass->categories();
$logsDisplay = $settingsClass->value('logs_display');

$i=$logsDisplay;
do {
  foreach ($logsCategories AS $logCategory) {
    $lookupDate = date('Y-m-d', strtotime('-' . $i . ' days'));
    $logsByCategoryByDay = $logsClass->allByCategoryByDay($logCategory['category'], $lookupDate);

    $logsArray[$logCategory['category']][$lookupDate] = count($logsByCategoryByDay);
    $datesArray[$lookupDate] = "'" . $lookupDate . "'";
  }
  $i--;
} while ($i >= 0);

foreach ($logsArray AS $logType => $totalsArray) {
  $output  = "{";
  $output .= "label: '" . $logType . "',";
  $output .= "backgroundColor: '" . $logsClass->categoryColour($logType) . "',";
  $output .= "data: [" . implode(",", $totalsArray) . "],";
  $output .= "borderColor: ['" . $logsClass->categoryColour($logType) . "'],";
  $output .= "borderWidth: 1";
  $output .= "}";

  $graphData[] = $output;
}
?>
<?php
$title = "Logs";
$subtitle = "Admin/User logs for the last " . $logsDisplay . " days";

echo makeTitle($title, $subtitle);
?>

<canvas id="canvas2"></canvas>

<input type="text" id="logs_fiter_input" class="form-control mt-3 mb-3" onkeyup="tableFilter()" placeholder="Filter Logs...">

<div id="myTable" class="list-group">
  <?php
  echo $logsClass->displayTable();
  ?>
</div>

<script>
var barChartData = {
	labels: [<?php echo implode(", ", $datesArray); ?>],
	datasets: [<?php echo implode(",", $graphData); ?>]
};

window.onload = function() {
	var ctx = document.getElementById('canvas2').getContext('2d');
	window.myBar = new Chart(ctx, {
		type: 'bar',
		data: barChartData,
		options: {
			tooltips: {
				mode: 'index',
				intersect: false
			},
			responsive: true,
			scales: {
				xAxes: [{
					stacked: true,
			  }],
				yAxes: [{
					stacked: true,
          scaleLabel: {
  					display: true,
  					labelString: 'Total Logs'
  				}
				}]
			}
		}
	});
};

function tableFilter() {
  const input = document.getElementById("logs_fiter_input");
  const inputStr = input.value.toUpperCase();
  document.querySelectorAll('#logsTable tr:not(.header)').forEach((tr) => {
    const anyMatch = [...tr.children]
      .some(td => td.textContent.toUpperCase().includes(inputStr));
    if (anyMatch) tr.style.removeProperty('display');
    else tr.style.display = 'none';
  });
}
</script>
