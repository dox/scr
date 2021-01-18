<?php
$termObject = new term();

if (isset($_GET['deleteBookingUID'])) {
  $bookingObject = new booking($_GET['deleteBookingUID']);
  if ($bookingObject->member_ldap == $_SESSION['username']) {
    echo "DELETE";
    $bookingObject->delete();
  } else {
    echo "Meal for '" . $bookingObject->member_ldap . "' not deleted.  You do not have permission as " . $_SESSION['username'];
  }
}
?>
<?php
$title = "SCR Meal Booking";
$subtitle = "Current term: " . $termObject->currentTerm()['name'];

echo makeTitle($title, $subtitle);
?>

<ul class="nav nav-tabs justify-content-center mb-4" id="myTab" role="tablist">
  <?php
  $termsClass = new terms();
  $windowOfWeeks = $termsClass->arrayWindowOfWeeks();

  foreach ($windowOfWeeks AS $week) {
    $checkTerm = $termsClass->checkIsInTerm($week);
    if (isset($checkTerm[0]['uid'])) {
      $term = new term($checkTerm[0]['uid']);
      $name = ordinal($term->whichWeek($week)) . " Week";
    } else {
      $name = "<small>w/c</small> " . date('M jS', strtotime($week));
    }

    if ($week == date('Y-m-d', strtotime('this week -1 day', time()))) {
      $class = "active";
    } else {
      $class = "";
    }

    $output  = "<li class=\"nav-item\" role=\"presentation\">";
    $output .= "<a class=\"nav-link " . $class . "\" id=\"" . $week . "\" data-toggle=\"tab\" href=\"#\" onclick=\"load_home(this.id)\" role=\"tab\" aria-controls=\"week-" . $week . "\" aria-selected=\"true\">" . $name . "</a>";
    $output .= "</li>";

    echo $output;
  }
  ?>
</ul>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="week-1">1...</div>
</div>


<script>
load_home('<?php echo date('Y-m-d', strtotime('this week -1 day', time())); ?>');
async function load_home(this_id) {
  let url = 'nodes/_weekly_meals.php?id=' + this_id;

  home.innerHTML = await (await fetch(url)).text();
}
</script>

<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="menuContentDiv"></div>
      <!--<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>-->
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
