<?php
admin_gatekeeper();
?>

<?php
$logsClass->purge();
$logsByDay = $logsClass->byDay("logon");

$title = "Logs";
$subtitle = "Admin/User logs for the last " . $logsDisplay . " days";

echo makeTitle($title, $subtitle);

if (isset($_GET['p'])){
  $offset = filter_var($_GET['p'], FILTER_SANITIZE_NUMBER_INT);
} else {
  $offset = 0;
}

if (isset($_POST['logs_search'])) {
  $search = filter_var($_POST['logs_search'], FILTER_SANITIZE_STRING);
} else {
  $search = null;
}

//filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);

$logsAll = $logsClass->paginatedResultsTotal($search);
$logs = $logsClass->paginatedResults($offset, $search);
?>

<div id="chart-logs"></div>

<form method="post" id="search" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
<div class="input-group my-3">
  <input type="text" class="form-control" id="logs_search" name="logs_search" placeholder="e.g. '[memberUID:137]'" aria-label="Search Logs" aria-describedby="button-addon2" value="<?php if (isset($search)) { echo $search; } ?>">
  <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
</div>
</form>



<div id="myTable" class="list-group">
  <?php
  echo $logsClass->displayTable($logs);
  ?>
</div>

<?php
echo $logsClass->paginationDisplay($logsAll, $offset);


foreach (array_keys($logsByDay) AS $label) {
  $labelsArray[] = date('M-d', strtotime($label));
}
?>

<script>
var options = {
  chart: {
    type: 'bar',
    height: '300px'
  },
  series: [{
    name: 'Logs',
    data: [<?php echo implode(",", $logsByDay); ?>]
  }],
  xaxis: {
    categories: ['<?php echo implode("','", $labelsArray); ?>']
  }
}

var chart = new ApexCharts(document.querySelector("#chart-logs"), options);

chart.render();
</script>