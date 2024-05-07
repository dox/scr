<?php
pageAccessCheck("reports");

$reportsClass = new reports();
$reports = $reportsClass->all();
?>

<?php
$title = "Reports";
$subtitle = "Some text here</a>.";
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#calendar-plus\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons);

echo $reportsClass->displayTable();
?>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="termForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New Term Date</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="name">Term Name</label>
            <input type="text" class="form-control" name="name" id="name" aria-describedby="termNameHelp">
            <small id="nameHelp" class="form-text text-muted">Something like 'Trinity 2020'</small>
          </div>

          <div class="form-group">
            <label for="date_start">Term Start Date</label>
            <input type="text" class="form-control" name="date_start" id="date_start" aria-describedby="termStartDate">
            <small id="date_startHelp" class="form-text text-muted">Sunday of 1st week</small>
          </div>

          <div class="form-group">
            <label for="date_end">Term End Date</label>
            <input type="text" class="form-control" name="date_end" id="date_end" aria-describedby="termEndDate">
            <small id="date_endHelp" class="form-text text-muted">Saturday of 8th week</small>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><svg width="1em" height="1em"><use xlink:href="img/icons.svg#calendar-plus"/></svg> Add Term</button>
      </div>
      </form>
    </div>
  </div>
</div>


<script>
function dismiss(el){
  document.getElementById(el).parentNode.style.display='none';
};

var fp = flatpickr("#date_start", {
  dateFormat: "Y-m-d",
  enable: [
        function(date) {
            // return true to disable
            return (date.getDay() === 0);

        }
    ]
})

var fp = flatpickr("#date_end", {
  dateFormat: "Y-m-d",
  enable: [
        function(date) {
            // return true to disable
            return (date.getDay() === 6);

        }
    ]
})
</script>
