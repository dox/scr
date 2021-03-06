function bookMealQuick(this_id) {
  event.preventDefault();

  var mealUID = this_id.replace("mealUID-", "");

  var buttonClicked = document.getElementById(this_id);
  var formData = new FormData();

  formData.append("meal_uid", mealUID);

  var request = new XMLHttpRequest();

  request.open("POST", "../actions/booking_create.php", true);
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else {
      // check if the booking was made
      var error_check = request.responseText.includes("Error");

      // if the booking had an error, alert
      if (error_check == true) {
        alert(this.responseText);
      } else {
        // booking made, update the booking button
        buttonClicked.className = 'btn btn-sm rounded-0 rounded-bottom btn-success';
        buttonClicked.removeAttribute("onclick");
        buttonClicked.href = "index.php?n=booking&mealUID=" + mealUID;
        //totalBookings.innerHTML = parseInt(totalBookings.innerHTML, 10)+1;
        buttonClicked.innerText = 'Manage Booking';
      }
    }
  }

  request.onerror = function() {
    alert("Request failed");
  };

  return false;
};

function addGuest(oFormElement) {
  event.preventDefault();

  var xhr = new XMLHttpRequest();

	xhr.onload = async function() {
    var bookingUID = document.getElementById('bookingUID').value;
    var mealUID = document.getElementById('mealUID').value;
    var guestsList = document.getElementById('guests_list');
    var mealGuestList = document.getElementById('meal_guest_list');
    let url = 'nodes/widgets/_bookingGuestList.php?bookingUID=' + bookingUID + '&mealUID=' + mealUID;
    let url2 = 'nodes/widgets/_mealGuestList.php?mealUID=' + mealUID;

    // load the remote part into the guests_list div
    guestsList.innerHTML = await (await fetch(url)).text();
    mealGuestList.innerHTML = await (await fetch(url2)).text();

    // Close the Guest Add modal
    var modalGuestAdd = bootstrap.Modal.getInstance(document.getElementById('modal_guest_add'));
    modalGuestAdd.hide();
	}

	xhr.onerror = function(){
		// failure case
		alert (xhr.responseText);
	}

	xhr.open (oFormElement.method, oFormElement.action, true);
	xhr.send (new FormData (oFormElement));

	return false;
}

function deleteGuest(this_id) {

  var booking_uid = document.getElementById('bookingUID').value;
  var mealUID = document.getElementById('mealUID').value;
  var guest_uid = this_id;
  var guestsList = document.getElementById('guests_list');
  var mealGuestList = document.getElementById('meal_guest_list');

  if (window.confirm("Are you sure you want to remove this guest from your booking?")) {

      var xhr = new XMLHttpRequest();

      var formData = new FormData();
      formData.append("guest_uid", guest_uid);
      formData.append("booking_uid", booking_uid);

    	xhr.onload = async function() {
        let url = 'nodes/widgets/_bookingGuestList.php?bookingUID=' + booking_uid + '&mealUID=' + mealUID;
        let url2 = 'nodes/widgets/_mealGuestList.php?mealUID=' + mealUID;

        // load the remote part into the guests_list div
        guestsList.innerHTML = await (await fetch(url)).text();
        mealGuestList.innerHTML = await (await fetch(url2)).text();

        // Close the Guest Add modal
        var modalGuestAdd = bootstrap.Modal.getInstance(document.getElementById('modal_guest_add'));
    	}

    	xhr.onerror = function(){
    		// failure case
    		alert (xhr.responseText);
    	}

    	xhr.open ("POST", "../actions/booking_delete_guest.php", true);
    	xhr.send (formData);

    	return false;
	}

}

function displayMenu(this_id) {
  var mealUID = this_id.replace("menuUID-", "");
  var request = new XMLHttpRequest();

  request.open('GET', '/nodes/widgets/_menu.php?mealUID=' + mealUID, true);

  request.onload = function() {
    if (request.status >= 200 && request.status < 400) {
      var resp = request.responseText;

      menuContentDiv.innerHTML = resp;
    }
  };

  request.send();
}

function applyTemplate() {
  event.preventDefault();

  var template_name = document.getElementById('template_name').value;
  var template_start_date = document.getElementById('template_start_date').value;

  var formData = new FormData();

  formData.append("template_name", template_name);
  formData.append("template_start_date", template_start_date);

  //https://javascript.info/xmlhttprequest GREAT documentation!
  var request = new XMLHttpRequest();

  request.open("POST", "../actions/template_apply.php", true);
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else { // show the result
      alert("Template applied!");
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
    domusDescriptionInput.required = true;
  } else {
    domusDescriptionDiv.classList.add("visually-hidden");
	  domusDescriptionInput.value = "";
    domusDescriptionInput.required = false;
  }
}

function domusCheckbox(this_id) {
  var domusCheckBox = document.getElementById(this_id);
  var domusDescriptionInput = document.getElementById('domus_reason');
  var domusDescriptionHelp = document.getElementById('domus_reasonHelp');

  if (domusCheckBox.checked == true) {
    domusDescriptionInput.hidden = false;
    domusDescriptionInput.required = true;
    domusDescriptionHelp.hidden = false;
  } else {
    domusDescriptionInput.hidden = true;
    domusDescriptionInput.required = false;
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
        document.getElementById('ldap').value = this.responseText.toUpperCase();
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

function impersonate(oFormElement) {
  var impersonateDropdown = document.getElementById('impersonate_ldap');
  var buttonClicked = document.getElementById('impersonate_submit_button');
  var impersonateHeaderButton = document.getElementById('impersonating_header_button');

  submitForm(oFormElement);

  if (buttonClicked.value == "stop") {
    buttonClicked.classList.remove("btn-warning");
    buttonClicked.classList.add("btn-primary");
    buttonClicked.innerHTML = "Impersonate";
    buttonClicked.value = "";
    impersonateDropdown.disabled = false;
    impersonateHeaderButton.classList.add("visually-hidden");
  } else {
    buttonClicked.classList.remove("btn-primary");
    buttonClicked.classList.add("btn-warning");
    buttonClicked.innerHTML = "Stop Impersonating";
    buttonClicked.value = "stop";
    impersonateDropdown.disabled = true;
    impersonateHeaderButton.classList.remove("visually-hidden");
  }

  return false;
}

// setup all tooltips
//var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
//var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
//  return new bootstrap.Tooltip(tooltipTriggerEl)
//})

// auto hide temporary alerts
