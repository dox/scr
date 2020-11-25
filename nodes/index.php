<?php
$termObject = new term();
?>
<main class="container">
  <div class="px-3 py-3 pt-md-5 pb-md-4 text-center">
    <h1 class="display-4">Meals</h1>
    <p class="lead">Some text here about meal booking.  Make it simple!</p>
  </div>
    <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
	  <li class="nav-item" role="presentation"><a class="nav-link">Vacation</a></li>
      <?php
      $i = 0;
      do {
        if ($termObject->currentWeek() == $i) {
          $class = " active";
        } else {
          $class = "";
        }
        $output  = "<li class=\"nav-item\" role=\"presentation\">";
        $output .= "<a class=\"nav-link " . $class . "\" id=\"" . $i . "\" data-toggle=\"tab\" href=\"#\" onclick=\"load_home(this.id)\" role=\"tab\" aria-controls=\"week-" . $i . "\" aria-selected=\"true\">Week " . $i . "</a>";
        $output .= "</li>";

        echo $output;

        $i++;
      } while ($i <= $termObject->weeksInTerm());
      ?>
      <li class="nav-item" role="presentation"><a class="nav-link">Vacation</a></li>
    </ul>

    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="week-1">1...</div>
    </div>
  </div>
  </div>
</main>





<script>
load_home('<?php echo $termObject->currentWeek(); ?>');
async function load_home(this_id) {
  let url = 'nodes/_weekly_meals.php?id=' + this_id;

  home.innerHTML = await (await fetch(url)).text();
}
</script>
