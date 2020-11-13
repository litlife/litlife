<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\AchievementUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $achievement_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $create_user_id
 * @property-read \App\Achievement|null $achievement
 * @property-read \App\User $create_user
 * @property-read \App\User $user
 * @method static Builder|AchievementUser newModelQuery()
 * @method static Builder|AchievementUser newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|AchievementUser query()
 * @method static Builder|Model void()
 * @method static Builder|AchievementUser whereAchievementId($value)
 * @method static Builder|AchievementUser whereCreateUserId($value)
 * @method static Builder|AchievementUser whereCreatedAt($value)
 * @method static Builder|AchievementUser whereCreator(\App\User $user)
 * @method static Builder|AchievementUser whereId($value)
 * @method static Builder|AchievementUser whereUpdatedAt($value)
 * @method static Builder|AchievementUser whereUserId($value)
 * @mixin Eloquent
 */
class AchievementUser extends Model
{
    protected $table = 'achievement_user';

    use UserCreate;

    public function achievement()
    {
        return $this->hasOne('App\Achievement', 'id', 'achievement_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
