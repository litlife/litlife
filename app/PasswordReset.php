<?php

namespace App;

use App\Model as Model;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * App\PasswordReset
 *
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $token
 * @property string|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset notUsed()
 * @method static Builder|PasswordReset onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset token($s)
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUserId($value)
 * @method static Builder|PasswordReset withTrashed()
 * @method static Builder|PasswordReset withoutTrashed()
 * @mixin Eloquent
 */
class PasswordReset extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'user_id',
		'token',
		'email'
	];


	public static function boot()
	{
		static::Creating(function ($item) {

			do {
				$token = Str::random(32);
			} // Проверим, нет ли уже такого токена, если есть сгенерим заново
			while (PasswordReset::where('token', $token)->first());

			$item->token = $token;
		});

		parent::boot();
	}

	public function user()
	{
		return $this->belongsTo('App\User', 'user_id', 'id');
	}

	public function scopeToken($query, $s)
	{
		return $query->where('token', $s);
	}

	public function scopeWhereEmail($query, $email)
	{
		return $query->where('email', 'ilike', mb_strtolower($email));
	}

	public function used()
	{
		$this->used_at = Carbon::now();
		$this->save();
	}

	public function isUsed()
	{
		return (boolean)$this->used_at;
	}

	public function scopeNotUsed($query)
	{
		return $query->whereNull('used_at');
	}
}
