<?php
$connectionsConfig = [
	'id' => 'connections-day-24',
	'title' => 'Christmas Eve in Oxford',
	'subtitle' => 'The final board mixes Oxford tradition, learning, and a hint of celebration.',
	'footer' => 'A festive finish for the Advent run.',
	'intro_message' => 'One category belongs right at the end-of-term mood.',
	'groups' => [
		[
			'name' => 'End of term feelings',
			'color' => 'var(--cg-yellow)',
			'words' => ['EXHAUSTION', 'RELIEF', 'SATISFACTION', 'TRIUMPH'],
		],
		[
			'name' => 'Christmas in college',
			'color' => 'var(--cg-green)',
			'words' => ['CAROL', 'GARLAND', 'PUDDING', 'WREATH'],
		],
		[
			'name' => 'Academic achievements',
			'color' => 'var(--cg-blue)',
			'words' => ['AWARD', 'DISTINCTION', 'PRIZE', 'SCHOLARSHIP'],
		],
		[
			'name' => 'Oxford skyline words',
			'color' => 'var(--cg-purple)',
			'words' => ['CHIMNEY', 'DOME', 'ROOFLINE', 'TOWER'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
