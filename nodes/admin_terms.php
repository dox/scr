<?php
admin_gatekeeper();

$termsClass = new terms();

if (isset($_POST['name'])) {
  $termsClass->create($_POST);
  echo $settingsClass->alert("success", $_POST['name'], " term created");
}

$terms = $termsClass->all();
?>

<div class="container">
  <div class="px-3 py-3 pt-md-5 pb-md-4 text-center">
    <h1 class="display-4">Terms</h1>
    <p class="lead">Term dates from <a href="https://www.ox.ac.uk/about/facts-and-figures/dates-of-term">Oxford's website</a>.</p>
  </div>

  <div class="pb-3 text-right">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-calendar-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
        <path fill-rule="evenodd" d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7z"/>
      </svg> Add new
    </button>
  </div>

  <div class="list-group">
    <?php
    foreach ($terms AS $term) {
      $termObject = new term($term['uid']);

      if ($termObject->isCurrentTerm()) {
        $class = " list-group-item-primary";
      } else {
        $class = "";
      }
      $output  = "<a href=\"#\" class=\"list-group-item " . $class . " list-group-item-action\">";
      $output .= "<div class=\"d-flex w-100 justify-content-between\">";
      $output .= "<h5 class=\"mb-1\">" . $termObject->name . "</h5>";
      $output .= "<small class=\"text-muted\">" . "<span class=\"badge bg-primary rounded-pill\">" . $termObject->weeksInTerm() . " weeks</span> " . $log['ip'] . "</small>";
      //$output .= "<p id=\"" . $term['uid'] . "\" onclick=\"dismiss(this.id);\">dismiss this box</p>";
      //$output .= "<span class=\"badge bg-primary rounded-pill\">" . $log['type'] . "</span>";
      $output .= "</div>";
      //$output .= "<p class=\"mb-1\">" . $log['description'] . "</p>";
      $output .= "<small class=\"text-muted\">" . date('Y-m-d', strtotime($termObject->date_start)) . " - " . date('Y-m-d', strtotime($termObject->date_end)) . "</small>";
      $output .= "</a>";

      echo $output;
    }
    ?>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="termForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New Term Date</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
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
            <small id="date_startHelp" class="form-text text-muted">2020-01-01</small>
          </div>

          <div class="form-group">
            <label for="date_end">Term End Date</label>
            <input type="text" class="form-control" name="date_end" id="date_end" aria-describedby="termEndDate">
            <small id="date_endHelp" class="form-text text-muted">2020-09-30</small>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save term</button>
      </div>
      </form>
    </div>
  </div>
</div>



<script>
function dismiss(el){
  document.getElementById(el).parentNode.style.display='none';
};
</script>
