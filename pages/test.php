<div class="container my-5">
	<h1 class="text-center mb-4 fw-bold">🎄 Advent Calendar</h1>

	<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-4 advent-calendar">
		<!-- Day 1 -->
		<div class="col"><div class="advent-door" data-day="1"><div class="advent-front"><span>1</span></div><div class="advent-back">🎁</div></div></div>
		<div class="col"><div class="advent-door" data-day="2"><div class="advent-front"><span>2</span></div><div class="advent-back">🍫</div></div></div>
		<div class="col"><div class="advent-door" data-day="3"><div class="advent-front"><span>3</span></div><div class="advent-back">❄️</div></div></div>
		<div class="col"><div class="advent-door" data-day="4"><div class="advent-front"><span>4</span></div><div class="advent-back">🎄</div></div></div>
		<div class="col"><div class="advent-door" data-day="5"><div class="advent-front"><span>5</span></div><div class="advent-back">🕯️</div></div></div>
		<div class="col"><div class="advent-door" data-day="6"><div class="advent-front"><span>6</span></div><div class="advent-back">🍪</div></div></div>

		<div class="col"><div class="advent-door" data-day="7"><div class="advent-front"><span>7</span></div><div class="advent-back">⭐</div></div></div>
		<div class="col"><div class="advent-door" data-day="8"><div class="advent-front"><span>8</span></div><div class="advent-back">🎶</div></div></div>
		<div class="col"><div class="advent-door" data-day="9"><div class="advent-front"><span>9</span></div><div class="advent-back">🧦</div></div></div>
		<div class="col"><div class="advent-door" data-day="10"><div class="advent-front"><span>10</span></div><div class="advent-back">☕</div></div></div>
		<div class="col"><div class="advent-door" data-day="11"><div class="advent-front"><span>11</span></div><div class="advent-back">🦌</div></div></div>
		<div class="col"><div class="advent-door" data-day="12"><div class="advent-front"><span>12</span></div><div class="advent-back">🎁</div></div></div>

		<div class="col"><div class="advent-door" data-day="13"><div class="advent-front"><span>13</span></div><div class="advent-back">🍷</div></div></div>
		<div class="col"><div class="advent-door" data-day="14"><div class="advent-front"><span>14</span></div><div class="advent-back">🎬</div></div></div>
		<div class="col"><div class="advent-door" data-day="15"><div class="advent-front"><span>15</span></div><div class="advent-back">🧣</div></div></div>
		<div class="col"><div class="advent-door" data-day="16"><div class="advent-front"><span>16</span></div><div class="advent-back">🍊</div></div></div>
		<div class="col"><div class="advent-door" data-day="17"><div class="advent-front"><span>17</span></div><div class="advent-back">🕯️</div></div></div>
		<div class="col"><div class="advent-door" data-day="18"><div class="advent-front"><span>18</span></div><div class="advent-back">🎵</div></div></div>

		<div class="col"><div class="advent-door" data-day="19"><div class="advent-front"><span>19</span></div><div class="advent-back">❄️</div></div></div>
		<div class="col"><div class="advent-door" data-day="20"><div class="advent-front"><span>20</span></div><div class="advent-back">🎄</div></div></div>
		<div class="col"><div class="advent-door" data-day="21"><div class="advent-front"><span>21</span></div><div class="advent-back">🍫</div></div></div>
		<div class="col"><div class="advent-door" data-day="22"><div class="advent-front"><span>22</span></div><div class="advent-back">🎁</div></div></div>
		<div class="col"><div class="advent-door" data-day="23"><div class="advent-front"><span>23</span></div><div class="advent-back">⭐</div></div></div>
		<div class="col"><div class="advent-door" data-day="24"><div class="advent-front"><span>24</span></div><div class="advent-back">🎅</div></div></div>

	</div>
</div>

<div class="modal fade" id="adventModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content rounded-4">
			<div class="modal-header">
				<h5 class="modal-title" id="adventModalTitle"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body" id="adventModalBody"></div>
		</div>
	</div>
</div>


<style>
.advent-calendar {
	max-width: 1100px;
	margin: auto;
}

.advent-door {
	position: relative;
	width: 100%;
	padding-top: 100%;
	perspective: 1000px;
	cursor: pointer;
}

.advent-front,
.advent-back {
	position: absolute;
	inset: 0;
	border-radius: 1rem;
	display: flex;
	align-items: center;
	justify-content: center;
	font-weight: bold;
	font-size: 2.2rem;
	transition: transform 0.6s ease, opacity 0.3s ease;
	backface-visibility: hidden;
}

.advent-front {
	background: linear-gradient(135deg, #b11226, #d62839);
	color: #fff;
	box-shadow: 0 10px 25px rgba(0,0,0,0.25);
}

.advent-back {
	background: #fff;
	transform: rotateY(180deg);
	box-shadow: inset 0 0 0 3px #d62839;
}

/* Hover flip */
.advent-door:not(.locked):hover .advent-front {
	transform: rotateY(-180deg);
}

.advent-door:not(.locked):hover .advent-back {
	transform: rotateY(0);
}

/* Opened */
.advent-door.opened .advent-front {
	transform: rotateY(-180deg);
}

.advent-door.opened .advent-back {
	transform: rotateY(0);
}

/* Locked */
.advent-door.locked {
	opacity: 0.4;
	cursor: not-allowed;
}

.advent-door.locked::after {
	content: "🔒";
	position: absolute;
	inset: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 2rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {

	const today = new Date();
	const currentDay = today.getMonth() === 0 ? today.getDate() : 0;

	const openedDays = JSON.parse(localStorage.getItem('openedDoors')) || [];

	const modal = new bootstrap.Modal(document.getElementById('adventModal'));
	const modalTitle = document.getElementById('adventModalTitle');
	const modalBody = document.getElementById('adventModalBody');

	const content = {
		1: "🎁 Welcome to December! Let the countdown begin.",
		2: "🍫 A little chocolate never hurt anyone.",
		3: "❄️ Wrap up warm — winter is settling in.",
		4: "🎄 Time to start feeling festive.",
		5: "🕯️ Slow down and enjoy a quiet moment.",
		6: "🍪 You’ve earned a sweet treat today.",
		7: "⭐ Something good is coming your way.",
		8: "🎶 Put on your favourite Christmas song.",
		9: "🧦 Cosy socks weather has officially arrived.",
		10: "☕ Take a break with a warm drink.",
		11: "🦌 Nearly halfway there — keep going!",
		12: "🎁 You’re doing brilliantly. Treat yourself.",
		13: "🍷 A glass of something warming tonight?",
		14: "🎬 Perfect evening for a festive film.",
		15: "🧣 Halfway! Wrap up and keep cosy.",
		16: "🍊 A classic Christmas flavour.",
		17: "🕯️ The nights are drawing in — embrace it.",
		18: "🎵 Turn the music up just a little.",
		19: "❄️ The big day is getting close now.",
		20: "🎄 Decorations deserve some admiration.",
		21: "🍫 One last indulgence before the final stretch.",
		22: "🍷 A glass of something warming tonight?",
		23: "🎁 Almost there… excitement building!",
		24: "⭐ Christmas Eve — savour it."
	};

	document.querySelectorAll('.advent-door').forEach(door => {
		const day = parseInt(door.dataset.day);

		if (day > currentDay) {
			door.classList.add('locked');
			return;
		}

		if (openedDays.includes(day)) {
			door.classList.add('opened');
		}

		door.addEventListener('click', () => {
			if (door.classList.contains('locked')) return;

			door.classList.add('opened');

			if (!openedDays.includes(day)) {
				openedDays.push(day);
				localStorage.setItem('openedDoors', JSON.stringify(openedDays));
			}

			modalTitle.textContent = `Day ${day}`;
			modalBody.innerHTML = content[day];
			modal.show();
		});
	});
});
</script>
