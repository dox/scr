function bookMealQuick(this_id) {
  event.preventDefault()

  var mealUID = this_id.replace("mealUID-", "");

  var buttonClicked = document.getElementById(this_id);
  var totalBookings = document.getElementById('capacityUID-' + mealUID);
  var formData = new FormData();

  formData.append("meal_uid", mealUID);

  //https://javascript.info/xmlhttprequest GREAT documentation!
  var request = new XMLHttpRequest();

  request.open("POST", "../actions/booking_create.php", true);
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else { // show the result
      //alert(this.responseText);

      buttonClicked.className = 'btn btn-success w-100';
      buttonClicked.removeAttribute("onclick");
      buttonClicked.href = "index.php?n=booking&mealUID=" + mealUID;
      totalBookings.innerHTML = parseInt(totalBookings.innerHTML + 1, 10);
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
  var domusDescriptionDiv = document.getElementsByClassName('guest_domus_descriptionDiv')[0];
  var domusDescriptionInput = document.getElementById('guest_domus_description');

  if (domusCheckBox.checked == true) {
    domusDescriptionDiv.classList.remove("visually-hidden");
  } else {
    domusDescriptionDiv.classList.add("visually-hidden");
	  domusDescriptionInput.value = "";
  }
}

function domus(this_id) {
  var domusCheckBox = document.getElementById(this_id);
  var domusDescriptionInput = document.getElementById('domus_description');
  var domusDescriptionHelp = document.getElementById('domus_descriptionHelp');

  if (domusCheckBox.checked == true) {
    domusDescriptionInput.hidden = false;
    domusDescriptionHelp.hidden = false;
  } else {
    domusDescriptionInput.hidden = true;
    domusDescriptionHelp.hidden = true;
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

function ldapLookup() {
  event.preventDefault();

  var firstname = document.getElementById('firstname').value;
  var lastname = document.getElementById('lastname').value;

  var formData = new FormData();

  formData.append("firstname", firstname);
  formData.append("sn", lastname);

  //https://javascript.info/xmlhttprequest GREAT documentation!
  var request = new XMLHttpRequest();

  request.open("POST", "../actions/ldap_lookup.php", true);
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else { // show the result
      if (this.responseText.includes("Unknown")) {
        alert(this.responseText);
      } else {
        document.getElementById('ldap').value = this.responseText;
      }

      //alert(`Done, got ${request.response.length} bytes`); // response is the server response
    }
  };

  request.onerror = function() {
    alert("Request failed");
  };

  return false;
}


// Multiselect for dietary
var expanded = false;

function showCheckboxes() {
  var checkboxes = document.getElementById("checkboxes");
  if (!expanded) {
    checkboxes.style.display = "block";
    expanded = true;
  } else {
    checkboxes.style.display = "none";
    expanded = false;
  }
}

function checkMaxCheckboxes(maxAllowed = 2) {
  var inputElems = document.getElementsByClassName("dietaryOptionsMax"), count = 0;

  for (var i=0; i<inputElems.length; i++) {
    if (inputElems[i].type == "checkbox" && inputElems[i].checked == true) {
      count++;
    }
  }

  if (count >= maxAllowed) {
    for (var i=0; i<inputElems.length; i++) {
      if (inputElems[i].checked != true) {
        inputElems[i].disabled = true;
      }
    }
  } else {
    for (var i=0; i<inputElems.length; i++) {
      inputElems[i].disabled = false;
    }
  }
}

// setup all tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
