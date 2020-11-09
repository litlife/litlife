<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\AdminNote
 *
 * @property int $id
 * @property string $admin_noteable_type
 * @property int $admin_noteable_id
 * @property string|null $text
 * @property int|null $create_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $user_edited_at Время когда пользователь отредактировал
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $admin_noteable
 * @property-read \App\User|null $create_user
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote newQuery()
 * @method static Builder|AdminNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereAdminNoteableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereAdminNoteableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereUserEditedAt($value)
 * @method static Builder|AdminNote withTrashed()
 * @method static Builder|AdminNote withoutTrashed()
 * @mixin Eloquent
 */
class AdminNote extends Model
{
	use SoftDeletes;
	use UserCreate;

	protected $fillable = [
		'text'
	];

	protected $dates = [
		'user_edited_at'
	];

	public function admin_noteable()
	{
		return $this->morphTo();
	}
}
