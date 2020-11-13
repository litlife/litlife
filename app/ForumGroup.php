<?php

namespace App;

use App\Enums\VariablesEnum;
use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;


/**
 * App\ForumGroup
 *
 * @property int $id
 * @property string $name
 * @property int $create_user_id
 * @property string|null $forum_sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $image_id forum_group.image_id
 * @property-read \App\User $create_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Forum[] $forums
 * @property-read \App\Image|null $image
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup any()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup newQuery()
 * @method static Builder|ForumGroup onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup orderBySettings()
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereForumSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereUpdatedAt($value)
 * @method static Builder|ForumGroup withTrashed()
 * @method static Builder|ForumGroup withoutTrashed()
 * @mixin Eloquent
 */
class ForumGroup extends Model
{
    use SoftDeletes;
    use UserCreate;

    protected $fillable = [
        'name'
    ];

    public function scopeAny($query)
    {
        return $query->withTrashed();
    }

    public function forums()
    {
        return $this->hasMany('App\Forum');
    }

    public function scopeOrderBySettings($query)
    {
        if (!empty($this->getSort())) {
            return $query->orderByField('id', $this->getSort());
        } else {
            return $query;
        }
    }

    public function getSort()
    {
        $order = optional(Variable::where('name', VariablesEnum::getValue('ForumGroupSort'))
            ->first())
            ->value;

        return $order;
    }

    public function image()
    {
        return $this->hasOne('App\Image', 'id', 'image_id');
    }
}
