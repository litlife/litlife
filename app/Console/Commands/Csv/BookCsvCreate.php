<?php

namespace App\Console\Commands\Csv;

use App\Book;
use App\BookVote;
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
                            {--columns=book_id,book_title,book_writers_genders,book_genres_ids,book_keywords_ids,male_vote_percent}
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
            ->when($this->columns->contains('book_genres_ids'), function ($query) {
                $query->with('genres');
            })
            ->when($this->columns->contains('book_keywords_ids'), function ($query) {
                $query->with('book_keywords.keyword');
            })
            ->when($this->after_time, function ($query) {
                $query->where('created_at', '>=', $this->after_time);
            });

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
        $title = $book->title;
        $title = preg_replace('/(\,|\")/iu', ' ', $title);
        $title = preg_replace('/([[:space:]]+)/iu', ' ', $title);

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
        sort($book_genres_ids);
        sort($book_keywords_ids);

        $array['book_id'] = $book->id;
        $array['book_title'] = '"'.$title.'"';

        if ($this->columns->contains('book_writers_genders'))
            $array['book_writers_genders'] = '"'.implode(',', $writers_genders).'"';

        if ($this->columns->contains('book_genres_ids'))
            $array['book_genres_ids'] = '"'.implode(',', $book_genres_ids).'"';

        if ($this->columns->contains('book_keywords_ids'))
            $array['book_keywords_ids'] = '"'.implode(',', $book_keywords_ids).'"';

        if ($this->columns->contains('male_vote_percent'))
            $array['male_vote_percent'] = $book->male_vote_percent;

        return $array;
    }
}
