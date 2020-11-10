<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Achievement
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $image_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $create_user_id
 * @property-read User $create_user
 * @property-read Image $image
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement newQuery()
 * @method static Builder|Achievement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement similaritySearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement void()
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereCreator(User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereUpdatedAt($value)
 * @method static Builder|Achievement withTrashed()
 * @method static Builder|Achievement withoutTrashed()
 * @mixin Eloquent
 */
class Achievement extends Model
{
    use SoftDeletes;
    use UserCreate;

    protected $fillable = ['title', 'description'];

    public function scopeVoid($query)
    {
        return $query;
    }

    public function image()
    {
        return $this->belongsTo('App\Image');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function scopeSimilaritySearch($query, $searchText)
    {
        $query->selectRaw("*, similarity(title, ?) AS rank", [$searchText]);

        $query->whereRaw("title % ?", [$searchText]);

        $query->orderBy("rank", 'desc');

        return $query;
    }
}
