<?php
$connectionsConfig = [
	'id' => 'connections-day-22',
	'title' => 'Oxford Winter Connections',
	'subtitle' => 'Cold weather, old buildings, and academic habits all collide here.',
	'footer' => 'Festive, educational, and mildly devious.',
	'intro_message' => 'One group is all about the season around the colleges.',
	'groups' => [
		[
			'name' => 'Winter around college',
			'color' => 'var(--cg-yellow)',
			'words' => ['FROST', 'SCARF', 'SOUP', 'TWILIGHT'],
		],
		[
			'name' => 'Kinds of reading',
			'color' => 'var(--cg-green)',
			'words' => ['CLOSE', 'CRITICAL', 'FURTHER', 'SET'],
		],
		[
			'name' => 'Academic milestones',
			'color' => 'var(--cg-blue)',
			'words' => ['ADMISSION', 'GRADUATION', 'MATRICULATION', 'SUBMISSION'],
		],
		[
			'name' => 'Seen in an old library',
			'color' => 'var(--cg-purple)',
			'words' => ['DESK', 'GLOBE', 'LADDER', 'LEDGER'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
