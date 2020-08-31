<?php

namespace App\Console\Commands;

use App\Notifications\SendingInvitationToTakeSurveyNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SendInvitationToTakeSurvey extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'survey:send_invitations 
	{onlyUsersWhoRegisteredLaterThanTheDate? : Выбрать пользователей только с датой регистрации позже указанной} 
	{daysPassedSinceDateRegistration=7 : Минимум дней прошло со дня регистстрации пользователя}
	{count=10 : Количество пользователей выбранных за одно выполнение комманды}
	';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Отправляем приглашение пройти опрос';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	private $onlyUsersWhoRegisteredLaterThanTheDate;
	private $daysPassedSinceDateRegistration;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		if (!empty($this->argument('onlyUsersWhoRegisteredLaterThanTheDate')))
			$this->onlyUsersWhoRegisteredLaterThanTheDate = Carbon::parse($this->argument('onlyUsersWhoRegisteredLaterThanTheDate'));

		$this->daysPassedSinceDateRegistration = $this->argument('daysPassedSinceDateRegistration');

		User::whereHas('data', function (Builder $query) {
			$query->whereNull('invitation_to_take_survey_has_been_sent')
				->orWhere('invitation_to_take_survey_has_been_sent', false);
		})
			->doesntHave('surveys')
			->when(!empty($this->onlyUsersWhoRegisteredLaterThanTheDate), function ($query) {
				$query->where('created_at', '>=', $this->onlyUsersWhoRegisteredLaterThanTheDate);
			})
			->where('created_at', '<=', now()->subDays($this->daysPassedSinceDateRegistration))
			->with(['data', 'surveys'])
			->chunkById($this->argument('count'), function ($users) {
				foreach ($users as $user) {
					DB::transaction(function () use ($user) {
						$this->sendInvitation($user);
					});
				}
			});
	}

	public function sendInvitation(User $user)
	{
		if ($user->data->invitation_to_take_survey_has_been_sent)
			// приглашение уже отправлено
			return false;

		if ($user->surveys->count())
			// пользователь уже прошел опрос
			return false;

		if (!empty($this->onlyUsersWhoRegisteredLaterThanTheDate)) {
			if ($user->created_at < $this->onlyUsersWhoRegisteredLaterThanTheDate)
				// пользователь зарегистрировался позже указанной даты
				return false;
		}

		if ($user->created_at->addDays($this->daysPassedSinceDateRegistration)->isFuture())
			// со дня регистрации еще не прошло указанное количество дней
			return false;

		$user->notify(new SendingInvitationToTakeSurveyNotification($user));

		$user->data->invitation_to_take_survey_has_been_sent = true;
		$user->push();
	}
}
