<?php

namespace App\Console\Commands\Old;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OldFillOriginBookId extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:fill_origin_book_id {lower_id?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$lower_id = $this->argument('lower_id');

		DB::table('book_statuses')
			->whereNull('origin_book_id')
			->when(!empty($lower_id), function ($query) {
				$query->where('id', '<', '100');
			})
			->update(['origin_book_id' => DB::raw('book_id')]);

		DB::table('book_votes')
			->whereNull('origin_book_id')
			->when(!empty($lower_id), function ($query) {
				$query->where('id', '<', '100');
			})
			->update(['origin_book_id' => DB::raw('book_id')]);

		DB::table('book_keywords')
			->whereNull('origin_book_id')
			->when(!empty($lower_id), function ($query) {
				$query->where('id', '<', '100');
			})
			->update(['origin_book_id' => DB::raw('book_id')]);

		DB::table('comments')
			->whereNull('origin_commentable_id')
			->when(!empty($lower_id), function ($query) {
				$query->where('id', '<', '100');
			})
			->update(['origin_commentable_id' => DB::raw('commentable_id')]);
	}
}
