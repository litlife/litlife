<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteBookStatusIfUserDeleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:if_deleted_delete_book_statuses {days_passed=7} {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Комманда удаляет статусы книг пользователей, аккаунты которых удалены, спустя несколько дней';

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
     * @return int
     */
    public function handle()
    {
        $days = (int)$this->argument('days_passed');

        User::onlyTrashed()
            ->has('book_read_statuses')
            ->where('deleted_at', '<', now()->subDays($days))
            ->when(!empty($this->argument('user_id')), function ($query) {
                $query->where('id', '=', $this->argument('user_id'));
            })
            ->chunkById(10, function ($users) {
                foreach ($users as $user)
                    $this->item($user);
            });

        return 0;
    }

    /**
     * @param  User  $user
     * @return void
     */
    public function item(User $user)
    {
        if (!$user->trashed())
            throw new \LogicException('The user must be deleted');

        $user->book_read_statuses()->chunkById(100, function ($statuses) {
            foreach ($statuses as $status) {
                $status->delete();
            }
        });
    }
}
