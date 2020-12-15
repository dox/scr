<?php
$termsClass = new terms();

$meal = new meal($_GET['mealUID']);
$checkTerm = $termsClass->checkIsInTerm($meal->date_meal);
$term = new term($checkTerm[0]['uid']);

$bookingsClass = new bookings();
$bookingByMember = $bookingsClass->bookingForMealByMember($meal->uid, $_SESSION['username']);
$bookingObject = new booking($bookingByMember['uid']);

if (!empty($_POST['guest_name'])) {
  $bookingObject->update($_POST);
  $bookingObject = new booking($_GET['bookingUID']);
  echo $settingsClass->alert("success", $_POST['name'], " booking updated");
}

//$bookingsThisMeal = $bookingsClass->bookings_this_meal($meal->uid);

/*
$arr = array(
  array('name'=>'John Smith', 'dietary' => 'No fish'),
  array('name'=>'Jane Doe', 'dietary' => 'No pork')
);
echo json_encode($arr);
printArray($arr);
*/
?>

<?php
$title = "Week " . $term->whichWeek($meal->date_meal) . " " . $meal->name;
$subtitle = "Some text here about meal booking.  Make it simple!";
if ($_SESSION['admin'] == true) {
  //$icons[] = array("class" => "btn-warning", "name" => $icon_edit. " Edit Meal", "value" => "a href=\"index.php?n=admin_meal=" . $meal->uid . "\"");
}
$icons[] = array("class" => "btn-primary", "name" => $icon_add_member. " Add Guest", "value" => "data-toggle=\"modal\" data-target=\"#exampleModal\"");
$icons[] = array("class" => "btn-danger", "name" => $icon_delete. " Delete Booking", "value" => "data-toggle=\"modal\" data-target=\"#staticBackdrop\"");

echo makeTitle($title, $subtitle, $icons);
?>

<div class="row g-3">
      <div class="col-md-5 col-lg-4 order-md-last">
        <div class="divide-y">
          <div>
            <label class="row">
              <span class="col">Domus</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch">
                  <input class="form-check-input" id="domus" type="checkbox" <?php if ($bookingObject->domus == 1) { echo "checked";} ?> onchange="domus(this.id)">
                </label>
              </span>
              <input type="text" class="form-control" id="domus_description" placeholder="Domus reason (required)" hidden>
              <small id="domus_descriptionHelp" class="form-text text-muted" hidden>A brief description of why your booking is Domus</small>
            </label>
          </div>
          <div>
            <label class="row">
              <span class="col">Wine</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" <?php if ($bookingObject->wine == 1) { echo "checked";} ?>></label>
              </span>
            </label>
          </div>
          <div>
            <label class="row">
              <span class="col">Dessert</span>
              <span class="col-auto">
                <label class="form-check form-check-single form-switch"><input class="form-check-input" type="checkbox" <?php if ($bookingObject->dessert == 1) { echo "checked";} ?>></label>
              </span>
            </label>
          </div>
        </div>

        <hr />

        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">Your Guests</span>
          <span class="badge bg-secondary rounded-pill"><?php echo count($bookingObject->guestsArray()); ?></span>
        </h4>
        <ul class="list-group mb-3">
          <?php
          foreach ($bookingObject->guestsArray() AS $guest) {
            $deleteIcon = "<svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-x-circle-fill\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\"><path fill-rule=\"evenodd\" d=\"M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z\"/></svg>";

            $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
            $output .= "<div>";
            $output .= "<h6 class=\"my-0\">" . $guest['guest_name'] . " " . $badge . "</h6>";
            $output .= "<small class=\"text-muted\">" . $guest['guest_dietary'] . "</small>";
            if ($guest['guest_domus'] == "on") {
            	$output .= "<br /><span class=\"badge rounded-pill bg-info text-dark\">Domus</span>
            	<small class=\"text-muted\">" . $guest['guest_domus_description'] . "</small>";
            }
            $output .= "</div>";
            $output .= "<span class=\"text-muted\">" . $deleteIcon . "</span>";
            $output .= "</li>";

            echo $output;
          }
          ?>
        </ul>



      </div>
      <div class="col-md-7 col-lg-8">
        <h4 class="mb-3">Guest List</h4>
        <ul>
        <?php
        foreach ($meal->bookings_this_meal() AS $booking) {
          $totalGuests = count(json_decode($booking['guests_array']));

          $memberObject = new member($booking['member_ldap']);
          echo "<li>" . $memberObject->public_displayName() . " (" . $totalGuests . " guests)</li>";
        }
        ?>
        </ul>
      </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="../actions/booking_add_guest.php" onsubmit="return submitForm(this);">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Guest</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="name">Guest Name</label>
            <input type="text" class="form-control" name="guest_name" id="guest_name" aria-describedby="termNameHelp">
            <small id="nameHelp" class="form-text text-muted">This name will show on the sign-up list</small>
          </div>

          <div class="form-group">
            <label for="date_start">Guest's Dietary Requirements</label>
            <input type="text" class="form-control" name="guest_dietary" id="guest_dietary" aria-describedby="termStartDate">
            <small id="dietaryHelp" class="form-text text-muted">Leave blank for 'none'</small>
          </div>


          <hr />

          <div class="form-group">
            <div class="form-check form-switch">
	            <input class="form-check-input" type="checkbox" id="guest_wine" name="guest_wine">
	            <label class="form-check-label" for="domus">Wine</label>
	        </div>
          </div>
          <div class="form-group">
            <div class="form-check form-switch">
	            <input class="form-check-input" type="checkbox" id="guest_domus" name="guest_domus" onchange="guestDomus(this.id)">
	            <label class="form-check-label" for="domus">Domus</label>
	        </div>
          </div>
          <div class="form-group">
            <label for="date_start">Domus Description</label>
            <input type="text" class="form-control" name="guest_domus_description" id="guest_domus_description" hidden aria-describedby="domus_description" placeholder="Domus reason (required)">
            <small id="guest_domus_descriptionHelp" class="form-text text-muted" hidden>A brief description of why this guest is Domus</small>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save term</button>
      </div>
      <input type="hidden" id="bookingUID" name="bookingUID" value="<?php echo $bookingObject->uid; ?>">
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" tabindex="-1" id="staticBackdrop" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Meal Booking</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this meal booking?  This will also delete any guests you have booked for this meal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-dismiss="modal">Close</button>
        <a href="index.php?deleteBookingUID=<?php echo $bookingObject->uid; ?>" role="button" class="btn btn-danger" onclck="bookingDeleteButton();">Delete</a>
      </div>
    </div>
  </div>
</div>
