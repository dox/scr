<?php
$connectionsConfig = [
	'id' => 'connections-day-10',
	'title' => 'Teddy Hall Terms',
	'subtitle' => 'St Edmund Hall inspiration, with a dash of everyday Oxford student life.',
	'footer' => 'A college name can carry a lot of puzzle mileage.',
	'intro_message' => 'Expect a few cozy Hall-adjacent clues.',
	'groups' => [
		[
			'name' => 'Student accommodation words',
			'color' => 'var(--cg-yellow)',
			'words' => ['BEDDER', 'BULLETIN', 'CORRIDOR', 'KITCHEN'],
		],
		[
			'name' => 'Discussion verbs',
			'color' => 'var(--cg-green)',
			'words' => ['DEBATE', 'OUTLINE', 'QUESTION', 'SUMMARISE'],
		],
		[
			'name' => 'Likely near a college gate',
			'color' => 'var(--cg-blue)',
			'words' => ['BICYCLE', 'PORTER', 'SIGNPOST', 'VISITOR'],
		],
		[
			'name' => 'Pages in a book',
			'color' => 'var(--cg-purple)',
			'words' => ['APPENDIX', 'CHAPTER', 'PREFACE', 'SECTION'],
		],
	],
];

require_once(__DIR__ . '/../_connections.php');
