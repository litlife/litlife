<?php

namespace App\Console\Commands\Csv;

use App\Book;
use App\BookVote;
use App\Enums\BookComplete;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class BookCsvCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:book
                            {after_time?}
                            {--disk=public}
                            {--file=/datasets/book_dataset.csv}
                            {--batch=5000}
                            {--columns=book_id,book_title,book_writers_genders,book_category,male_vote_percent,book_is_si,book_is_lp,book_ready_status,book_lang}
                            {--min_book_user_votes_count=5}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $after_time;
    private $disk;
    private $file;
    private $batch;
    private $columns;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->after_time = $this->argument('after_time');
        $this->disk = $this->option('disk');
        $this->file = $this->option('file');
        $this->batch = $this->option('batch');
        $this->columns = collect(explode(',', $this->option('columns')));

        Storage::disk($this->disk)
            ->delete($this->file);

        Storage::disk($this->disk)
            ->append($this->file, implode(',', $this->columns->toArray()));

        $query = Book::query()
            ->when($this->columns->contains('book_writers_genders'), function ($query) {
                $query->with('writers');
            })
            ->when($this->columns->contains('book_category'), function ($query) {
                $query->with('genres')
                    ->with('book_keywords.keyword');
            })
            ->when($this->after_time, function ($query) {
                $query->where('created_at', '>=', $this->after_time);
            })
            ->where('user_vote_count', '>=', $this->option('min_book_user_votes_count'));

        $count = $query->count();

        $this->line('Count: '.$count);

        $bar = $this->output->createProgressBar($count);

        $bar->start();

        $query->chunkById($this->batch, function ($books) use (&$bar) {

                $array = [];

                foreach ($books as $book) {
                    $array[] = $this->handleLine($book);

                    $bar->advance();
                }

                $output = implode("\n", $array);

                Storage::disk($this->disk)
                    ->append($this->file, $output);
            });

        $bar->finish();

        $this->line('');
        $this->line('File created '.Storage::disk($this->disk)->url($this->file));

        return 0;
    }

    private function handleLine(Book $book) :string
    {
        $array = $this->getArray($book);

        return implode(',', $array);
    }

    private function getArray($book) :array
    {
        $writers_genders = [];
        $book_genres_ids = [];
        $book_keywords_ids = [];

        foreach ($book->writers as $writer)
            $writers_genders[] = $writer->gender;

        foreach ($book->genres as $genre)
            $book_genres_ids[] = $genre->id;

        foreach ($book->book_keywords as $keyword)
        {
            if (!empty($keyword->keyword))
            {
                $book_keywords_ids[] = $keyword->keyword->id;
            }
        }

        sort($writers_genders);

        $array['book_id'] = $book->id;

        if ($this->columns->contains('book_title'))
        {
            $title = mb_strtolower($book->title_author_search_helper);
            $title = preg_replace('/(\,|\")/iu', ' ', $title);
            $title = preg_replace('/([[:space:]]+)/iu', ' ', $title);
            $array['book_title'] = '"'.$title.'"';
        }

        if ($this->columns->contains('book_writers_genders'))
            $array['book_writers_genders'] = '"'.implode(',', $writers_genders).'"';

        if ($this->columns->contains('book_category'))
        {
            $categories = $book->genres->pluck('name')->map(function ($name) {
                return preg_replace('/(\,|\")/iu', ' ', $name);
            });

            $keywords = $book->book_keywords->pluck('keyword.text')->map(function ($name) {
                return preg_replace('/(\,|\")/iu', ' ', $name);
            });

            $categories = $categories
                ->merge($keywords)
                ->map(function ($text) {
                    return mb_strtolower($text);
                })
                ->unique()
                ->sort();

            $array['book_category'] = '"'.implode(', ', $categories->toArray()).'"';
        }

        if ($this->columns->contains('male_vote_percent'))
        {
            if ($book->male_vote_percent !== null)
                $array['male_vote_percent'] = round($book->male_vote_percent, 1);
            else
                $array['male_vote_percent'] = '';
        }

        if ($this->columns->contains('book_is_si'))
            $array['book_is_si'] = intval($book->is_si);

        if ($this->columns->contains('book_is_lp'))
            $array['book_is_lp'] = intval($book->is_lp);

        if ($this->columns->contains('book_ready_status'))
        {
            if (BookComplete::hasKey($book->ready_status))
                $array['book_ready_status'] = BookComplete::getValue($book->ready_status);
            else
                $array['book_ready_status'] = '';
        }

        if ($this->columns->contains('book_lang')) {
            $array['book_lang'] = $book->ti_lb;
        }

        return $array;
    }
}
