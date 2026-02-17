<?php
$user->pageCheck('global_admin');
?>

<style>
/* Grid column spacing handled by Bootstrap .row.g-4 */
.advent-card {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1; /* modern */
  padding-top: 100%;    /* fallback */
  border-radius: 0.75rem;
  overflow: hidden;
  background-color: #b11226;
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  box-shadow: 0 10px 25px rgba(0,0,0,0.25);
  cursor: pointer;
  display: block;
  transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
}

.advent-card.unlocked:hover {
  transform: translateY(-6px);
  box-shadow: 0 18px 40px rgba(0,0,0,0.35);
}

.advent-card .overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

.advent-card .day-number {
  font-weight: 800;
  font-size: 2.4rem;
  color: #fff;
  text-shadow: 0 4px 10px rgba(0,0,0,0.6);
  z-index: 2;
}

.advent-card .overlay::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.12), rgba(0,0,0,0.28));
  z-index: 1;
}

.advent-card.locked {
  filter: grayscale(0.9) brightness(0.6);
  cursor: not-allowed;
}

.advent-card .lock {
  position: absolute;
  top: 8px;
  right: 8px;
  z-index: 3;
  font-size: 1.4rem;
  pointer-events: none;
}

.advent-card:focus {
  outline: 3px solid rgba(214,40,57,0.45);
  outline-offset: 3px;
}

/* Modal body slight reset */

@media (max-width: 480px) {
  .advent-card .day-number { font-size: 1.8rem; }
}
</style>
  
<div class="container my-5">
	<h1>🎄 Advent Calendar</h1>

	<div class="row g-4" id="calendarGrid" aria-live="polite">
	  <!-- cards injected by JS -->
	</div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="adventModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="adventModalTitle"></h5>
		  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body" id="adventModalBody"></div>
	  </div>
	</div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
  
	/* ===============================
	   CONFIG
	=============================== */
  
	const IMG_EXT = 'jpg';
	const imgPath = day => `assets/advent/cards/${day}.${IMG_EXT}`;
	const LOCKED_IMAGE = 'assets/advent/cards/locked.jpg';
  
	// Base folder for modal content
	const CONTENT_BASE_PATH = '/assets/advent/content';
  
	const FETCH_TIMEOUT = 8000; // 8 seconds
  
	/* ===============================
	   DATE LOGIC
	   December = month 11 (0-based)
	=============================== */
  
	const today = new Date();
	const currentDay = (today.getMonth() === 1) ? today.getDate() : 0;
  
	/* ===============================
	   MODAL SETUP
	=============================== */
  
	const modalEl = document.getElementById('adventModal');
	const bsModal = new bootstrap.Modal(modalEl);
	const modalTitle = document.getElementById('adventModalTitle');
	const modalBody = document.getElementById('adventModalBody');
  
	/* ===============================
	   Loading Spinner
	=============================== */
  
	function showSpinner(day) {
	  modalTitle.textContent = `Day ${day}`;
	  modalBody.innerHTML = `
		<div class="d-flex justify-content-center align-items-center" style="min-height:150px;">
		  <div class="spinner-border text-secondary" role="status"></div>
		</div>
	  `;
	}
  
	/* ===============================
	   Remote Fetch Per Day
	=============================== */
  
	const contentCache = new Map();
  
	async function fetchDayContent(day) {
  
	  if (contentCache.has(day)) {
		return contentCache.get(day);
	  }
  
	  const url = `${CONTENT_BASE_PATH}/${day}/index.php`;
  
	  const controller = new AbortController();
	  const timeout = setTimeout(() => controller.abort(), FETCH_TIMEOUT);
  
	  try {
		const response = await fetch(url, {
		  signal: controller.signal,
		  credentials: 'same-origin'
		});
  
		clearTimeout(timeout);
  
		if (!response.ok) {
		  throw new Error(`Server error: ${response.status}`);
		}
  
		const html = await response.text();
  
		contentCache.set(day, html);
		return html;
  
	  } catch (error) {
		clearTimeout(timeout);
		console.error('Fetch error:', error);
		return null;
	  }
	}
  
	/* ===============================
	   Load + Show Modal
	=============================== */
  
	async function loadAndShow(day) {
  
	  showSpinner(day);
	  bsModal.show();
  
	  const html = await fetchDayContent(day);
  
	  if (!html) {
		modalBody.innerHTML = `
		  <div class="alert alert-warning mb-0">
			Sorry — content could not be loaded.
		  </div>
		`;
		return;
	  }
  
	  modalTitle.textContent = `Day ${day}`;
	  modalBody.innerHTML = html;
	}
  
	/* ===============================
	   BUILD GRID
	=============================== */
  
	const grid = document.getElementById('calendarGrid');
  
	for (let day = 1; day <= 24; day++) {
  
	  const col = document.createElement('div');
	  col.className = 'col-6 col-sm-4 col-md-3 col-lg-2';
  
	  const card = document.createElement('div');
	  card.className = 'advent-card';
	  card.dataset.day = day;
	  card.setAttribute('role', 'button');
	  card.setAttribute('tabindex', '0');
	  card.setAttribute('aria-label', `Day ${day}`);
  
	  const overlay = document.createElement('div');
	  overlay.className = 'overlay';
	  overlay.innerHTML = `<span class="day-number">${day}</span>`;
	  card.appendChild(overlay);
  
	  const isLocked = day > currentDay;
  
	  if (isLocked) {
  
		card.style.backgroundImage = `url('${LOCKED_IMAGE}')`;
		card.classList.add('locked');
  
		const lockEl = document.createElement('div');
		lockEl.className = 'lock';
		lockEl.textContent = '🔒';
		card.appendChild(lockEl);
  
	  } else {
  
		card.classList.add('unlocked');
  
		const img = new Image();
		img.onload = () => {
		  card.style.backgroundImage = `url('${imgPath(day)}')`;
		};
		img.src = imgPath(day);
	  }
  
	  card.addEventListener('click', () => {
		if (card.classList.contains('locked')) return;
		loadAndShow(day);
	  });
  
	  card.addEventListener('keydown', (e) => {
		if (e.key === 'Enter' || e.key === ' ') {
		  e.preventDefault();
		  card.click();
		}
	  });
  
	  col.appendChild(card);
	  grid.appendChild(col);
	}
  
  });
  </script>
