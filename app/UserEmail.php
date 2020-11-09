<?php

namespace App;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * App\UserEmail
 *
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $confirm
 * @property string|null $deleted_at
 * @property bool $show_in_profile
 * @property bool $rescue
 * @property bool $notice
 * @property string|null $domain
 * @property bool|null $is_valid Соответствует ли адрес RFC
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserEmailToken[] $tokens
 * @property-read \App\User $user
 * @method static Builder|UserEmail confirmed()
 * @method static Builder|UserEmail confirmedOrUnconfirmed()
 * @method static Builder|UserEmail createdBeforeMoveToNewEngine()
 * @method static Builder|UserEmail email($email)
 * @method static Builder|UserEmail newModelQuery()
 * @method static Builder|UserEmail newQuery()
 * @method static Builder|UserEmail notNoticed()
 * @method static Builder|UserEmail notice()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserEmail query()
 * @method static Builder|UserEmail rescuing()
 * @method static Builder|UserEmail showedInProfile()
 * @method static Builder|UserEmail unconfirmed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|UserEmail whereConfirm($value)
 * @method static Builder|UserEmail whereCreatedAt($value)
 * @method static Builder|UserEmail whereDeletedAt($value)
 * @method static Builder|UserEmail whereDomain($value)
 * @method static Builder|UserEmail whereEmail($value)
 * @method static Builder|UserEmail whereId($value)
 * @method static Builder|UserEmail whereInEmails($emails)
 * @method static Builder|UserEmail whereIsValid($value)
 * @method static Builder|UserEmail whereNotice($value)
 * @method static Builder|UserEmail whereRescue($value)
 * @method static Builder|UserEmail whereShowInProfile($value)
 * @method static Builder|UserEmail whereUpdatedAt($value)
 * @method static Builder|UserEmail whereUserId($value)
 * @mixin Eloquent
 */
class UserEmail extends Model
{
	protected $fillable = [
		'email'
	];
	private $moveToNewEngineDate = '2019-03-12 00:00:00';

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function tokens()
	{
		return $this->hasMany('App\UserEmailToken');
	}

	public function scopeNotice($query)
	{
		return $query->where('notice', true);
	}

	public function scopeNotNoticed($query)
	{
		return $query->where('notice', false);
	}

	public function scopeConfirmed($query)
	{
		return $query->where('confirm', true);
	}

	public function scopeUnconfirmed($query)
	{
		return $query->where('confirm', false);
	}

	public function scopeConfirmedOrUnconfirmed($query)
	{
		return $query->where('confirm', true)
			->orWhere('confirm', false);
	}

	public function scopeRescuing($query)
	{
		return $query->where('rescue', true);
	}

	public function scopeShowedInProfile($query)
	{
		return $query->where('show_in_profile', true);
	}

	public function scopeEmail($query, $email)
	{
		$email = preg_quote($email);
		return $query->where('email', 'ilike', $email);
	}

	public function scopeWhereEmail($query, $email)
	{
		$email = preg_quote($email);
		return $query->where('email', 'ilike', mb_strtolower($email));
	}

	public function scopeWhereInEmails($query, $emails)
	{
		return $query->where(function ($query) use ($emails) {

			foreach ($emails as $email) {
				$email = preg_quote($email);
				$email = mb_strtolower($email);
				$query->orWhere('email', 'ilike', $email);
			}
		});
	}

	public function isConfirmed()
	{
		return (boolean)$this->confirm;
	}

	public function isRescue()
	{
		return (boolean)$this->rescue;
	}

	public function isNotice()
	{
		return (boolean)$this->notice;
	}

	public function isShowInProfile()
	{
		return (boolean)$this->show_in_profile;
	}

	public function setEmailAttribute($value)
	{
		$this->attributes['email'] = mb_strtolower($value);

		preg_match('/(.*)@(.*)/iu', $this->attributes['email'], $m);

		list(, $name, $domain) = $m;

		$this->attributes['domain'] = $domain;
	}

	public function getEmailAttribute($value)
	{
		return mb_strtolower($value);
	}

	public function isCreatedBeforeMoveToNewEngine()
	{
		$newEngineDate = Carbon::parse($this->moveToNewEngineDate);

		if ($this->created_at->isBefore($newEngineDate))
			return true;
		else
			return false;
	}

	public function scopeCreatedBeforeMoveToNewEngine($query)
	{
		return $query->where('created_at', '<', $this->moveToNewEngineDate);
	}

	public function getMoveToNewEngineDate()
	{
		return $this->moveToNewEngineDate;
	}

	public function confirmEmail()
	{
		DB::transaction(function () {

			UserEmail::email($this->email)
				->whereNotIn('id', [$this->id])
				->with('user')
				->each(function ($email) {
					// если есть другие копии подтвержденного ящика, то удаляем остальные
					//$other_email->delete();
					$email->confirm = false;
					$email->save();

					if (!empty($email->user)) {
						$email->user->refreshConfirmedMailboxCount();

						if ($email->user->emails()->confirmed()->count() < 1) {
							$email->user->setting->loginWithIdEnable();
						}

						$email->user->push();
					}
				});

			$this->confirm = true;
			$this->isValidRefresh();
			$this->save();

			if (!empty($this->user)) {
				$this->user->refreshConfirmedMailboxCount();
				$this->user->save();
			}
		});
	}

	public function isValid(): bool
	{
		$validator = Validator::make([
			'email' => $this->email
		], [
			'email' => 'email:rfc',
		]);

		if ($validator->fails()) {
			return false;
		} else {
			return true;
		}
	}

	public function isValidRefresh()
	{
		$this->is_valid = $this->isValid();
	}
}
