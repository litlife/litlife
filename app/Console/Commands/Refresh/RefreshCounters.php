<?php

namespace App\Console\Commands\Refresh;

use App\AuthorSaleRequest;
use App\Book;
use App\BookFile;
use App\Comment;
use App\Events\BookFilesCountChanged;
use App\Manager;
use App\Post;
use App\SupportRequest;
use App\UserOnModeration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:counters';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все счетчики';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Cache::forever('authors_count_refresh', 'true');
		Cache::forever('books_count_refresh', 'true');
		Cache::forever('comments_count_refresh', 'true');
		Cache::forever('posts_count_refresh', 'true');
		Cache::forever('sequences_count_refresh', 'true');
		Cache::forever('users_count_refresh', 'true');
		Cache::forever('genres_count_refresh', 'true');

		Comment::flushCachedOnModerationCount();
		Post::flushCachedOnModerationCount();
		BookFile::flushCachedOnModerationCount();
		Book::flushCachedOnModerationCount();
		Comment::flushCachedOnModerationCount();
		Manager::flushCachedOnModerationCount();
		UserOnModeration::flushCachedCount();
		AuthorSaleRequest::flushCachedOnModerationCount();

		SupportRequest::flushNumberOfUnsolved();
		SupportRequest::flushNumberInProcess();
		SupportRequest::flushNumberOfSolved();
	}
}
