<?php

namespace App\Console\Commands\Refresh;


use App\Topic;
use Illuminate\Console\Command;

class RefreshLatestTopicsAtBottom extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:latest_topics_at_bottom';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет кешированные последние активные темы';

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
		Topic::refreshLatestTopics();
	}
}
