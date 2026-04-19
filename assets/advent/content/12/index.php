<?php
$connectionsConfig = [
	'id' => 'connections-day-12',
	'title' => 'Essay Crisis Connections',
	'subtitle' => 'Everything is due, somehow, and the groups are hiding in plain sight.',
	'footer' => 'A puzzle for anyone who has met a deadline at 11:59.',
	'intro_message' => 'One set belongs to writing under pressure.',
	'groups' => [
		[
			'name' => 'Essay process words',
			'color' => 'var(--cg-yellow)',
			'words' => ['DRAFTING', 'EDITING', 'PLANNING', 'PROOFREADING'],
		],
		[
			'name' => 'Ways to support an argument',
			'color' => 'var(--cg-green)',
			'words' => ['CITATION', 'EXAMPLE', 'FIGURE', 'STATISTIC'],
		],
		[
			'name' => 'Classic study break items',
			'color' => 'var(--cg-blue)',
			'words' => ['BISCUIT', 'COFFEE', 'HEADPHONES', 'SNACK'],
		],
		[
			'name' => 'Words for ranking work',
			'color' => 'var(--cg-purple)',
			'words' => ['ASSESS', 'GRADE', 'JUDGE', 'SCORE'],
		],
	],
];

require __DIR__ . '/../_connections.php';
