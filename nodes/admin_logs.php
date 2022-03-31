<?php
admin_gatekeeper();
?>
<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>

<?php
$logsClass->purge();
$logs = $logsClass->all();
$logsByDay = $logsClass->byDay("logon");
?>
<?php
$title = "Logs";
$subtitle = "Admin/User logs for the last " . $logsDisplay . " days";

echo makeTitle($title, $subtitle);
?>

<div class="ct-chart-logs"></div>

<input type="text" id="logs_fiter_input" class="form-control mt-3 mb-3" onkeyup="tableFilter()" placeholder="Filter Logs...">

<div id="myTable" class="list-group">
  <?php
  if (isset($_GET['p'])){
    $offset = $_GET['p'];
  } else {
    $offset = 0;
  }
  echo $logsClass->displayTable($offset);
  ?>
</div>

<?php
echo $logsClass->paginationDisplay(count($logs), $_GET['p']);

?>


<script>
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