<link rel="stylesheet" href="css/flatpickr.min.css">
<script src="js/flatpickr.js"></script>

<?php
admin_gatekeeper();

$termsClass = new terms();

if (isset($_POST['name'])) {
  $termsClass->create($_POST);
  echo $settingsClass->alert("success", $_POST['name'], " term created");
}

if (isset($_GET['termDELETE'])) {
 echo "DELETE!";
 $termClass = new term($_GET['termDELETE']);
 $termClass->delete();
}

$terms = $termsClass->all();
?>

<?php
$title = "Terms";
$subtitle = "Term dates from <a href=\"https://www.ox.ac.uk/about/facts-and-figures/dates-of-term\">Oxford's website</a>.";
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#calendar-plus\"/></svg> Add New", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"");

echo makeTitle($title, $subtitle, $icons);
?>

<div class="list-group">
  <?php
  foreach ($terms AS $term) {
    $termObject = new term($term['uid']);

    if ($termObject->isCurrentTerm()) {
      $class = " list-group-item-primary";
    } else {
      $class = "";
    }

    $output  = "<a href=\"index.php?n=admin_term&termUID=" . $termObject->uid . "\" class=\"list-group-item " . $class . " list-group-item-action\">";
    $output .= "<div class=\"d-flex w-100 justify-content-between\">";
    $output .= "<h5 class=\"mb-1\">" . $termObject->name . "</h5>";
    $output .= "<small class=\"text-muted\">" . "<span class=\"badge bg-primary rounded-pill\">" . $termObject->weeksInTerm() . " weeks</span> " . $log['ip'] . "</small>";
    //$output .= "<p id=\"" . $term['uid'] . "\" onclick=\"dismiss(this.id);\">dismiss this box</p>";
    //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
    $output .= "</div>";
    //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
    $output .= "<small class=\"text-muted\">" . dateDisplay($termObject->date_start) . " - " . dateDisplay($termObject->date_end) . "</small>";
    $output .= "</a>";

    echo $output;
  }
  ?>
</div>

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
          <div class="mb-3">
            <label for="name">Term Name</label>
            <input type="text" class="form-control" name="name" id="name" aria-describedby="termNameHelp">
            <small id="nameHelp" class="form-text text-muted">e.g. 'Trinity <?php echo date('Y')+1; ?>'</small>
          </div>

          <div class="mb-3">
            <label for="date_start">Term Start Date</label>
            <div class="input-group">
              <span class="input-group-text" id="date_start-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
              <input type="text" class="form-control" name="date_start" id="date_start" aria-describedby="date_start">
            </div>
            <small id="date_startHelp" class="form-text text-muted">Sunday of 1st week</small>
          </div>

          <div class="mb-3">
            <label for="date_end">Term End Date</label>
            <div class="input-group">
              <span class="input-group-text" id="date_start-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="img/icons.svg#calendar-plus"/></svg></span>
              <input type="text" class="form-control" name="date_end" id="date_end" aria-describedby="date_end">
            </div>
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
