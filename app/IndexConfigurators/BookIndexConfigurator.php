<?php

namespace App\IndexConfigurators;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class BookIndexConfigurator extends IndexConfigurator
{
	use Migratable;

	// It's not obligatory to determine name. By default it'll be a snaked class name without `IndexConfigurator` part.
	protected $name = 'book_index';

	protected $settings = [
		'analysis' => [
			'analyzer' => [
				'edge_ngram_analyzer' => [
					'filter' => ['lowercase', 'edge_ngram'],
					'tokenizer' => 'standard'
				],
				'keyword_analyzer' => [
					'filter' => ['lowercase'],
					'tokenizer' => 'standard'
				]
			],
			'filter' => [
				'edge_ngram' => [
					'type' => 'edgeNGram',
					'min_gram' => 2,
					'max_gram' => 25,
					'token_chars' => ['letter', 'digit']
				]
			]
		]
	];
}