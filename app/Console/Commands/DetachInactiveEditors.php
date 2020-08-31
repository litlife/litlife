<?php

namespace App\Console\Commands;

use App\Manager;
use Illuminate\Console\Command;

class DetachInactiveEditors extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'managers:delete_inactive_editors {months_have_passed_since_the_last_visit=12} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Отсоединяет неактивных редакторов авторов';
	private $months;

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
		$this->months = $this->argument('months_have_passed_since_the_last_visit');

		Manager::editors()
			->with('user')
			->where('id', '>=', $this->argument('latest_id'))
			->whereHas('user', function ($query) {
				$query->where('last_activity_at', '<', now()->subMonths($this->months));
			})
			->chunkById(100, function ($managers) {
				foreach ($managers as $manager)
					$this->item($manager);
			});
	}

	public function item(Manager $manager)
	{
		if (!$manager->isEditorCharacter())
			return false;

		if ($manager->user->last_activity_at > now()->subMonths($this->months))
			return false;

		$manager->delete();

		echo("manager: " . $manager->id . "\n");

		return true;
	}
}
