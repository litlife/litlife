<?php

namespace App\Console\Commands;

use App\Jobs\SupportQuestion\UpdateNumberInProgressQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfAnsweredQuestions;
use App\SupportQuestion;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class AutoSolveSupportQuestion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support_question:solve {days=7} {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command marks support questions as resolved if there is no response from the user and time has passed';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = $this->argument('days');
        $id = $this->argument('id');

        SupportQuestion::void()
            ->whereLastResponseNotByUser()
            ->where('last_message_created_at', '<', now()->subDays($days))
            ->whereStatus('ReviewStarts')
            ->when(!empty($id), function ($q) use ($id) {
                $q->where('id', $id);
            })
            ->chunk(100, function ($items) {
                foreach ($items as $item) {
                    $this->item($item);
                }
            });

        UpdateNumberOfAnsweredQuestions::dispatch();
        UpdateNumberInProgressQuestions::dispatch();
    }

    public function item(SupportQuestion $question)
    {
        $question->statusAccepted();
        $question->save();
    }
}
