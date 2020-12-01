function bookMealQuick(this_id) {
  event.preventDefault()

  var mealUID = this_id.replace("mealUID-", "");

  var buttonClicked = document.getElementById(this_id);
  var formData = new FormData();

  formData.append("meal_uid", mealUID);

  //https://javascript.info/xmlhttprequest GREAT documentation!
  var request = new XMLHttpRequest();

  request.open("POST", "../actions/booking_create.php");
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else { // show the result
      buttonClicked.className = 'btn btn-success';
      buttonClicked.removeAttribute("onclick");
      buttonClicked.href = "index.php?n=booking&mealUID=" + mealUID;
      buttonClicked.innerText = 'Manage Booking';
      
      //alert(`Done, got ${request.response.length} bytes`); // response is the server response
    }
  };

  request.onerror = function() {
    alert("Request failed");
  };

  return false;
}

function bookingDeleteButton() {
  if (window.confirm("Are you sure you want to run delete this meal booking?  This will also remove all of your guests from this booking.")) {
			location.href = 'index.php';
	}
}


function guestDomus(this_id) {
  var domusCheckBox = document.getElementById(this_id);
  var domusDescriptionInput = document.getElementById('guest_domus_description');

  if (domusCheckBox.checked == true) {
	  domusDescriptionInput.disabled = false;
  } else {
	  domusDescriptionInput.disabled = true;
	  domusDescriptionInput.value = "";
  }
}

function submitForm(oFormElement) {
	var xhr = new XMLHttpRequest();

	xhr.onload = function(){
		// success case
		//alert (xhr.responseText);
	}

	xhr.onerror = function(){
		// failure case
		alert (xhr.responseText);
	}

	xhr.open (oFormElement.method, oFormElement.action, true);
	xhr.send (new FormData (oFormElement));

	return false;
}
