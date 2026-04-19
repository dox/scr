<?php
$connectionsConfig = [
	'id' => 'connections-day-13',
	'title' => 'Across the Quad',
	'subtitle' => 'An Oxford walk turns into four neat categories.',
	'footer' => 'Mind the cobbles and the trick clues.',
	'intro_message' => 'Places, people, and study words all mingle here.',
	'groups' => [
		[
			'name' => 'People you may meet in college',
			'color' => 'var(--cg-yellow)',
			'words' => ['DEAN', 'FELLOW', 'PORTER', 'STUDENT'],
		],
		[
			'name' => 'Outdoor college features',
			'color' => 'var(--cg-green)',
			'words' => ['CLOCK', 'FOUNTAIN', 'GATEHOUSE', 'LAWN'],
		],
		[
			'name' => 'Research verbs',
			'color' => 'var(--cg-blue)',
			'words' => ['COMPARE', 'EVALUATE', 'LOCATE', 'SYNTHESISE'],
		],
		[
			'name' => 'Things you might carry to a tutorial',
			'color' => 'var(--cg-purple)',
			'words' => ['BINDER', 'HANDOUT', 'PENCIL', 'READING'],
		],
	],
];

require __DIR__ . '/../_connections.php';
