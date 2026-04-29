<?php
$connectionsConfig = [
	'id' => 'connections-day-23',
	'title' => 'Bells and Books',
	'subtitle' => 'A late-Advent board with Oxford atmosphere and studious clues.',
	'footer' => 'Nearly the end, still no easy freebies.',
	'intro_message' => 'Listen for one group that belongs to the city soundscape.',
	'groups' => [
		[
			'name' => 'Sounds in Oxford',
			'color' => 'var(--cg-yellow)',
			'words' => ['BICYCLEBELL', 'CHOIR', 'FOOTSTEPS', 'LAUGHTER'],
		],
		[
			'name' => 'Words in note-taking',
			'color' => 'var(--cg-green)',
			'words' => ['ARROW', 'HEADING', 'MARGIN', 'UNDERLINE'],
		],
		[
			'name' => 'Academic groupings',
			'color' => 'var(--cg-blue)',
			'words' => ['COHORT', 'FACULTY', 'SUBJECT', 'TERM'],
		],
		[
			'name' => 'College garden things',
			'color' => 'var(--cg-purple)',
			'words' => ['FLOWERBED', 'HEDGE', 'IVY', 'ROBIN'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
