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
 
			 target.innerHTML = '<div class="text-muted">Loading...</div>';
 
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
 
		 const mealUid = button.dataset.mealUid;
 
		 // If already booked, just let it act as a normal link
		 if (button.dataset.booked === '1') {
			 return; // allow default click to navigate
		 }
 
		 e.preventDefault(); // prevent link while booking
 
		 const originalText = button.textContent;
 
		 // Show spinner
		 button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Booking...`;
 
		 fetch('./ajax/meal_quickbook.php', {
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

function filterList(inputSelector, listSelector) {
  const filterValue = document.querySelector(inputSelector).value.toLowerCase();
  const items = document.querySelectorAll(`${listSelector} li`);

  items.forEach(item => {
	const text = item.textContent.toLowerCase();
	item.style.display = text.includes(filterValue) ? '' : 'none';
  });
}

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

function toggleReason(dropdownId, inputId, triggerValue) {
	const select = document.getElementById(dropdownId);
	const input = document.getElementById(inputId);

	function update() {
		const show = select.value === triggerValue; // only when equal
		input.classList.toggle('d-none', !show);
		input.required = show;
	}

	select.addEventListener('change', update);
	update();
}