<?php
admin_gatekeeper();
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<?php
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

  $output  = "{";
  $output .= "label: '" . $logType . "',";
  $output .= "data: [" . implode(",", $totalsArray) . "],";
  $output .= "backgroundColor: ['" . $logsColour . "'],";
  $output .= "borderColor: ['" . $logsColour . "'],";
  $output .= "borderWidth: 1";
  $output .= "}";

  $graphData[] = $output;
}
?>

<div class="container">
  <div class="px-3 py-3 pt-md-5 pb-md-4 text-center">
    <h1 class="display-4">Logs</h1>
    <p class="lead">Admin/User logs for the last <?php echo $logsDisplay; ?> days</p>
  </div>
  <canvas id="logChart"></canvas>
  <div class="list-group">
    <?php
    foreach ($logs AS $log) {
      $output  = "<a href=\"#\" class=\"list-group-item list-group-item-action\">";
      $output .= "<div class=\"d-flex w-100 justify-content-between\">";
      $output .= "<h5 class=\"mb-1\">" . $log['username'] . " - " . $log['description'] . "</h5>";
      $output .= "<small class=\"text-muted\">" . date('Y-m-d H:i:s', strtotime($log['date'])) . "</small>";
      //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
      $output .= "</div>";
      //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
      $output .= "<small class=\"text-muted\"><span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span> " . $log['ip'] . "</small>";
      $output .= "</a>";

      echo $output;
    }
    ?>
  </div>
</div>

<script>
var ctx = document.getElementById('logChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php echo implode(", ", $datesArray); ?>],
        datasets: [
          <?php echo implode(",", $graphData); ?>
        ]
    },
    options: {
      scales: {
        xAxes: [{
          //stacked: true
				}],
        yAxes: [{
          //stacked: true,
					scaleLabel: {
						display: true,
						labelString: 'Total Logs'
					},
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
});
</script>
