<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookCover
 *
 * @property-read \App\User $create_user
 * @method static Builder|BookCover newModelQuery()
 * @method static Builder|BookCover newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookCover query()
 * @method static Builder|BookCover void()
 * @method static Builder|BookCover whereCreator(\App\User $user)
 * @mixin Eloquent
 */
class BookCover extends Model
{
    use UserCreate;

    public function scopeVoid($query)
    {
        return $query;
    }
}
