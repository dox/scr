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

			// Determine target
			const target = targetSelector ? document.querySelector(targetSelector) : document.querySelector(trigger.getAttribute('href'));
			if (!target) return;

			const url = trigger.dataset.url;
			if (!url) return;

			// Skip if already loaded
			if (useCache && target.dataset.loadedUrl === url) return;

			// Show loading
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

			// Highlight active link if needed
			document.querySelectorAll(triggerSelector).forEach(el => el.classList.remove('fw-bold'));
			trigger.classList.add('fw-bold');
		});
	});

	// Auto-load first element
	const first = document.querySelector(triggerSelector);
	if (first) first.click();
}
