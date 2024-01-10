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
  <style>
  @media print 
  {
      @page {
        size: A4; /* DIN A4 standard, Europe */
        margin:0;
      }
      html, body {
          width: 210mm;
          height: 282mm;
          background: #FFF;
          overflow:visible;
      }
      body {
          padding-top:15mm;
      }
      table {
        page-break-inside:auto
      }
      tr    {
        page-break-inside:avoid; page-break-after:auto
      }
  }
</style>
</head>

<body class="bg-body-tertiary">
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
    
    <table class="table">
      <thead>
        <tr>
          <th scope="col" width="2em">#</th>
          <th scope="col">Name</th>
          <th scope="col" width="2em"><svg width="2em" height="2em" class="mx-1 text-muted"><use xlink:href="img/icons.svg#wine-glass"/></svg></th>
          <th scope="col" width="2em"><svg width="2em" height="2em" class="mx-1 text-muted"><use xlink:href="img/icons.svg#cookie"/></svg></th>
          <th scope="col" width="2em"><svg width="2em" height="2em" class="mx-1 text-muted"><use xlink:href="img/icons.svg#graduation-cap"/></svg></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $thisMealsBookingsUIDs = $bookingsClass->bookingsUIDsByMealUID($_GET['mealUID']);
        
        $i = 1;
        foreach ($thisMealsBookingsUIDs AS $booking) {
          $bookingObject = new booking($booking['uid']);
          $memberObject = new member($bookingObject->member_ldap);
          $guestsArray = $bookingObject->guestsArray();
          
          $output  = "<tr>";
          $output .= "<th scope=\"row\" rowspan=\"" . count($bookingObject->guestsArray()) + 1 . "\"><h5>" . $i . "</h5></th>";
          $output .= "<td>";
            $output .= "<h4>" . $memberObject->displayName() . "</h4>";
            if (!empty($memberObject->dietary)) {
              $dietaryArray = explode(",", $memberObject->dietary);
              
              $output .= implode(", ", $dietaryArray);
            }
          $output .= "</td>";
          
          $output .= "<td>";
            if ($bookingObject->wine == "1") {
              $output .= "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#wine-glass\"/></svg>";
            }
          $output .= "</td>";
          $output .= "<td>";
            if ($bookingObject->dessert == "1") {
              $output .= "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#cookie\"/></svg>";
            }
          $output .= "</td>";
          $output .= "<td>";
            if ($bookingObject->domus == "1") {
              $output .= "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#graduation-cap\"/></svg>";
            }
          $output .= "</td>";
          $output .= "</tr>";
          
          
          if (!empty($guestsArray)) {
            foreach ($guestsArray AS $guest) {
              $output .= "<tr>";
              
              $guest = json_decode($guest);
              
              $output .= "<td>";
                $output .= "<h5> + " . $guest->guest_name . "</h5>";
                if (!empty($guest->guest_dietary)) {
                  $output .= implode(", ", $guest->guest_dietary);
                }
              $output .= "</td>";
              $output .= "<td>";
                if ($guest->guest_wine == "on") {
                  $output .= "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#wine-glass\"/></svg>";
                }
              $output .= "</td>";
              $output .= "<td>";
                if ($bookingObject->dessert == "1") {
                  $output .= "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#cookie\"/></svg>";
                }
              $output .= "</td>";
              $output .= "<td>";
                if ($guest->guest_charge_to == "Domus") {
                  $output .= "<svg width=\"2em\" height=\"2em\" class=\"mx-1\"><use xlink:href=\"img/icons.svg#graduation-cap\"/></svg>";
                }
              $output .= "</td>";
              
              $output .= "</tr>";
            }
          }
          
          echo $output;
          
          $i++;
        }
        ?>
      </tbody>
    </table>
    
    <div class="row mt-3 float-end">
      <p><em>Guest List generated on <?php echo dateDisplay(date('r'), true) . " " . timeDisplay(date('r'), true) . " by " . $_SESSION['username']; ?></em></p>
    </div>
  </div>
</body>
</html>
