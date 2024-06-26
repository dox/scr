<?php
$termObject = new term();

if (isset($_GET['deleteBookingUID'])) {
  $bookingObject = new booking($_GET['deleteBookingUID']);

  if (isset($bookingObject->uid)) {
    $mealObject = new meal($bookingObject->meal_uid);
    if ($bookingObject->member_ldap == $_SESSION['username']) {
      if (date('Y-m-d H:i:s') <= date('Y-m-d H:i:s', strtotime($mealObject->date_cutoff)) || checkpoint_charlie("members,impersonate")) {
        $bookingObject->delete();
      } else {
        $logArray['category'] = "booking";
        $logArray['result'] = "danger";
        $logArray['description'] = "Error attempting to delete [bookingUID:" . $bookingObject->uid . "]. Cutoff passed.";
        $logsClass->create($logArray);
      }
    } else {
      if ( checkpoint_charlie("members,impersonate") == true) {
        $bookingObject->delete();
      } else {
        $logArray['category'] = "booking";
        $logArray['result'] = "danger";
        $logArray['description'] = "Error attempting to delete [bookingUID:" . $bookingObject->uid . "] . " . $_SESSION['username'] . " not permitted to delete booking for " . $bookingObject->member_ldap;
        $logsClass->create($logArray);
      }
    }
  }
}

?>
<?php
$title = "SCR Meal Booking";
$subtitle = "Current term: " . $termObject->currentTerm()['name'];

echo makeTitle($title, $subtitle);
?>

<style>
  .tabbable .nav-tabs {
     overflow-x: auto;
     overflow-y:hidden;
     flex-wrap: nowrap;
  }
  .tabbable .nav-tabs .nav-link {
    white-space: nowrap;
  }
</style>



<div class="container-fluid">
  <nav class="tabbable">
    <div class="nav nav-tabs justify-content-left" id="nav-tab" role="tablist">
      <?php
      $termsClass = new terms();
      $windowOfWeeks = $termsClass->arrayWindowOfWeeks();
      
      $firstDayOfCurrentWeek = firstDayOfWeek();
      
      foreach ($windowOfWeeks AS $week) {
        $checkTerm = $termsClass->checkIsInTerm($week);
        if (isset($checkTerm[0]['uid'])) {
          $term = new term($checkTerm[0]['uid']);
          $name = ordinal($term->whichWeek($week)) . " Week";
        } else {
          $name = "<small>w/c</small> " . date('M jS', strtotime($week));
        }
      
        if ($week == $firstDayOfCurrentWeek) {
          
          $class = "active";
          $name = "<strong>" . $name . "</strong>";
        } else {
          $class = "";
        }
      
        $output  = "<li class=\"nav-item\" id=\"" . $week . "-tab\" role=\"presentation\">";
        $output .= "<a class=\"nav-link " . $class . "\" id=\"" . $week . "\" data-toggle=\"tab\" href=\"#\" onclick=\"load_home(this.id)\" role=\"tab\" aria-controls=\"week-" . $week . "\">" . $name . "</a>";
        $output .= "</li>";
      
        echo $output;
      }
      ?>
    </div>
  </nav>
</div>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="week-1">
    <p class="text-center">
      <svg width="4em" height="4em" class="text-muted spinning">
        <use xlink:href="img/icons.svg#spinner"/>
      </svg>
    </p>
  </div>
</div>

<script>
load_home('<?php echo $firstDayOfCurrentWeek; ?>');
async function load_home(this_id) {
  var triggerEl = document.getElementById(this_id);
  var tabTrigger = new bootstrap.Tab(triggerEl);
  tabTrigger.show();

  home.innerHTML = "<div class=\"text-center\"><div class=\"spinner-border\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div></div>";
  let url = 'nodes/widgets/_weekly_meals.php?id=' + this_id;

  home.innerHTML = await (await fetch(url)).text();
}
</script>

<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="menuContentDiv"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
