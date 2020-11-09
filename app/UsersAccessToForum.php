<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\UsersAccessToForum
 *
 * @property int $user_id
 * @property int $forum_id
 * @method static Builder|UsersAccessToForum newModelQuery()
 * @method static Builder|UsersAccessToForum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UsersAccessToForum query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|UsersAccessToForum whereForumId($value)
 * @method static Builder|UsersAccessToForum whereUserId($value)
 * @mixin Eloquent
 */
class UsersAccessToForum extends Model
{
	public $timestamps = false;
	public $incrementing = false;
	protected $primaryKey = null;
}
