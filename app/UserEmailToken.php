<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserEmailToken
 *
 * @property int $user_email_id
 * @property string $token
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property-read UserEmail $email
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken newQuery()
 * @method static Builder|UserEmailToken onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereUserEmailId($value)
 * @method static Builder|UserEmailToken withTrashed()
 * @method static Builder|UserEmailToken withoutTrashed()
 * @mixin Eloquent
 */
class UserEmailToken extends Model
{
    use SoftDeletes;

    public $incrementing = true;
    protected $primaryKey = 'id';

    function email()
    {
        return $this->belongsTo('App\UserEmail', 'user_email_id');
    }
}
