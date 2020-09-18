<?php

namespace App\Console\Commands\Refresh;

use App\Enums\CacheTags;
use App\Events\BookFilesCountChanged;
use App\Participation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RefreshAllParticipationsCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_participations_counters {limit=1000} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет счетчики всех участий в диалогах';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Participation::orderBy('user_id', 'asc')
			->orderBy('conversation_id', 'asc')
			->chunk($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					DB::transaction(function () use ($item) {
						$this->info('participation: ' . $item->user_id . ' ' . $item->conversation_id);

						$conversation = $item->conversation;

						$count = $conversation->messages()
							->whereDate('created_at', '>', '2019-03-11 00:00:00')
							->count();

						if (empty($count)) {

							$latest_message = $conversation->messages()
								->orderBy('id', 'desc')
								->limit(1)
								->get()
								->first();

							if (!empty($latest_message)) {
								$item->latest_seen_message_id = $latest_message->id;
							} else {
								$item->latest_seen_message_id = $item->latest_message_id;
							}

						} else {
							//echo ($count);
						}

						$item->updateNewMessagesCount();
						$item->updateLatestMessage();
						$item->save();
					});
				}
			});

		Cache::tags([CacheTags::NewPrivateMessagesCount])->flush();
	}
}
