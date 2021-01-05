<?php

namespace App;

use App\Model as Model;
use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


/**
 * App\Genre
 *
 * @property int $id
 * @property string $name
 * @property string|null $fb_code
 * @property int $book_count
 * @property int $age
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $slug Слаг
 * @property int|null $genre_group_id Старый ID главного жанра
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
 * @property-read \Illuminate\Database\Eloquent\Collection|Genre[] $childGenres
 * @property-read Genre|null $group
 * @method static \Illuminate\Database\Eloquent\Builder|Genre main()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre notMain()
 * @method static Builder|Genre onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Genre parseIds($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre query()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre rememberCount($minutes = 5, $refresh = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre search($searchText)
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereBookCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereFbCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereGenreGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereIdWithSlug($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereUpdatedAt($value)
 * @method static Builder|Genre withTrashed()
 * @method static Builder|Genre withoutTrashed()
 * @mixin Eloquent
 * @property int|null $old_genre_group_id Старый ID главного жанра
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereOldGenreGroupId($value)
 */
class Genre extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'genre_group_id',
        'name',
        'fb_code',
        'age'
    ];

    static function cachedCountRefresh()
    {
        Cache::forever('genres_count_refresh', true);
    }

    public function books()
    {
        return $this->belongsToMany('App\Book', 'book_genres');
    }

    public function scopeSearch($query, $searchText)
    {
        if ($searchText) {
            //$query->where('sg_name', '~*', "'^".preg_quote($searchText)."'");

            //$query->where('sg_name', 'ilike', $searchText.'%');

            $query->whereRaw('"name" ~* ?', [preg_quote($searchText)]);
        }

        return $query;
    }

    public function scopeRememberCount($query, $minutes = 5, $refresh = false)
    {
        if ($refresh) {
            Cache::forget('genre_count');
        }

        return Cache::remember('genre_count', $minutes, function () use ($query) {
            return $query->notMain()->count();
        });
    }

    public function scopeParseIds($query, $ids)
    {
        if (is_string($ids)) {
            if (preg_match('/,/iu', $ids)) {
                $ids = explode(',', $ids);
                array_walk($ids, function (&$value) {
                    $value = pg_intval(intval($value));
                });
                $query->whereIn('id', $ids);
            } else {
                $query->where('id', pg_intval(intval($ids)));
            }

        } elseif (is_array($ids)) {
            array_walk($ids, function (&$value) {
                $value = pg_intval(intval($value));
            });
            $query->whereIn('id', $ids);
        }

        return $query;
    }

    public function setNameAttribute($s)
    {
        $this->attributes['name'] = trim($s);
        $this->attributes['slug'] = Str::slug($this->name, '-');
    }

    public function scopeWhereIdWithSlug($query, $id)
    {
        preg_match('/^([0-9]+)/iu', $id, $matches);

        if (!empty($matches[1])) {
            $id = pg_intval($matches[1]);
            return $query->where('id', $id);
        } else {
            return $query->whereNull('id');
        }
    }

    public function getIdWithSlug()
    {
        return trim($this->id . '-' . $this->slug, '-');
    }

    public function scopeMain($query)
    {
        return $query->whereNull('genre_group_id');
    }

    public function scopeNotMain($query)
    {
        return $query->whereNotNull('genre_group_id');
    }

    public function childGenres()
    {
        return $this->hasMany('App\Genre', 'genre_group_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Genre', 'genre_group_id', 'id');
    }

    public function isMain()
    {
        return !(boolean)$this->genre_group_id;
    }

    public function setAgeAttribute($s)
    {
        if (empty($s)) {
            $s = 0;
        }

        $this->attributes['age'] = intval($s);
    }
}
