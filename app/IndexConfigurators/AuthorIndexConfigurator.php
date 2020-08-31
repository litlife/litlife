<?php

namespace App\IndexConfigurators;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class AuthorIndexConfigurator extends IndexConfigurator
{
	use Migratable;

	// It's not obligatory to determine name. By default it'll be a snaked class name without `IndexConfigurator` part.
	protected $name = 'author_index';

	// You can specify any settings you want, for example, analyzers.
	protected $settings = [
		'analysis' => [
			'analyzer' =>
				array(
					'autocomplete' =>
						array(
							'tokenizer' => 'autocomplete',
							'filter' =>
								array(
									0 => 'lowercase',
								),
						),
					'autocomplete_search' =>
						array(
							'tokenizer' => 'lowercase',
						),
				),
			'tokenizer' =>
				array(
					'autocomplete' =>
						array(
							'type' => 'edge_ngram',
							'min_gram' => 2,
							'max_gram' => 10,
							'token_chars' =>
								array(
									0 => 'letter',
								),
						),
				)


			/*
			'filter' => [
				'autocomplete_filter' => [
					'type' => 'edge_ngram',
					'min_gram' => '1',
					'max_gram' => '20',
				]
			],
			'analyzer' => [
				'autocomplete' => [
					'type' => 'custom',
					'tokenizer' => 'standard',
					'filter' => [
						'lowercase',
						'autocomplete_filter'
					],
				]
			]
   */
			/*
			'tokenizer' => [
				'my_tokenizer' => [
					'type' => 'edge_ngram',
					'min_gram' => '2',
					'max_gram' => '10',
					'token_chars' => [
						"letter",
						"digit"
					]
				]
			],
			'analyzer' => [
				'my_analyzer' => [
					'tokenizer' => 'my_tokenizer'
				]
			]
			*/
		]
	];
}