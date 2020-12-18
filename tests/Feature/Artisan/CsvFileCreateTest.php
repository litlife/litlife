<?php

namespace Tests\Feature\Artisan;

use App\Author;
use App\Book;
use App\BookKeyword;
use App\BookVote;
use App\Genre;
use App\Jobs\Book\UpdateBookRating;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvFileCreateTest extends TestCase
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
        $create_user = User::factory()->male();

        $book = Book::factory()
            ->has(Author::factory()->count(2), 'writers')
            ->has(Genre::factory()->count(2))
            ->has(BookKeyword::factory()->count(2), 'book_keywords');

        $vote = BookVote::factory()
            ->for($book)
            ->for($create_user, 'create_user')
            ->create();

        $this->artisan('csv_file:create', [
            'after_time' => $vote->user_updated_at,
            '--disk' => $this->disk,
            '--file' => $this->file
        ])->assertExitCode(0);

        $book = $vote->book;

        UpdateBookRating::dispatch($book);

        $book->refresh();

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(2, count($lines));

        $array = str_getcsv($lines[1], ",");

        dump($lines[0]);
        dump($lines[1]);

        $this->assertEquals([
            'book_id', 'create_user_id', 'rate', 'create_user_gender', 'book_writers_genders', 'book_genres_ids', 'book_keywords_ids', 'male_vote_percent'
        ], explode(',', $lines[0]));

        $this->assertEquals($vote->book->id, $array[0]);
        $this->assertEquals($vote->create_user->id, $array[1]);
        $this->assertEquals($vote->vote, $array[2]);
        $this->assertEquals($vote->create_user->gender, $array[3]);

        $writers_genders = $book->writers->pluck('gender')->toArray();
        $book_genres_ids = $book->genres->pluck('id')->toArray();
        $book_keywords_ids = $book->book_keywords()->with('keyword')->get()->pluck('keyword.id')->toArray();

        sort($writers_genders);
        sort($book_genres_ids);
        sort($book_keywords_ids);

        $this->assertEquals(implode(',', $writers_genders), $array[4]);
        $this->assertEquals(implode(',', $book_genres_ids), $array[5]);
        $this->assertEquals(implode(',', $book_keywords_ids), $array[6]);
        $this->assertEquals($vote->book->male_vote_percent, $array[7]);
    }

    public function testAfterTime()
    {
        $create_user = User::factory();

        $vote = BookVote::factory()
            ->create();

        $this->artisan('csv_file:create', [
            'after_time' => $vote->user_updated_at->addMinute(),
            '--disk' => $this->disk,
            '--file' => $this->file
        ])->assertExitCode(0);

        $book = $vote->book;

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(1, count($lines));
    }
}
