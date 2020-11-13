<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Like
 *
 * @property int $id
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $create_user_id
 * @property string $ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\User $create_user
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $likeable
 * @method static \Illuminate\Database\Eloquent\Builder|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like newQuery()
 * @method static Builder|Like onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Like query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereUpdatedAt($value)
 * @method static Builder|Like withTrashed()
 * @method static Builder|Like withoutTrashed()
 * @mixin Eloquent
 */
class Like extends Model
{
    use SoftDeletes;
    use UserCreate;

    protected $fillable = [
        'user_id'
    ];

    protected $visible = [
        'id',
        'likeable_type',
        'likeable_id',
        'create_user_id',
        'created_at',
        'deleted_at'
    ];

    /**
     * Get all of the owning commentable models.
     */
    public function likeable()
    {
        return $this->morphTo()->any();
    }

    public function getLikeableTypeAttribute($value)
    {
        if (is_numeric($value)) {
            return intval($value);
        } else {
            return $value;
        }
    }
}
