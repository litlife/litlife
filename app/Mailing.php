<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Mailing
 *
 * @property int $id
 * @property string $email Почта
 * @property int|null $priority Приоритет отправки
 * @property string|null $name Имя пользователя
 * @property Carbon|null $sent_at Время отправки сообщения
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Mailing newModelQuery()
 * @method static Builder|Mailing newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Mailing query()
 * @method static Builder|Mailing sent()
 * @method static Builder|Model void()
 * @method static Builder|Mailing waited()
 * @method static Builder|Mailing whereCreatedAt($value)
 * @method static Builder|Mailing whereEmail($value)
 * @method static Builder|Mailing whereId($value)
 * @method static Builder|Mailing whereName($value)
 * @method static Builder|Mailing wherePriority($value)
 * @method static Builder|Mailing whereSentAt($value)
 * @method static Builder|Mailing whereUpdatedAt($value)
 * @mixin Eloquent
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
        $email = ilikeSpecialChars($email);

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
