<?php

namespace App\Console\Commands\Old;

use Illuminate\Console\Command;

class OldAll extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:all';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обрабатывает все старые данные в новые';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->one('to_new:time_columns');

		$this->one('to_new:genres_authors_sequences_to_new');
		$this->one('to_new:author_photos');
		$this->one('to_new:avatars');
		$this->one('to_new:book_access');
		$this->one('to_new:book_files');
		$this->one('to_new:book_connects');
		$this->one('to_new:book');
		$this->one('to_new:user_email');
		$this->one('to_new:fill_user_settings');
		$this->one('to_new:findAndFillSourceFiles');
		$this->one('to_new:similar_vote_info');
		$this->one('to_new:read_styles');
		$this->one('to_new:user_settings');
		$this->one('to_new:vote_info');
	}

	public function one($s)
	{
		$this->info($s);

		$this->call($s);
	}
}
