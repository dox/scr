<?php
admin_gatekeeper();
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<?php
$logsClass->purge();
$logs = $logsClass->all();
$logsTypes = $logsClass->types();
$logsDisplay = $settingsClass->value('logs_display');

$i=$logsDisplay;
do {
  foreach ($logsTypes AS $logType) {
    $lookupDate = date('Y-m-d', strtotime('-' . $i . ' days'));
    $logsByTypeByDay = $logsClass->allByTypeByDay($logType['type'], $lookupDate);

    $logsArray[$logType['type']][$lookupDate] = count($logsByTypeByDay);
    $datesArray[$lookupDate] = "'" . $lookupDate . "'";
  }
  $i--;
} while ($i >= 0);

foreach ($logsArray AS $logType => $totalsArray) {
  $logsColour = $settingsClass->value('logs_colour_' . $logType);

  if (!isset($logsColour)) {
    $logsColour = "rgba(" . rand(0,255) . ", " . rand(0,255) . ", " . rand(0,255) . ", 0.2)";
  }

  $output  = "{";
  $output .= "label: '" . $logType . "',";
  $output .= "backgroundColor: '" . $logsColour . "',";
  $output .= "data: [" . implode(",", $totalsArray) . "]";
  //$output .= "borderColor: ['" . $logsColour . "'],";
  //$output .= "borderWidth: 1";
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

<div class="list-group">
  <?php
  foreach ($logs AS $log) {
    $output  = "<a href=\"#\" class=\"list-group-item list-group-item-action\">";
    $output .= "<div class=\"d-flex w-100 justify-content-between\">";
    $output .= "<h5 class=\"mb-1\">" . $log['username'] . " - " . $log['description'] . "</h5>";
    $output .= "<small class=\"text-muted\">" . dateDisplay($log['date']) . " " . date('H:i:s', strtotime($log['date'])) . "</small>";
    //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
    $output .= "</div>";
    //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
    $output .= "<small class=\"text-muted\"><span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span> " . $log['ip'] . "</small>";
    $output .= "</a>";

    echo $output;
  }
  ?>
</div>

<script>
var barChartData = {
			labels: [<?php echo implode(", ", $datesArray); ?>],
			datasets: [
        <?php echo implode(",", $graphData); ?>]

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

</script>
