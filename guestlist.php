<?php
include_once("inc/autoload.php");

admin_gatekeeper();

$mealObject = new meal($_GET['mealUID']);
$bookingsClass = new bookings();
//printArray($mealObject);

?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Andrew Breakspear">
  <title>St Edmund Hall: <?php echo $mealObject->name . ", " . dateDisplay($mealObject->date_meal, true); ?></title>

  <link rel="canonical" href="https://scr2.seh.ox.ac.uk/">

  <!-- Bootstrap core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/app.css" rel="stylesheet">

	<!-- Favicons -->
	<link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
  <link rel="manifest" href="/img/favicons/site.webmanifest">
	<link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
  <link rel="icon" href="/img/favicons/favicon.ico">
  <meta name="theme-color" content="#7952b3">

  <script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/app.js"></script>
</head>

<body>
  <div class="container">
    <h1 class="display-1 text-center"><?php echo $mealObject->name; ?></h1>
    <h1 class="display-6 text-center"><?php echo $mealObject->location; ?> <small class="text-muted"><?php echo dateDisplay($mealObject->date_meal, true); ?></small></h1>

    <div class="row row-deck row-cards mb-3">
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="subheader">SCR Diners</div>
            <div class="h1 mb-3"><?php echo $mealObject->total_bookings_this_meal('SCR'); ?></div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="subheader">MCR Diners</div>
            <div class="h1 mb-3"><?php echo $mealObject->total_bookings_this_meal('MCR'); ?></div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="subheader">SCR Dessert</div>
            <div class="h1 mb-3">test</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card">
          <div class="card-body">
            <div class="subheader">MCR Dessert</div>
            <div class="h1 mb-3">test</div>
          </div>
        </div>
      </div>
    </div>
    <h4 class="d-flex justify-content-between align-items-center mb-3">Guest List</h4>
    <div class="list-group">
      <?php
      $thisMealsBookingsUIDs = $bookingsClass->bookingsUIDsByMealUID($_GET['mealUID']);
      foreach ($thisMealsBookingsUIDs AS $booking) {
        $bookingObject = new booking($booking['uid']);
        $memberObject = new member($bookingObject->member_ldap);
        $guestsArray = json_decode($bookingObject->guests_array);
        //printArray($bookingObject);

        $icons = array();
        $guestIcons = array();

        if (count($guestsArray) > 0) {
          $displayName = $memberObject->displayName() . " <small>(+ " . count($guestsArray) . autoPluralise(" guest", " guests", count($guestsArray)) . ")</small>";
        } else {
          $displayName = $memberObject->displayName();
        }

        if ($booking['domus'] == "1") {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"><use xlink:href=\"img/icons.svg#graduation-cap\"/></svg>";
        }
        if ($booking['wine'] == "1") {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"><use xlink:href=\"img/icons.svg#wine-glass\"/></svg>";
        }
        if ($booking['dessert'] == "1") {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"><use xlink:href=\"img/icons.svg#cookie\"/></svg>";
        }

        $output  = "<a href=\"#\" class=\"list-group-item list-group-item-action\" aria-current=\"true\">";
        $output .= "<div class=\"d-flex w-100 justify-content-between\">";
        $output .= "<h5 class=\"mb-1\">" . $displayName . "</h5>";
        $output .= "<small>" . implode("", $icons) . "</small>";
        $output .= "</div>";

        if (!empty($guestsArray)) {
          //$output .= "<ul>";
          foreach ($guestsArray AS $guest) {
            if ($guest->guest_domus == "on") {
              $guestIcons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"><use xlink:href=\"img/icons.svg#graduation-cap\"/></svg>";
            } else {
              $guestIcons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"></svg>";
            }
            if ($guest->guest_wine == "on") {
              $guestIcons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"><use xlink:href=\"img/icons.svg#wine-glass\"/></svg>";
            } else {
              $guestIcons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"></svg>";
            }
            if ($guest->guest_dessert == "on") {
              $guestIcons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"><use xlink:href=\"img/icons.svg#cookie\"/></svg>";
            } else {
              $guestIcons[] = "<svg width=\"2em\" height=\"2em\" class=\"ms-1 me-1\"></svg>";
            }

            $output .= "<div class=\"d-flex w-100 justify-content-between\">";
            $output .= "<h6 class=\"mb-1 text-muted\"> + " . $guest->guest_name . "</h6>";
            $output .= "<small>" . implode("", $guestIcons) . "</small>";
            $output .= "</div>";

            //$output .= "<li>" . $guest->guest_name . implode(", ", $guestIcons) ."</li>";
            if (!empty($guest->guest_dietary)) {
              //$output .= "<small class=\"text-muted\">" . implode(", ", $guest->guest_dietary) . "</small>";
            }
          }
          $output .= "</ul>";
        }

        //$output .= "<p class=\"mb-1\">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>";

        if (!empty($guestObject->dietary)) {
          $output .= "<small>" . implode(", ", $guestObject->guest_dietary) . "</small>";
        }
        $output .= "</a>";

        echo $output;
      }
      ?>
    </div>
  </div>
</body>
</html>
