<?php
$connectionsConfig = [
	'id' => 'connections-day-3',
	'title' => 'Festive Connections',
	'subtitle' => 'Find four groups of four. Pick four words, then hit Submit.',
	'footer' => 'Categories range from obvious to mildly evil, just as they should.',
	'intro_message' => 'One group is deliberately sneaky.',
	'groups' => [
		[
			'name' => 'Christmas words',
			'color' => 'var(--cg-yellow)',
			'words' => ['CAROL', 'JINGLE', 'MISTLETOE', 'TINSEL'],
		],
		[
			'name' => 'Things that glow',
			'color' => 'var(--cg-green)',
			'words' => ['CANDLE', 'EMBER', 'LANTERN', 'NEON'],
		],
		[
			'name' => 'Seen in the sky',
			'color' => 'var(--cg-blue)',
			'words' => ['AURORA', 'CLOUD', 'COMET', 'MOON'],
		],
		[
			'name' => 'Santa reindeer',
			'color' => 'var(--cg-purple)',
			'words' => ['CUPID', 'DASHER', 'DONNER', 'VIXEN'],
		],
	],
];

require __DIR__ . '/../_connections.php';
