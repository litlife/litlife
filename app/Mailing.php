<?php

namespace App;

use App\Model as Model;

/**
 * App\Mailing
 *
 * @property int $id
 * @property string $email Почта
 * @property int|null $priority Приоритет отправки
 * @property string|null $name Имя пользователя
 * @property \Illuminate\Support\Carbon|null $sent_at Время отправки сообщения
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing sent()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing waited()
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Mailing extends Model
{
	protected $dates = [
		'created_at',
		'updated_at',
		'sent_at'
	];

	public function scopeWhereEmail($query, $email)
	{
		$email = preg_quote($email);

		return $query->where('email', 'ilike', mb_strtolower($email));
	}

	public function setEmailAttribute($s)
	{
		$this->attributes['email'] = mb_substr(trim(mb_strtolower($s)), 0, 100);
	}

	public function setNameAttribute($s)
	{
		$this->attributes['name'] = mb_substr(trim($s), 0, 255);
	}

	public function setPriorityAttribute($s)
	{
		$this->attributes['priority'] = intval($s);
	}

	public function scopeSent($query)
	{
		return $query->whereNotNull('sent_at');
	}

	public function scopeWaited($query)
	{
		return $query->whereNull('sent_at');
	}

	public function isSent()
	{
		return (boolean)$this->sent_at;
	}
}
