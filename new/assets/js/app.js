/**
 * Generic Ajax content loader
 * 
 * @param {string} triggerSelector - selector for links or tabs
 * @param {string} targetSelector - selector for container to inject content
 * @param {object} options - {event: string, cache: boolean}
 */
function initAjaxLoader(triggerSelector, targetSelector, options = {}) {
	 const useCache = options.cache !== undefined ? options.cache : true;
	 const eventName = options.event || 'click';
 
	 document.querySelectorAll(triggerSelector).forEach(trigger => {
		 trigger.addEventListener(eventName, function(e) {
			 if (eventName === 'click') e.preventDefault();
 
			 const target = targetSelector
				 ? document.querySelector(targetSelector)
				 : document.querySelector(trigger.getAttribute('href'));
 
			 if (!target) return;
 
			 const url = trigger.dataset.url;
			 if (!url) return;
 
			 if (useCache && target.dataset.loadedUrl === url) return;
 
			 target.innerHTML = `
			   <div class="d-flex justify-content-center align-items-center">
				 <div class="spinner-border" role="status">
				   <span class="visually-hidden">Loading...</span>
				 </div>
			   </div>`;
 
			 fetch(url)
				 .then(resp => resp.text())
				 .then(html => {
					 target.innerHTML = html;
					 if (useCache) target.dataset.loadedUrl = url;
				 })
				 .catch(err => {
					 target.innerHTML = '<div class="text-danger">Error loading content</div>';
					 console.error(err);
				 });
		 });
	 });
 
	 // Auto-load the tab with data-selected="true" or fallback to first tab
	 const selected = document.querySelector(`${triggerSelector}[data-selected="true"]`);
	 const toClick = selected || document.querySelector(triggerSelector);
 
	 if (toClick) toClick.click();
 }

document.addEventListener('DOMContentLoaded', function () {
	document.body.addEventListener('click', function(e) {
		const button = e.target.closest('.meal-book-btn');
		if (!button) return;
		
		const mealUid = button.dataset.meal_uid;
		
		// If already booked, just let it act as a normal link
		if (button.dataset.booked === '1') {
			return; // allow default click to navigate
		}
		
		e.preventDefault(); // prevent link while booking
		const originalText = button.textContent;
		
		// Show spinner
		button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Booking...`;
		
		fetch('./ajax/booking_create.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'meal_uid=' + encodeURIComponent(mealUid)
		})
		.then(response => response.json())
		.then(data => {
			if (data.success && data.booking_uid) {
				// Booking confirmed: turn into normal link
				button.classList.remove('btn-primary');
				button.classList.add('btn-success');
				button.textContent = 'Manage Booking';
				
				// Use the returned booking_uid for the link
				button.href = `index.php?page=booking&uid=${data.booking_uid}`;
				button.dataset.booked = '1'; // mark as booked
			} else {
				button.textContent = originalText;
				alert(data.message || 'Booking failed.');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			button.textContent = originalText;
			alert('Booking failed due to a network error.');
		});
	});
});

document.addEventListener('DOMContentLoaded', () => {

  document.body.addEventListener('click', async (e) => {
	const button = e.target.closest('.booking-update-btn');
	if (!button) return;

	e.preventDefault();

	const container = button.closest('form');
	if (!container) return;

	const chargeToEl     = container.querySelector('#charge_to');
	const domusReasonEl = container.querySelector('#domus_reason');
	const wineEl        = container.querySelector('#wine_choice');
	const dessertEl     = container.querySelector('#dessert');

	// Domus validation
	if (chargeToEl?.value === 'Domus' && (!domusReasonEl?.value || domusReasonEl.value.trim() === '')) {
	  alert('Please provide a reason for Domus.');
	  domusReasonEl.focus();
	  return;
	}

	const bookingUid = button.dataset.booking_uid;

	const originalText = button.textContent;
	button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;

	const data = new URLSearchParams({
	  action: 'booking_update',
	  booking_uid: bookingUid,
	  charge_to: chargeToEl?.value || '',
	  domus_reason: domusReasonEl?.value || '',
	  wine_choice: wineEl?.value || '',
	  dessert: dessertEl?.checked ? 1 : 0
	});

	try {
	  const res = await fetch('./ajax/booking_update.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: data.toString()
	  });

	  const json = await res.json();

	  if (json.success) {
		button.classList.replace('btn-primary', 'btn-success');
		button.textContent = 'Success';

		setTimeout(() => {
		  window.location.reload();
		}, 400);

	  } else {
		button.textContent = originalText;
		alert(json.message || 'Booking update failed.');
	  }

	} catch (err) {
	  console.error(err);
	  button.textContent = originalText;
	  alert('Booking failed due to a network error.');
	}

  });

});

document.addEventListener('DOMContentLoaded', () => {

  document.body.addEventListener('click', async (e) => {
	const button = e.target.closest('.guest-add-btn, .guest-update-btn, .guest-delete-btn');
	if (!button) return;

	e.preventDefault();
	
	// disable the button
	button.disabled = true;
	
	// Determine action
	let action = '';
	if (button.classList.contains('guest-add-btn')) action = 'guest_add';
	else if (button.classList.contains('guest-update-btn')) action = 'guest_update';
	else if (button.classList.contains('guest-delete-btn')) action = 'guest_delete';
	if (!action) return;

	const container = button.closest('form, .modal-content');
	if (!container) return;

	const chargeToEl     = container.querySelector('#guest_charge_to');
	const domusReasonEl = container.querySelector('#guest_domus_reason');
	const wineEl        = container.querySelector('#guest_wine_choice');
	const dessertEl     = container.querySelector('#guest_dessert');

	const guest_uidEl  = container.querySelector('#guest_uid');
	const guest_nameEl = container.querySelector('#guest_name');

	const guestDietaryEls = container.querySelectorAll('input[id="guest_dietary[]"]:checked');
	const guestDietaryValues = Array.from(guestDietaryEls).map(el => el.value);

	// Domus validation
	if (action != 'guest_delete' && chargeToEl?.value === 'Domus' && (!domusReasonEl?.value || domusReasonEl.value.trim() === '')) {
	  alert('Please provide a reason for Domus.');
	  domusReasonEl.focus();
	  
	  // re-enable the button
	  button.disabled = false;
	  
	  return;
	}

	const bookingUid = button.dataset.booking_uid;

	const originalText = button.textContent;
	button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Working...`;

	const data = new URLSearchParams({
	  action: action,
	  booking_uid: bookingUid,
	  guest_name: guest_nameEl?.value || '',
	  guest_uid: guest_uidEl?.value || '',
	  charge_to: chargeToEl?.value || '',
	  domus_reason: domusReasonEl?.value || '',
	  wine_choice: wineEl?.value || '',
	  dessert: dessertEl?.checked ? 1 : 0
	});

	guestDietaryValues.forEach(val => {
	  data.append('guest_dietary[]', val);
	});

	try {
	  const res = await fetch('./ajax/booking_update.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: data.toString()
	  });

	  const json = await res.json();

	  if (json.success) {
		button.classList.replace('btn-primary', 'btn-success');
		button.textContent = 'Success';

		setTimeout(() => {

		  if (action === 'guest_add' || action === 'guest_update') {
			bootstrap.Modal.getInstance(
			  document.getElementById('addEditGuestModal')
			)?.hide();
		  }
		  
		  window.location.reload();
		}, 400);

	  } else {
		// re-enable the button
		button.textContent = originalText;
		button.disabled = false;
		alert(json.message || 'Guest action failed.');
	  }

	} catch (err) {
	  console.error(err);
	  // re-enable the button
	  button.textContent = originalText;
	  button.disabled = false;
	  alert('Guest action failed due to a network error.');
	}
  });
});




document.addEventListener('DOMContentLoaded', function () {
	 document.body.addEventListener('click', function(e) {
		 const button = e.target.closest('.booking-delete-btn');
		 if (!button) return;
 
		 const bookingUID = button.dataset.booking_uid;
		 
		 e.preventDefault(); // prevent link while booking
 
		 const originalText = button.textContent;
 
		 // Show spinner
		 button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...`;
 
		 fetch('./ajax/booking_delete.php', {
			 method: 'POST',
			 headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			 body: 'booking_uid=' + encodeURIComponent(bookingUID)
		 })
		 .then(response => response.json())
		 .then(data => {
			 if (data.success) {
				 // Deletion confirmed
				 window.location.href = "index.php"
			 } else {
				 button.textContent = originalText;
				 alert(data.message || 'Deleting booking failed.');
			 }
		 })
		 .catch(error => {
			 console.error('Error:', error);
			 button.textContent = originalText;
			 alert('Deleting booking failed due to a network error.');
		 });
	 });
});

// quick filter for members
function filterList(inputSelector, listSelector) {
  const filterValue = document.querySelector(inputSelector).value.toLowerCase();
  const items = document.querySelectorAll(`${listSelector} li`);

  items.forEach(item => {
	const text = item.textContent.toLowerCase();
	item.style.display = text.includes(filterValue) ? '' : 'none';
  });
}

// limit dietary options to a maximum value
document.addEventListener('DOMContentLoaded', function () {
	const containers = document.querySelectorAll('.accordion-body[data-max]');

	containers.forEach(container => {
		const max = parseInt(container.dataset.max, 10);
		const checkboxes = container.querySelectorAll('.dietaryOptionsMax');

		function checkMaxCheckboxes() {
			const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
			checkboxes.forEach(cb => {
				cb.disabled = !cb.checked && checkedCount >= max;
			});
		}

		// bind change events
		checkboxes.forEach(cb => cb.addEventListener('change', checkMaxCheckboxes));

		// enforce the rule immediately
		checkMaxCheckboxes();
	});
});

// load remote content into a div
function remoteModalLoader(triggerSelector, modalSelector, bodySelector) {
  document.addEventListener('click', e => {
	const trigger = e.target.closest(triggerSelector);
	if (!trigger) return;

	e.preventDefault();

	const url = trigger.dataset.url;
	const target = document.querySelector(bodySelector);

	target.innerHTML = '<div class="text-muted">Loading...</div>';

	fetch(url)
	  .then(r => r.text())
	  .then(html => {
		target.innerHTML = html;

		// announce that the fragment is now alive in the DOM
		document.dispatchEvent(new CustomEvent('ajax-modal-loaded'), {});

		bootstrap.Modal.getOrCreateInstance(
		  document.querySelector(modalSelector)
		).show();
	  })
	  .catch(() => {
		target.innerHTML = '<div class="text-danger">Error loading content</div>';
	  });
  });
}

function enableOnExactMatch(inputId, buttonId, triggerText) {
	const field = document.getElementById(inputId);
	const button = document.getElementById(buttonId);

	button.disabled = (field.value !== triggerText);
}

// live search for wines
function liveSearch(inputId, resultsId, endpoint, extraParams = {}) {
	const input = document.getElementById(inputId);
	const results = document.getElementById(resultsId);

	input.addEventListener('keyup', function () {
		const query = this.value.trim();
		if (query === '') {
			results.innerHTML = '';
			return;
		}

		// Build query string
		let params = new URLSearchParams({ q: query, ...extraParams });

		let xhr = new XMLHttpRequest();
		xhr.open('GET', `${endpoint}?${params.toString()}`, true);

		xhr.onload = function () {
			results.innerHTML = '';

			if (xhr.status !== 200) return;

			let response = JSON.parse(xhr.responseText);

			if (!response.data || response.data.length === 0) {
				let li = document.createElement('li');
				li.className = "list-group-item";
				li.textContent = 'No results found';
				results.appendChild(li);
				return;
			}

			response.data.forEach(item => {
				let li = document.createElement('li');
				li.className = "list-group-item";
				let link = document.createElement('a');
				link.href = `index.php?page=wine_wine&uid=${item.uid}`;
				link.textContent = item.name;
				li.appendChild(link);
				results.appendChild(li);
			});
		};

		xhr.send();
	});
}

// auto hide toasts (if any).  Picks up data-bs-autohide and data-bs-delay
document.addEventListener('DOMContentLoaded', function() {
  const toastElList = document.querySelectorAll('.toast');
  toastElList.forEach(toastEl => {
	const toast = new bootstrap.Toast(toastEl);
	toast.show();
  });
});