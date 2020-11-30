function bookMealQuick(this_id) {
  event.preventDefault()

  var mealUID = this_id.replace("mealUID-", "");

  var buttonClicked = document.getElementById(this_id);

  buttonClicked.className = 'btn btn-success';
  buttonClicked.removeAttribute("onclick");
  buttonClicked.href = "index.php?n=booking&mealUID=" + mealUID;
  buttonClicked.innerText = 'Manage Booking';

  var formData = new FormData();
  //formData.append("member_ldap", 'test');
  formData.append("meal_uid", mealUID);

  var request = new XMLHttpRequest();
  request.open("POST", "../actions/booking_create.php");
  request.send(formData);

  return false;
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
