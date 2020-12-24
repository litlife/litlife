<?php

namespace App\Console\Commands\Csv;

use App\BookVote;
use App\Enums\BookComplete;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class BookVoteCsvCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:book_vote 
                            {after_time?}
                            {--disk=public}
                            {--file=/datasets/book_votes_dataset.csv}
                            {--batch=5000}
                            {--columns=book_id,create_user_id,rate,create_user_gender,book_writers_genders,book_genres_ids,book_keywords_ids,male_vote_percent,create_user_born_year,user_updated_at_timestamp,book_is_si,book_is_lp,book_ready_status}
                            {--min_book_user_votes_count=5}
                            {--min_book_rate_count=5}
                            {--min_rate=4}';

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

        $query = BookVote::query()
            ->when($this->columns->contains('book_writers_genders'), function ($query) {
                $query->with('book.writers');
            })
            ->when($this->columns->contains('book_genres_ids'), function ($query) {
                $query->with('book.genres');
            })
            ->when($this->columns->contains('book_keywords_ids'), function ($query) {
                $query->with('book.book_keywords.keyword');
            })
            ->when($this->after_time, function ($query) {
                $query->where('user_updated_at', '>=', $this->after_time);
            })
            ->whereHas('book', function (Builder $query) {
                $query->where('user_vote_count',
                    '>=', $this->option('min_book_user_votes_count'));
            })
            ->whereHas('create_user', function (Builder $query) {
                $query->where('book_rate_count',
                    '>=', $this->option('min_book_rate_count'));
            })
            ->where('vote', '>', $this->option('min_rate'));

        $count = $query->count();

        $this->line('Count: '.$count);

        $bar = $this->output->createProgressBar($count);

        $bar->start();

        $query->with(['create_user', 'book'])
            ->chunkById($this->batch, function ($votes) use (&$bar) {

                $array = [];

                foreach ($votes as $vote) {
                    $array[] = $this->handleLine($vote);

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

    private function handleLine(BookVote $vote) :string
    {
        $array = $this->getArray($vote);

        return implode(',', $array);
    }

    private function getArray(BookVote $vote) :array
    {
        //$book_title = $vote->book->title;
        //$book_title = preg_replace('/(\,|\")/iu', ' ', $book_title);
        //$book_title = preg_replace('/([[:space:]]+)/iu', ' ', $book_title);

        $writers_genders = [];
        $book_genres_ids = [];
        $book_keywords_ids = [];

        foreach ($vote->book->writers as $writer)
            $writers_genders[] = $writer->gender;

        foreach ($vote->book->genres as $genre)
            $book_genres_ids[] = $genre->id;

        foreach ($vote->book->book_keywords as $keyword)
        {
            if (!empty($keyword->keyword))
            {
                $book_keywords_ids[] = $keyword->keyword->id;
            }
        }

        sort($writers_genders);
        sort($book_genres_ids);
        sort($book_keywords_ids);

        $array['book_id'] = $vote->book_id;
        $array['create_user_id'] = $vote->create_user->id;
        $array['rate'] = $vote->vote;

        if ($this->columns->contains('create_user_gender'))
        {
            if (optional($vote->create_user)->gender)
                $array['create_user_gender'] = $vote->create_user->gender;
        }

        if ($this->columns->contains('book_writers_genders'))
            $array['book_writers_genders'] = '"'.implode(',', $writers_genders).'"';

        if ($this->columns->contains('book_genres_ids'))
            $array['book_genres_ids'] = '"'.implode(',', $book_genres_ids).'"';

        if ($this->columns->contains('book_keywords_ids'))
            $array['book_keywords_ids'] = '"'.implode(',', $book_keywords_ids).'"';

        if ($this->columns->contains('male_vote_percent'))
            if ($vote->book->male_vote_percent !== null)
                $array['male_vote_percent'] = round($vote->book->male_vote_percent, 1);
            else
                $array['male_vote_percent'] = '';

        if ($this->columns->contains('create_user_born_year'))
        {
            $array['create_user_born_year'] = optional($vote->create_user->born_date)->year;
        }

        if ($this->columns->contains('user_updated_at_timestamp'))
            $array['user_updated_at_timestamp'] = $vote->user_updated_at->timestamp;

        if ($this->columns->contains('book_is_si'))
            $array['book_is_si'] = intval($vote->book->is_si);

        if ($this->columns->contains('book_is_lp'))
            $array['book_is_lp'] = intval($vote->book->is_lp);

        if ($this->columns->contains('book_ready_status'))
        {
            if (BookComplete::hasKey($vote->book->ready_status))
                $array['book_ready_status'] = BookComplete::getValue($vote->book->ready_status);
            else
                $array['book_ready_status'] = '';
        }

        return $array;
    }
}
