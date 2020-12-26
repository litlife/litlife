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
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookCsvCreateTest extends TestCase
{
    private $file = '/test2.csv';
    private $disk = 'public';

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake($this->disk);

        Book::query()
            ->where('created_at', '>=', now())
            ->delete();
    }

    public function testLine()
    {
        $create_user = User::factory()->male();

        $book = Book::factory()
            ->has(Author::factory()->count(2), 'writers')
            ->has(Genre::factory()->count(2))
            ->has(BookKeyword::factory()->count(2), 'book_keywords')
            ->create();

        $this->artisan('csv:book', [
            'after_time' => $book->created_at,
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_book_user_votes_count' => 0
        ])->assertExitCode(0);

        UpdateBookRating::dispatch($book);

        $book->refresh();

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(2, count($lines));

        $array = str_getcsv($lines[1], ",");

        dump($lines[0]);
        dump($lines[1]);

        $this->assertEquals([
            'book_id', 'book_title', 'book_writers_genders', 'book_category', 'male_vote_percent',
            'book_is_si', 'book_is_lp', 'book_ready_status'
        ], explode(',', $lines[0]));

        $this->assertEquals($book->id, $array[0]);

        $title = preg_replace('/(\,|\")/iu', ' ', $book->title);
        $title = preg_replace('/([[:space:]]+)/iu', ' ', $title);
        
        $this->assertEquals($title, $array[1]);

        $writers_genders = $book->writers->pluck('gender')->toArray();
        $book_genres_ids = $book->genres->pluck('name')->toArray();
        $book_keywords_ids = $book->book_keywords()->with('keyword')->get()->pluck('keyword.text')->toArray();

        sort($writers_genders);

        $category = array_merge($book_genres_ids, $book_keywords_ids);

        sort($category);

        $this->assertEquals(implode(',', $writers_genders), $array[2]);
        $this->assertEquals(implode(', ', $category), $array[3]);
        $this->assertEquals('', $array[4]);
        $this->assertEquals(intval($book->is_si), $array[5]);
        $this->assertEquals(intval($book->is_lp), $array[6]);
        $this->assertEquals(BookComplete::getValue($book->ready_status), $array[7]);
    }

    public function testAfterTime()
    {
        $create_user = User::factory();

        $book = Book::factory()
            ->create();

        $this->artisan('csv:book', [
            'after_time' => $book->created_at->addMinute(),
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_book_user_votes_count' => 0
        ])->assertExitCode(0);

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(1, count($lines));
    }

    public function testIfKeywordNotFound()
    {
        $create_user = User::factory()->male();

        $book = Book::factory()
            ->has(Author::factory()->count(2), 'writers')
            ->has(Genre::factory()->count(2))
            ->has(BookKeyword::factory()->count(2), 'book_keywords')
            ->create();

        $book->book_keywords()->first()->keyword->forceDelete();

        $keyword = $book->book_keywords()->has('keyword')->first()->keyword;

        $this->artisan('csv:book', [
            'after_time' => $book->created_at,
            '--disk' => $this->disk,
            '--file' => $this->file,
            '--min_book_user_votes_count' => 0
        ])->assertExitCode(0);

        $content = Storage::disk($this->disk)->get($this->file);

        $lines = explode("\n", $content);

        $this->assertEquals(2, count($lines));

        $array = str_getcsv($lines[1], ",");

        $this->assertTrue(in_array($keyword->text, explode(', ', $array[3])));
    }
}
