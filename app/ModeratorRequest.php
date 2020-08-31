<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;


/**
 * App\ModeratorRequest
 *
 * @property int $id
 * @property int $author_id
 * @property int $user_id
 * @property string $type
 * @property string $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $checked_at
 * @property-read \App\Author $author
 * @property-read \App\User $user
 * @method static Builder|ModeratorRequest checked()
 * @method static Builder|ModeratorRequest newModelQuery()
 * @method static Builder|ModeratorRequest newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|ModeratorRequest query()
 * @method static Builder|ModeratorRequest unchecked()
 * @method static Builder|Model void()
 * @method static Builder|ModeratorRequest whereAuthorId($value)
 * @method static Builder|ModeratorRequest whereCheckedAt($value)
 * @method static Builder|ModeratorRequest whereCreatedAt($value)
 * @method static Builder|ModeratorRequest whereDeletedAt($value)
 * @method static Builder|ModeratorRequest whereId($value)
 * @method static Builder|ModeratorRequest whereText($value)
 * @method static Builder|ModeratorRequest whereType($value)
 * @method static Builder|ModeratorRequest whereUpdatedAt($value)
 * @method static Builder|ModeratorRequest whereUserId($value)
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
