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
	  domusReasonEl.focus();
	  domusReasonEl.classList.add('is-invalid');
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

	const guestDietaryEls = container.querySelectorAll('input.dietaryOptionsMax[name="guest_dietary[]"]:checked');
	const guestDietaryValues = Array.from(guestDietaryEls).map(el => el.value);
	
	
	
	// Guest name validation
	if (action !== 'guest_delete' && (!guest_nameEl || guest_nameEl.value.trim() === '')) {
	  guest_nameEl?.classList.add('is-invalid');
	  guest_nameEl?.focus();
	
	  button.disabled = false;
	  return;
	}
	
	// Domus validation
	if (action != 'guest_delete' && chargeToEl?.value === 'Domus' && (!domusReasonEl?.value || domusReasonEl.value.trim() === '')) {
	  domusReasonEl?.classList.add('is-invalid');
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

document.addEventListener('DOMContentLoaded', function () {
	 document.body.addEventListener('click', function(e) {
		 const button = e.target.closest('.transaction-delete-btn');
		 if (!button) return;
 
		 const transaction_uid = button.dataset.transaction_uid;
		 
		 e.preventDefault(); // prevent link while booking
 
		 const originalText = button.textContent;
 
		 // Show spinner
		 button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...`;
 
		 fetch('./ajax/wine_transaction_delete.php', {
			 method: 'POST',
			 headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			 body: 'uid=' + encodeURIComponent(transaction_uid)
		 })
		 .then(response => response.json())
		 .then(data => {
			 if (data.success) {
				 // Deletion confirmed
				 window.location.href = "index.php?page=wine_transactions"
			 } else {
				 button.textContent = originalText;
				 alert(data.message || 'Deleting transaction failed.');
			 }
		 })
		 .catch(error => {
			 console.error('Error:', error);
			 button.textContent = originalText;
			 alert('Deleting transaction failed due to a network error.');
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

// Enforce max dietary checkbox selections
function enforceDietaryLimits(root = document) {
  const containers = root.querySelectorAll('.accordion-body[data-max]');

  containers.forEach(container => {
	const max = parseInt(container.dataset.max, 10);
	const checkboxes = container.querySelectorAll('.dietaryOptionsMax');

	if (!checkboxes.length || isNaN(max)) return;

	function checkMaxCheckboxes() {
	  const checkedCount = Array.from(checkboxes)
		.filter(cb => cb.checked).length;

	  checkboxes.forEach(cb => {
		cb.disabled = !cb.checked && checkedCount >= max;
	  });
	}

	// Prevent duplicate listeners if called multiple times
	checkboxes.forEach(cb => {
	  cb.removeEventListener('change', checkMaxCheckboxes);
	  cb.addEventListener('change', checkMaxCheckboxes);
	});

	// Enforce immediately (page load / modal open)
	checkMaxCheckboxes();
  });
}

// Run on initial page load
document.addEventListener('DOMContentLoaded', () => {
  enforceDietaryLimits();
});

// Run when the guest modal is opened
document
  .getElementById('addEditGuestModal')
  ?.addEventListener('shown.bs.modal', e => {
	enforceDietaryLimits(e.target);
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

/**
 * live search for wines
 * @param {string} inputId - ID of the search input
 * @param {string} resultsId - ID of the container UL for results
 * @param {string} endpoint - URL for AJAX search
 * @param {Object} options - Optional parameters:
 *   - extraParams: additional GET parameters
 *   - onClick: function(item) called when a result is clicked
 */
function liveSearch(inputId, resultsId, endpoint, options = {}) {
	const input = document.getElementById(inputId);
	const results = document.getElementById(resultsId);
	if (!input || !results) return;

	input.addEventListener('keyup', function () {
		const query = this.value.trim();
		results.innerHTML = '';

		if (!query) return;

		const params = new URLSearchParams({ q: query, ...(options.extraParams || {}) });

		fetch(`${endpoint}?${params.toString()}`)
			.then(res => res.json())
			.then(response => {
				if (!response?.data?.length) {
					const li = document.createElement('li');
					li.className = 'list-group-item';
					li.textContent = 'No results found';
					results.appendChild(li);
					return;
				}

				response.data.forEach(item => {
					const li = document.createElement('li');
					li.className = 'list-group-item list-group-item-action';
					li.style.cursor = 'pointer';
					li.textContent = item.name;

					li.addEventListener('click', () => {
						if (typeof options.onClick === 'function') {
							options.onClick(item); // e.g., addWineToInvoice(item)
						} else {
							// Default: navigate to wine page
							window.location.href = `index.php?page=wine_wine&uid=${item.uid}`;
						}
						// Clear search input & results
						input.value = '';
						results.innerHTML = '';
					});

					results.appendChild(li);
				});
			})
			.catch(err => console.error(err));
	});
}

function liveSearchTransaction(inputId, resultsId, endpoint) {
	const input = document.getElementById(inputId);
	const results = document.getElementById(resultsId);

	if (!input || !results) return;

	input.addEventListener('keyup', function() {
		const query = this.value.trim();
		results.innerHTML = '';

		if (!query) return;

		const params = new URLSearchParams({ q: query });

		fetch(`${endpoint}?${params.toString()}`)
			.then(res => res.json())
			.then(data => {
				if (!data?.data?.length) {
					const li = document.createElement('li');
					li.className = 'list-group-item';
					li.textContent = 'No results found';
					results.appendChild(li);
					return;
				}

				data.data.forEach(wine => {
					const li = document.createElement('li');
					li.className = 'list-group-item list-group-item-action';
					li.style.cursor = 'pointer';
					li.textContent = wine.name;

					// When clicked, add wine to invoice
					li.addEventListener('click', () => {
						addWineToInvoice(wine);
						// Clear the search input and results
						input.value = '';
						results.innerHTML = '';
					});

					results.appendChild(li);
				});
			})
			.catch(err => console.error(err));
	});
}

document.addEventListener('click', function(e) {
	const heart = e.target.closest('.wine-heart');
	if (!heart) return;

	e.preventDefault();
	e.stopPropagation();

	const wineUid = heart.dataset.wineUid;
	const listUid = heart.dataset.listUid;

	fetch('./ajax/wine_toggle_fav.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ wine_uid: wineUid, list_uid: listUid })
	})
	.then(r => {
		if (!r.ok) throw new Error('Network response was not ok');
		return r.json();   // <-- this parses JSON from PHP
	})
	.then(data => {
		if (!data.success) {
			alert('Error: ' + (data.message || 'Unknown error'));
			return;
		}

		// Toggle heart icon
		heart.querySelector('i').classList.toggle('bi-heart');
		heart.querySelector('i').classList.toggle('bi-heart-fill');

		// Update wine count badge if you want
		const countBadge = heart.closest('.list-group-item').querySelector('.badge.bg-secondary');
		if (countBadge) countBadge.textContent = `${data.wine_count} wine${data.wine_count !== 1 ? 's' : ''}`;
	})
	.catch(err => {
		console.error('Fetch error:', err);
		alert('Failed to toggle favorite. See console.');
	});
});

// listen for meal template apply button
document.addEventListener('DOMContentLoaded', function () {
	document.body.addEventListener('click', function (e) {

		const button = e.target.closest('.meal-template-apply-btn');
		if (!button) return;

		e.preventDefault();

		const form   = document.getElementById('meal-template-form');
		const result = document.getElementById('template_result');
		if (!form || !result) return;

		const data = new FormData(form);

		const originalHTML = button.innerHTML;

		// Show spinner
		button.disabled = true;
		button.innerHTML =
			`<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
			 Applying…`;

		// Show progress message
		result.textContent = 'Applying meal template…';
		result.classList.remove('d-none');

		fetch('./ajax/meal_template_apply.php', {
			method: 'POST',
			body: data
		})
		.then(response => response.text())
		.then(html => {
			result.innerHTML = html;
			button.innerHTML = originalHTML;
			button.disabled = false;
		})
		.catch(error => {
			console.error(error);
			result.className = 'alert alert-danger';
			result.textContent = 'Something went wrong. The meal was not applied.';
			button.innerHTML = originalHTML;
			button.disabled = false;
		});
	});
});

// auto hide toasts (if any).  Picks up data-bs-autohide and data-bs-delay
document.addEventListener('DOMContentLoaded', function() {
  const toastElList = document.querySelectorAll('.toast');
  toastElList.forEach(toastEl => {
	const toast = new bootstrap.Toast(toastEl);
	toast.show();
  });
});