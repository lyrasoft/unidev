<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

return [
	'providers' => [
		'unidev' => \Lyrasoft\Unidev\Provider\UnidevProvider::class
	],

	'console' => [
		'commands' => [
			'unidev' => \Lyrasoft\Unidev\Command\UnidevCommand::class
		]
	]
];
