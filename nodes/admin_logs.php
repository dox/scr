<?php
admin_gatekeeper();
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.1/chart.min.js" integrity="sha512-5vwN8yor2fFT9pgPS9p9R7AszYaNn0LkQElTXIsZFCL7ucT8zDCAqlQXDdaqgA1mZP47hdvztBMsIoFxq/FyyQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
  $output .= "data: [" . implode(",", $totalsArray) . "],";
  $output .= "backgroundColor: '" . $logsClass->categoryColour($logType) . "',";
  $output .= "borderColor: ['" . $logsClass->categoryColour($logType, "0.6") . "'],";
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
<canvas id="logsChart" width="400" height="150"></canvas>

<input type="text" id="logs_fiter_input" class="form-control mt-3 mb-3" onkeyup="tableFilter()" placeholder="Filter Logs...">

<div id="myTable" class="list-group">
  <?php
  echo $logsClass->displayTable();
  ?>
</div>


<script>
var ctx = document.getElementById('logsChart');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(", ", $datesArray); ?>],
        datasets: [<?php echo implode(",", $graphData); ?>]
    },
    options: {
        scales: {
          x: {
                stacked: true
            },
            y: {
              stacked: true,
                beginAtZero: true
            }
        }
    }
});

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
