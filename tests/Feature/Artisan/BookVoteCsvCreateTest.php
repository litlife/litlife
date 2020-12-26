<?php

namespace Tests\Feature\Artisan;

use App\Author;
use App\Book;
use App\BookKeyword;
use App\BookVote;
use App\Enums\BookComplete;
use App\Genre;
use App\Jobs\Book\UpdateBookRating;
use App\User;
use App\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookVoteCsvCreateTest extends TestCase
{
    private $file = '/test.csv';
    private $disk = 'public';

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake($this->disk);

        BookVote::query()
            ->where('user_updated_at', '>=', now())
            ->delete();
    }

    public function testLine()
    {
        $create_user_data = UserData::factory()
            ->state([
                'favorite_genres' => 'Романы, Детективы'
            ]);

        $create_user = User::factory()
            ->male()
            ->has($create_user_data, 'data')
            ->state([
                'born_date' => '2010-02-03'
            ]);

        $book = Book::factory()
            ->lp_true()
            ->si_false()
            ->has(Author::factory()->count(2), 'writers')
            ->has(Genre::factory()->count(2))
            ->has(BookKeyword::factory()->count(2), 'book_keywords');

        $vote = BookVote::factory()
            ->for($book)
            ->for($create_user, 'create_user')
            ->create();

        $this->artisan('csv:book_vote', [
            'after_time' => $vote->user_updated_at,
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_rate' => 0,
            '--min_book_user_votes_count' => 0,
            '--min_book_rate_count' => 0
        ])->assertExitCode(0);

        $book = $vote->book;
        $user = $vote->create_user;

        UpdateBookRating::dispatch($book);

        $book->refresh();

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(2, count($lines));

        $array = str_getcsv($lines[1], ",");

        dump($lines[0]);
        dump($lines[1]);

        $this->assertEquals([
            'book_id', 'book_title', 'create_user_id', 'rate', 'create_user_gender', 'book_writers_genders',
            'book_category', 'male_vote_percent', 'create_user_born_year',
            'user_updated_at_timestamp', 'book_is_si', 'book_is_lp', 'book_ready_status',
            'create_user_favorite_genres'
        ], explode(',', $lines[0]));

        $this->assertEquals($vote->book->id, $array[0]);

        $title = $vote->book->title;
        $title = preg_replace('/(\,|\")/iu', ' ', $title);
        $title = preg_replace('/([[:space:]]+)/iu', ' ', $title);

        $this->assertEquals($title, $array[1]);
        $this->assertEquals($vote->create_user->id, $array[2]);
        $this->assertEquals($vote->vote, $array[3]);
        $this->assertEquals($vote->create_user->gender, $array[4]);

        $writers_genders = $book->writers->pluck('gender')->toArray();
        $book_genres_ids = $book->genres->pluck('name')->toArray();
        $book_keywords_ids = $book->book_keywords()->with('keyword')->get()->pluck('keyword.text')->toArray();

        $book_category = array_merge($book_genres_ids, $book_keywords_ids);

        sort($book_category);
        sort($writers_genders);

        $this->assertEquals(implode(',', $writers_genders), $array[5]);
        $this->assertEquals(implode(', ', $book_category), $array[6]);
        $this->assertEquals($vote->book->male_vote_percent, $array[7]);
        $this->assertEquals($vote->create_user->born_date->year, $array[8]);
        $this->assertEquals($vote->user_updated_at->timestamp, $array[9]);
        $this->assertEquals(intval($vote->book->is_si), $array[10]);
        $this->assertEquals(intval($vote->book->is_lp), $array[11]);
        $this->assertEquals(BookComplete::getValue($vote->book->ready_status), $array[12]);
        $this->assertEquals($user->data->favorite_genres, $array[13]);
    }

    public function testAfterTime()
    {
        $create_user = User::factory();

        $vote = BookVote::factory()
            ->create();

        $this->artisan('csv:book_vote', [
            'after_time' => $vote->user_updated_at->addMinute(),
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_rate' => 0,
            '--min_book_user_votes_count' => 0,
            '--min_book_rate_count' => 0
        ])->assertExitCode(0);

        $book = $vote->book;

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(1, count($lines));
    }

    public function testIfKeywordNotFound()
    {
        $create_user = User::factory()
            ->male();

        $book = Book::factory()
            ->has(Author::factory()->count(2), 'writers')
            ->has(Genre::factory()->count(2))
            ->has(BookKeyword::factory()->count(2), 'book_keywords');

        $vote = BookVote::factory()
            ->for($book)
            ->for($create_user, 'create_user')
            ->create();

        $book = $vote->book;

        $book->book_keywords()->first()->keyword->forceDelete();

        $keyword = $book->book_keywords()->has('keyword')->first()->keyword;

        $this->artisan('csv:book_vote', [
            'after_time' => $vote->user_updated_at,
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_rate' => 0,
            '--min_book_user_votes_count' => 0,
            '--min_book_rate_count' => 0
        ])->assertExitCode(0);

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(2, count($lines));

        $array = str_getcsv($lines[1], ",");

        $this->assertTrue(in_array($keyword->text, explode(', ', $array[6])));
    }

    public function testNotFoundIfMinRateGreater()
    {
        $create_user = User::factory();

        $vote = BookVote::factory()
            ->create(['vote' => 3]);

        $this->artisan('csv:book_vote', [
            'after_time' => $vote->user_updated_at,
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_rate' => 4,
            '--min_book_user_votes_count' => 0,
            '--min_book_rate_count' => 0
        ])->assertExitCode(0);

        $book = $vote->book;

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(1, count($lines));
    }

    public function testFoundIfMinRateLower()
    {
        $create_user = User::factory();

        $vote = BookVote::factory()
            ->create(['vote' => 4]);

        $this->artisan('csv:book_vote', [
            'after_time' => $vote->user_updated_at,
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_rate' => 3,
            '--min_book_user_votes_count' => 0,
            '--min_book_rate_count' => 0
        ])->assertExitCode(0);

        $book = $vote->book;

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(2, count($lines));
    }

    public function testUserBornDate()
    {
        $create_user = User::factory()
            ->male()
            ->state(['born_date' => null]);

        $vote = BookVote::factory()
            ->for($create_user, 'create_user')
            ->create();

        $this->artisan('csv:book_vote', [
            'after_time' => $vote->user_updated_at,
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_rate' => 0,
            '--min_book_user_votes_count' => 0,
            '--min_book_rate_count' => 0
        ])->assertExitCode(0);

        $book = $vote->book;

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(2, count($lines));

        $array = str_getcsv($lines[1], ",");

        $this->assertEquals($vote->book->male_vote_percent, $array[7]);
        $this->assertEquals('', $array[8]);
        $this->assertEquals($vote->user_updated_at->timestamp, $array[9]);
    }
}
