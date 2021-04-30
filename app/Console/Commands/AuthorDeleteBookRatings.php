<?php

namespace App\Console\Commands;

use App\Author;
use App\BookVote;
use App\Jobs\Author\UpdateAuthorRating;
use App\Jobs\Book\UpdateBookRating;
use App\Jobs\User\UpdateUserBookVotesCount;
use App\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuthorDeleteBookRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'author:delete_book_ratings {author_id} {older_than?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда удаляет все оценки книг определенного автора';

    private $author;
    private $olderThan;

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
        $this->author = Author::findOrFail($this->argument('author_id'));

        if (!empty($olderThan = $this->argument('older_than'))) {
            try {
                $this->olderThan = Carbon::parse($olderThan);
            } catch (InvalidFormatException $exception) {
                print($exception->getMessage());
                $this->error($exception->getMessage());
                return 1;
            }
        }

        DB::transaction(function () {
            $this->transaction();
        });

        return 0;
    }

    public function transaction()
    {
        $query = BookVote::void()
            ->join("book_authors", "book_authors.book_id", "=", "book_votes.book_id")
            ->where("book_authors.author_id", $this->author->id)
            ->when(!empty($this->olderThan), function ($query) {
                $query->where('book_votes.user_updated_at', '>=', $this->olderThan);
            })
            ->with('create_user');

        $query->chunkById(100, function ($ratings) {
            foreach ($ratings as $rating) {
                $this->item($rating);
            }

            $array = $ratings->pluck('create_user_id')->toArray();

            if (count($array) > 0) {
                $users = User::whereIn('id', $array)->get();

                foreach ($users as $user) {
                    UpdateUserBookVotesCount::dispatch($user);
                }
            }
        });

        $this->author->any_books()->chunkById(10, function ($books) {
            foreach ($books as $book) {
                UpdateBookRating::dispatch($book);
            }
        });

        UpdateAuthorRating::dispatch($this->author);
    }

    public function item(BookVote $rating)
    {
        $rating->delete();
    }
}
