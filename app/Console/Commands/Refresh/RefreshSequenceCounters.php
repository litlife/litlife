<?php

namespace App\Console\Commands\Refresh;

use App\Events\BookFilesCountChanged;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Sequence;
use Illuminate\Console\Command;

class RefreshSequenceCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:sequence_counters {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все счетчики серии';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$sequence = Sequence::any()->findOrFail($this->argument('id'));

		UpdateSequenceBooksCount::dispatch($sequence);

		$sequence->save();
	}
}
