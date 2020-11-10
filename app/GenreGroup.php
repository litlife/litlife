<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * App\GenreGroup
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Genre[] $genres
 * @property-write mixed $name
 * @method static \Illuminate\Database\Eloquent\Builder|GenreGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GenreGroup newQuery()
 * @method static Builder|GenreGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|GenreGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|GenreGroup whereIdWithSlug($id)
 * @method static Builder|GenreGroup withTrashed()
 * @method static Builder|GenreGroup withoutTrashed()
 * @mixin Eloquent
 */
class GenreGroup extends Model
{
    use SoftDeletes;

    protected $table = 'genres_groups';

    public function genres()
    {
        return $this->hasMany('App\Genre')
            ->orderBy('name', 'asc');
    }

    public function getGenreArrayIds()
    {
        return $this->genres
            ->sortByDesc('book_count')
            ->pluck('id')
            ->toArray();
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
            return $query->where('id', $matches[1]);
        } else {
            return $query->whereNull('id');
        }
    }

    public function getIdWithSlug()
    {
        return trim($this->id . '-' . $this->slug, '-');
    }

}
