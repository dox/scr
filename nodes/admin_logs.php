<?php
admin_gatekeeper();
?>
<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>

<?php
$logsClass->purge();
$logsByDay = $logsClass->byDay("logon");

$title = "Logs";
$subtitle = "Admin/User logs for the last " . $logsDisplay . " days";

echo makeTitle($title, $subtitle);
?>

<div class="ct-chart-logs"></div>

<form method="post" id="search" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
<div class="input-group my-3">
  <input type="text" class="form-control" id="logs_search" name="logs_search" placeholder="e.g. '[memberUID:137]'" aria-label="Search Logs" aria-describedby="button-addon2" value="<?php if (isset($_POST['logs_search'])) { echo $_POST['logs_search']; } ?>">
  <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
</div>
</form>

<div id="myTable" class="list-group">
  <?php
  if (isset($_GET['p'])){
    $offset = $_GET['p'];
  } else {
    $offset = 0;
  }
  
  echo $logsClass->displayTable($offset, $_POST['logs_search']);
  ?>
</div>

<?php
echo $logsClass->paginationDisplay(count($logsClass->all()), $_GET['p']);
?>

<script>
var data = {
  // A labels array that can contain any sort of values
  labels: ['<?php echo implode("','", array_keys($logsByDay)); ?>'],
  // Our series array that contains series objects or in this case series data arrays
  series: [
    [<?php echo implode(",", $logsByDay); ?>]
  ]
};

new Chartist.Bar('.ct-chart-logs', data, {
  low: 0,
  axisX: {
    // On the x-axis start means top and end means bottom
    position: 'end',
    showGrid: true
  },
  axisY: {
    // On the y-axis start means left and end means right
    showGrid: false,
    showLabel: true
  }
});
</script>