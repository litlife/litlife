<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;


/**
 * App\ModeratorRequest
 *
 * @property-read Author $author
 * @property-read User $user
 * @method static Builder|ModeratorRequest checked()
 * @method static Builder|ModeratorRequest newModelQuery()
 * @method static Builder|ModeratorRequest newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|ModeratorRequest query()
 * @method static Builder|ModeratorRequest unchecked()
 * @method static Builder|Model void()
 * @mixin Eloquent
 */
class ModeratorRequest extends Model
{


    /**
     * Только проверенные
     */
    public function scopeChecked($query)
    {
        return $query->whereNotNull('status_changed_at');
    }

    /**
     * Только не проверенные
     */
    public function scopeUnchecked($query)
    {
        return $query->whereNull('status_changed_at');
    }


    function author()
    {
        return $this->belongsTo('App\Author');
    }

    function user()
    {
        return $this->belongsTo('App\User');
    }
}
