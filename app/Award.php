<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Award
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $create_user_id
 * @property-read User $create_user
 * @method static \Illuminate\Database\Eloquent\Builder|Award newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Award newQuery()
 * @method static Builder|Award onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Award query()
 * @method static \Illuminate\Database\Eloquent\Builder|Award searchPartWord($textOrArray)
 * @method static \Illuminate\Database\Eloquent\Builder|Award similaritySearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|Award void()
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereCreator(User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereUpdatedAt($value)
 * @method static Builder|Award withTrashed()
 * @method static Builder|Award withoutTrashed()
 * @mixin Eloquent
 */
class Award extends Model
{
    use SoftDeletes;
    use UserCreate;

    protected $fillable = ['title', 'description'];

    public function scopeVoid($query)
    {
        return $query;
    }

    public function scopeSimilaritySearch($query, $searchText)
    {
        $query->selectRaw("*, similarity(title, ?) AS rank", [$searchText]);

        $query->whereRaw("title % ?", [$searchText]);

        $query->orderBy("rank", 'desc');

        return $query;
    }

    public function scopeSearchPartWord($query, $textOrArray)
    {
        if (is_array($textOrArray)) {
            foreach ($textOrArray as $keyword) {
                $keywords[] = preg_quote(trim($keyword));
            }
            return $query->whereRaw('"title" ~* ?', ['(' . implode('|', $keywords) . ')']);
        } else {
            return $query->whereRaw('"title" ~* ?', ['' . preg_quote($textOrArray) . '']);
        }
    }
}
