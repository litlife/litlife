<?php

namespace App\Console\Commands\Fix;

use App\Book;
use App\Comment;
use Illuminate\Console\Command;

class CorrectionOfBookIDForComments extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fix:correction_of_book_id_for_comments {limit=1000} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Проверяет и исправляет book id для комментариев';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	protected $bar;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');
		$latestId = $this->argument('latest_id');

		$query = Comment::bookType()
			->with(['commentable', 'originCommentable'])
			->where('id', '>=', $latestId);

		$this->bar = $this->output->createProgressBar($query->count());

		$this->bar->start();

		$query->chunkById($limit, function ($items) {
			foreach ($items as $item) {
				try {
					$this->item($item);
					$this->info('Comment id ' . $item->id);

				} catch (\LogicException $exception) {

				}

				$this->bar->advance();
			}
		});

		$this->bar->finish();

		return 0;
	}

	public function item(Comment $comment)
	{
		if (!$comment->isBookType())
			throw new \LogicException('Сommentable must be a book');

		$commentable = $comment->commentable;
		$originCommentable = $comment->originCommentable;

		if (!$originCommentable instanceof Book)
			throw new \LogicException('Origin commentable must be instance of book');

		if (!$originCommentable->isInGroup())
			throw new \LogicException('Origin commentable must be in group');

		if ($originCommentable->isMainInGroup())
			throw new \LogicException('Origin commentable must be not main in group');

		$mainBook = $originCommentable->mainBook;

		if (!$mainBook instanceof Book)
			throw new \LogicException('Main book must be a book');

		if ($mainBook->is($commentable))
			throw new \LogicException('Main book must be not commentable');

		$comment->commentable()->associate($mainBook);
		$comment->save();

		return true;
	}
}
