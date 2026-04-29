<?php
$connectionsConfig = [
	'id' => 'connections-day-20',
	'title' => 'Hall, Quad, Library',
	'subtitle' => 'A Connections board built out of everyday Oxford rhythms.',
	'footer' => 'If in doubt, imagine the route between lunch and a tutorial.',
	'intro_message' => 'One set is all about getting around campus.',
	'groups' => [
		[
			'name' => 'Ways to move around Oxford',
			'color' => 'var(--cg-yellow)',
			'words' => ['CYCLE', 'PUNT', 'RUN', 'WANDER'],
		],
		[
			'name' => 'Things you can borrow',
			'color' => 'var(--cg-green)',
			'words' => ['BOOK', 'CHARGER', 'GOWN', 'KEY'],
		],
		[
			'name' => 'Words in a citation',
			'color' => 'var(--cg-blue)',
			'words' => ['PAGE', 'PRESS', 'TITLE', 'YEAR'],
		],
		[
			'name' => 'Quiet academic virtues',
			'color' => 'var(--cg-purple)',
			'words' => ['CARE', 'FOCUS', 'PATIENCE', 'PRECISION'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
