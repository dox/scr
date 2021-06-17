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
  <?php include_once("views/html_head.php"); ?>
</head>

<body>
  <div class="container">
    <h1 class="text-center"><?php echo $mealObject->name; ?></h1>
    <h2 class="text-center mb-3"><?php echo $mealObject->location; ?> <small class="text-muted"><?php echo dateDisplay($mealObject->date_meal, true); ?></small></h2>

    <div class="row row-deck row-cards">
      <div class="col-6 col-md-3 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="subheader">SCR Diners</div>
            <div class="h1 mb-3"><?php echo $mealObject->total_bookings_this_meal('SCR'); ?></div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="subheader">MCR Diners</div>
            <div class="h1 mb-3"><?php echo $mealObject->total_bookings_this_meal('MCR'); ?></div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="subheader">SCR Dessert</div>
            <div class="h1 mb-3"><?php echo $mealObject->total_dessert_bookings_this_meal('SCR'); ?></div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="subheader">MCR Dessert</div>
            <div class="h1 mb-3"><?php echo $mealObject->total_dessert_bookings_this_meal('MCR'); ?></div>
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
        $guestsArray = $bookingObject->guestsArray();

        //printArray($guestsArray);

        $icons = array();
        $guestIcons = array();

        if (count($guestsArray) > 0) {
          $displayName = $memberObject->displayName() . " <small>(+ " . count($guestsArray) . autoPluralise(" guest", " guests", count($guestsArray)) . ")</small>";
        } else {
          $displayName = $memberObject->displayName();
        }

        if ($bookingObject->domus == "1") {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#graduation-cap\"/></svg>";
        } else {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#blank\"/></svg>";
        }
        if ($bookingObject->wine == "1") {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#wine-glass\"/></svg>";
        } else {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#blank\"/></svg>";
        }
        if ($bookingObject->dessert == "1") {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#cookie\"/></svg>";
        } else {
          $icons[] = "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#blank\"/></svg>";
        }

        $output  = "<a href=\"#\" class=\"list-group-item list-group-item-action\" aria-current=\"true\">";
        $output .= "<div class=\"d-flex w-100 justify-content-between\">";
        $output .= "<h5 class=\"mb-1\">" . $displayName . "</h5>";
        $output .= "<small>" . implode("", $icons) . "</small>";
        $output .= "</div>";

        if (!empty($memberObject->dietary)) {
          $output .= $memberObject->dietary;
        }

        if (!empty($guestsArray)) {
          //$output .= "<ul>";
          foreach ($guestsArray AS $guest) {
            $guest = json_decode($guest);
            $guestIcons = array();
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
              $output .= "<small class=\"text-muted\">" . implode(", ", $guest->guest_dietary) . "</small>";
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

    <div class="row mt-3 float-end">
      <p><em>Guest List generated on <?php echo dateDisplay(date('r'), true) . " " . timeDisplay(date('r'), true) . " by " . $_SESSION['username']; ?></em></p>
    </div>
  </div>
</body>
</html>
