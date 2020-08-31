<?php

namespace App\Console\Commands;

use App\Manager;
use Illuminate\Console\Command;

class DeleteManagersFromDeletedAuthors extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'managers:delete_if_author_deleted {latest_manager_id=0} {days_have_passed=31}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Удаляет редакторов и подтвержденных страниц авторов у удаленных авторов';
	protected $latest_manager_id;
	protected $days_have_passed;

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
		$this->latest_manager_id = $this->argument('latest_manager_id');
		$this->days_have_passed = $this->argument('days_have_passed');

		Manager::where('id', '>=', $this->latest_manager_id)
			->with(['manageable' => function ($query) {
				$query->any();
			}])
			->chunkById(100, function ($managers) {
				foreach ($managers as $manager)
					$this->item($manager);
			});
	}

	public function item(Manager $manager)
	{
		if (!$manager->manageable->trashed())
			return false;

		if ($manager->manageable->deleted_at > now()->subDays($this->days_have_passed))
			return false;

		$manager->delete();

		$this->info($manager->manageable->name . ' ' . $manager->manageable->id . ' удалена привязка id ' . $manager->id);

		return true;
	}
}
