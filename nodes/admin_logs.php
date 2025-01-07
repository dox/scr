<?php
pageAccessCheck("logs");
?>

<?php
$logsClass->purge();
$logsByDay = $logsClass->byDay("logon");
$maximumLogsDisplay = $settingsClass->value('logs_display');
$maximumLogsKeep = $settingsClass->value('logs_retention');

$title = "Logs";
$subtitle = "Admin/User logs for the last " . $maximumLogsDisplay . " days <i>(" . $maximumLogsKeep . " days available)</i>";

echo makeTitle($title, $subtitle, false, true);

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

$logs = $logsClass->paginatedResults($offset, $search);
?>

<div id="chart-logs"></div>

<?php
$totalBookings = $db->query("SELECT COUNT(*) AS totalBookings FROM bookings")->fetchArray();
$totalMeals = $db->query("SELECT COUNT(*) AS totalMeals FROM meals")->fetchArray();
$oldestMeal = $db->query("SELECT * FROM meals ORDER BY date_meal ASC LIMIT 1")->fetchArray();
?>
<div class="row">
  <div class="col-sm-6 mb-3 mb-sm-0">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?php echo number_format($totalBookings['totalBookings']); ?></h5>
        <p class="card-text">Total Bookings</p>
      </div>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?php echo number_format($totalMeals['totalMeals']); ?></h5>
        <p class="card-text">Total Meals <i>(since: <?php echo dateDisplay($oldestMeal['date_meal'], true); ?>)</i></p>
      </div>
    </div>
  </div>
</div>

<form method="post" id="search" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
<div class="input-group my-3">
  <input type="text" class="form-control" id="logs_search" name="logs_search" placeholder="e.g. '[memberUID:137]'" aria-label="Search Logs" aria-describedby="button-addon2" value="<?php if (isset($search)) { echo $search; } ?>">
  <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
</div>
</form>

<?php
$paginatedResults = $logsClass->paginatedLogs($logs);
echo $logsClass->displayTable($paginatedResults['currentPageItems']);


echo $logsClass->paginationResults();



foreach (array_keys($logsByDay) AS $label) {
  $labelsArray[] = date('M-d', strtotime($label));
}
?>

<script>
var options = {
  chart: {
    id: 'logs-daily',
    type: 'bar',
    height: '300px'
  },
  series: [{
    name: 'Logs',
    data: [<?php echo implode(",", $logsByDay); ?>]
  }],
  dataLabels: {
    enabled: false
  },
  xaxis: {
    categories: ['<?php echo implode("','", $labelsArray); ?>']
  }
}

var chart = new ApexCharts(document.querySelector("#chart-logs"), options);

chart.render();
</script>