function bookMealQuick(this_id) {
  var buttonClicked = document.getElementById(this_id);

  buttonClicked.className = 'btn btn-success';
  buttonClicked.innerText = 'Meal Booked';
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