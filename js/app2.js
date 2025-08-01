/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2023 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

(() => {
  'use strict'

  const getStoredTheme = () => localStorage.getItem('theme')
  const setStoredTheme = theme => localStorage.setItem('theme', theme)

  const getPreferredTheme = () => {
    const storedTheme = getStoredTheme()
    if (storedTheme) {
      return storedTheme
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  const setTheme = theme => {
    if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.setAttribute('data-bs-theme', 'dark');
    } else {
      document.documentElement.setAttribute('data-bs-theme', theme)
    }
  }

  setTheme(getPreferredTheme())

  const showActiveTheme = (theme, focus = false) => {
    const themeSwitcher = document.querySelector('#bd-theme')

    if (!themeSwitcher) {
      return
    }

    const themeSwitcherText = document.querySelector('#bd-theme-text')
    const activeThemeIcon = document.querySelector('.theme-icon-active use')
    const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
    const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')

    document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
      element.classList.remove('active')
      element.setAttribute('aria-pressed', 'false')
    })

    btnToActive.classList.add('active')
    btnToActive.setAttribute('aria-pressed', 'true')
    activeThemeIcon.setAttribute('href', svgOfActiveBtn)
    const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
    themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

    if (focus) {
      themeSwitcher.focus()
    }
  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = getStoredTheme()
    if (storedTheme !== 'light' && storedTheme !== 'dark') {
      setTheme(getPreferredTheme())
    }
  })

  window.addEventListener('DOMContentLoaded', () => {
    showActiveTheme(getPreferredTheme())

    document.querySelectorAll('[data-bs-theme-value]')
      .forEach(toggle => {
        toggle.addEventListener('click', () => {
          const theme = toggle.getAttribute('data-bs-theme-value')
          setStoredTheme(theme)
          setTheme(theme)
          showActiveTheme(theme, true)
        
          // fire a custom event for ApexCharts
          if (typeof chart !== 'undefined' && chart !== null) {
            chart.updateOptions({
              theme: {
                mode: theme
              }
            });
          }
        })
      })
  })
})()


function bookMealQuick(this_id) {
  event.preventDefault();

  const mealUID = this_id.replace("mealUID-", "");
  const buttonClicked = document.getElementById(this_id);

  const formData = new FormData();
  formData.append("meal_uid", mealUID);

  const request = new XMLHttpRequest();
  request.open("POST", "../actions/booking_create.php", true);
  request.send(formData);

  request.onload = function() {
    if (request.status !== 200) {
      alert("Something went wrong. Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`);
    } else {
      const isError = request.responseText.includes("Error");

      if (isError) {
        alert(request.responseText);
      } else {
        // ✅ Booking successful – update button styling and attributes
        buttonClicked.classList.remove("btn-primary", "btn-warning", "btn-secondary", "disabled");
        buttonClicked.classList.add("btn-success");

        // Just to be safe, make sure other required classes remain
        buttonClicked.classList.add("btn", "btn-sm", "w-100");

        // Update href and label
        buttonClicked.removeAttribute("onclick");
        buttonClicked.setAttribute("href", "index.php?n=booking&mealUID=" + mealUID);
        buttonClicked.innerText = "Manage Booking";
      }
    }
  };

  request.onerror = function() {
    alert("Request failed");
  };

  return false;
}

function addGuest(this_id) {
  var bookingUID = document.getElementById('bookingUID').value;
  var mealUID = document.getElementById('mealUID').value;
  var guestName = document.getElementById('guest_name').value;
  var guestDietary = [];
  var guestDietaryCheckboxes = document.querySelectorAll('input[name=guest_dietary]:checked');
  var guestChargeTo = document.getElementById('guest_charge_to').value;
  var guestDomusReason = document.getElementById('guest_domus_reason').value;
  var guestWineChoice = document.querySelector('input[name="guest_wine_choice"]:checked').value;
  var guestsList = document.getElementById('guests_list');
  var mealGuestList = document.getElementById('meal_guest_list');

  // if guest is domus, demand a description
  if (guestChargeTo == "") {
    alert("You must select a 'Charge To' for this guest.");
    return false;
  }
  if (guestChargeTo == "College Hospitality" || guestChargeTo == "Entertainment Allowance") {
    if (guestDomusReason == null || guestDomusReason == "") {
      alert("When charging a guest to '" + guestChargeTo + "' a reason must be given.");
      return false;
    }
  }

  for (var i = 0; i < guestDietaryCheckboxes.length; i++) {
    guestDietary.push(guestDietaryCheckboxes[i].value)
  }

  var xhr = new XMLHttpRequest();

  var formData = new FormData();
  formData.append("bookingUID", bookingUID);
  formData.append("guest_name", guestName);
  formData.append("guest_dietary", guestDietary);
  formData.append("guest_charge_to", guestChargeTo);
  formData.append("guest_domus_reason", guestDomusReason);
  formData.append("guest_wine_choice", guestWineChoice);

  xhr.onload = async function() {
    let url = 'nodes/widgets/_bookingGuestList.php?bookingUID=' + bookingUID + '&mealUID=' + mealUID;
    let url2 = 'nodes/widgets/_mealGuestList.php?mealUID=' + mealUID;

    // load the remote part into the guests_list div
    guestsList.innerHTML = await (await fetch(url)).text();
    mealGuestList.innerHTML = await (await fetch(url2)).text();

    // Close the Guest Add modal
    var modalGuestAdd = bootstrap.Modal.getInstance(document.getElementById('modalGuestAdd'));
    modalGuestAdd.hide();
    
    location.reload();
  }

  xhr.onerror = function(){
    // failure case
    alert (xhr.responseText);
  }

  xhr.open ("POST", "../actions/booking_add_guest.php", true);
  xhr.send (formData);

  return false;
}

function editGuest(this_id) {
  var bookingUID = document.getElementById('bookingUID').value;
  var mealUID = document.getElementById('mealUID').value;
  var guest_uid = this_id;
  var guestName = document.getElementById('guest_name').value;
  var guestDietary = [];
  var guestDietaryCheckboxes = document.querySelectorAll('input[name=guest_dietary]:checked');
  var guestChargeTo = document.getElementById('guest_charge_to').value;
  var guestDomusReason = document.getElementById('guest_domus_reason').value;
  var guestWineChoice = document.querySelector('input[name="guest_wine_choice"]:checked').value;
  var guestsList = document.getElementById('guests_list');
  var mealGuestList = document.getElementById('meal_guest_list');
  
  // if guest is domus, demand a description
  if (guestChargeTo == "College Hospitality" || guestChargeTo == "Entertainment Allowance") {
    if (guestDomusReason == null || guestDomusReason == "") {
      alert("When charging a guest to '" + guestChargeTo + "' a reason must be given.");
      return false;
    }
  }

  for (var i = 0; i < guestDietaryCheckboxes.length; i++) {
    guestDietary.push(guestDietaryCheckboxes[i].value)
  }

  var xhr = new XMLHttpRequest();

  var formData = new FormData();
  formData.append("bookingUID", bookingUID);
  formData.append("guest_uid", guest_uid);
  formData.append("guest_name", guestName);
  formData.append("guest_dietary", guestDietary);
  formData.append("guest_charge_to", guestChargeTo);
  formData.append("guest_domus_reason", guestDomusReason);
  formData.append("guest_wine_choice", guestWineChoice);

  xhr.onload = async function() {
    let url = 'nodes/widgets/_bookingGuestList.php?bookingUID=' + bookingUID + '&mealUID=' + mealUID;
    let url2 = 'nodes/widgets/_mealGuestList.php?mealUID=' + mealUID;

    // load the remote part into the guests_list div
    guestsList.innerHTML = await (await fetch(url)).text();
    mealGuestList.innerHTML = await (await fetch(url2)).text();

    // Close the Guest Add modal
    var modalGuestAdd = bootstrap.Modal.getInstance(document.getElementById('modalGuestAdd'));
    modalGuestAdd.hide();
    
    location.reload();
  }

  xhr.onerror = function(){
    // failure case
    alert (xhr.responseText);
  }

  xhr.open ("POST", "../actions/booking_edit_guest.php", true);
  xhr.send (formData);

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
        var modalGuestAdd = bootstrap.Modal.getInstance(document.getElementById('modalGuestAdd'));
        modalGuestAdd.hide();
        
        location.reload();
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

function editGuestModal(e) {
  var bookingUID = e.id.replace("bookingUID-", "");
  var guestUID = e.dataset.guestuid;
  var request = new XMLHttpRequest();

  request.open('GET', '/nodes/widgets/_guestAddEdit.php?bookingUID=' + bookingUID + '&guestUID=' + guestUID, true);

  request.onload = function() {
    if (request.status >= 200 && request.status < 400) {
      var resp = request.responseText;

      menuContentDiv.innerHTML = resp;
      
      const guest_element = document.getElementById("guest_charge_to");
      const guest_domus_reason = document.getElementById("guest_domus_reason");
      
      // show/hide the domus_reason box
      guest_element.addEventListener("change", (e) => {
        const value = e.target.value;
        const text = guest_element.options[guest_element.selectedIndex].text;
       
        if (value != "Battels") {
          // Domus/Entertainment sleected
          // show the domus_reason text box
          guest_domus_reason.required = true;
          guest_domus_reason.className = 'form-control mb-3';
        } else {
          // Battels selected
          // hide the domus_reason text box
          guest_domus_reason.value = "";
          guest_domus_reason.required = false;
          guest_domus_reason.className = 'form-control mb-3 visually-hidden';
        }
      });
    }
  };

  request.send();
}

function addGuestModal(e) {
  var bookingUID = e;
  var request = new XMLHttpRequest();

  request.open('GET', '/nodes/widgets/_guestAddEdit.php?bookingUID=' + bookingUID, true);

  request.onload = function() {
    if (request.status >= 200 && request.status < 400) {
      var resp = request.responseText;

      menuContentDiv.innerHTML = resp;
      
      const guest_element = document.getElementById("guest_charge_to");
      const guest_domus_reason = document.getElementById("guest_domus_reason");
      
      // show/hide the domus_reason box
      guest_element.addEventListener("change", (e) => {
        const value = e.target.value;
        const text = guest_element.options[guest_element.selectedIndex].text;
       
        if (value == "College Hospitality" || value == "Entertainment Allowance") {
          // Domus/Entertainment sleected
          // show the domus_reason text box
          guest_domus_reason.required = true;
          guest_domus_reason.className = 'form-control mb-3';
        } else {
          // Battels selected
          // hide the domus_reason text box
          guest_domus_reason.value = "";
          guest_domus_reason.required = false;
          guest_domus_reason.className = 'form-control mb-3 visually-hidden';
        }
      });
      
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
  checkMaxCheckboxes(2);
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
//var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
//var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
//  return new bootstrap.Tooltip(tooltipTriggerEl)
//})

// auto hide temporary alerts
