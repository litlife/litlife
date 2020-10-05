<?php

namespace App;

use App\Enums\UserAccountPermissionValues;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserAccountPermission
 *
 * @property int $user_id
 * @property int $write_on_the_wall
 * @property int $comment_on_the_wall
 * @property int $write_private_messages
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $view_relations
 * @method static Builder|UserAccountPermission newModelQuery()
 * @method static Builder|UserAccountPermission newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserAccountPermission query()
 * @method static Builder|Model void()
 * @method static Builder|UserAccountPermission whereCommentOnTheWall($value)
 * @method static Builder|UserAccountPermission whereCreatedAt($value)
 * @method static Builder|UserAccountPermission whereUpdatedAt($value)
 * @method static Builder|UserAccountPermission whereUserId($value)
 * @method static Builder|UserAccountPermission whereViewRelations($value)
 * @method static Builder|UserAccountPermission whereWriteOnTheWall($value)
 * @method static Builder|UserAccountPermission whereWritePrivateMessages($value)
 * @mixin Eloquent
 */
class UserAccountPermission extends Model
{
	public $possible_values = [
		'write_on_the_wall' => [
			UserAccountPermissionValues::me,
			UserAccountPermissionValues::friends,
			UserAccountPermissionValues::friends_and_subscribers,
			UserAccountPermissionValues::friends_and_subscriptions,
			UserAccountPermissionValues::everyone
		],
		'comment_on_the_wall' => [
			UserAccountPermissionValues::everyone,
			UserAccountPermissionValues::friends,
			UserAccountPermissionValues::friends_and_subscribers,
			UserAccountPermissionValues::friends_and_subscriptions,
			UserAccountPermissionValues::me,
		],
		'write_private_messages' => [
			UserAccountPermissionValues::everyone,
			UserAccountPermissionValues::friends,
			UserAccountPermissionValues::friends_and_subscribers,
			UserAccountPermissionValues::friends_and_subscriptions
		],
		'view_relations' => [
			UserAccountPermissionValues::everyone,
			UserAccountPermissionValues::friends,
			UserAccountPermissionValues::friends_and_subscribers,
			UserAccountPermissionValues::friends_and_subscriptions,
			UserAccountPermissionValues::me
		]
	];
	protected $primaryKey = 'user_id';
	protected $guarded = ['user_id'];
	protected $fillable = [
		'write_on_the_wall',
		'comment_on_the_wall',
		'write_private_messages',
		'view_relations' // кому можно видеть друзей, подписчиков или подписки
	];
	protected $attributes =
		[
			'write_on_the_wall' => UserAccountPermissionValues::everyone,
			'comment_on_the_wall' => UserAccountPermissionValues::everyone,
			'write_private_messages' => UserAccountPermissionValues::everyone,
			'view_relations' => UserAccountPermissionValues::everyone
		];

	/*
		public function setWriteOnTheWallAttribute($value)
		{
			if (in_array($value, $this->possible_values['write_on_the_wall']))
			{
				$this->attributes['write_on_the_wall'] = $value;
			}
		}

		public function setCommentOnTheWallAttribute($value)
		{
			if (in_array($value, $this->possible_values['comment_on_the_wall']))
			{
				$this->attributes['comment_on_the_wall'] = $value;
			}
		}

		public function setWritePrivateMessagesAttribute($value)
		{
			if (in_array($value, $this->possible_values['write_private_messages']))
			{
				$this->attributes['write_private_messages'] = $value;
			}
		}
	*/

	public function setAttribute($key, $value)
	{
		parent::setAttribute($key, $value);

		if (array_key_exists($key, $this->possible_values)) {

			if (in_array($value, $this->possible_values[$key])) {
				$this->attributes[$key] = $value;
			} else {
				$this->attributes[$key] = $this->original[$key];
			}
		}

		return $this;
	}
	/*
		public function getAttribute($key)
		{
			if (array_key_exists($key, $this->possible_values))
				return UserAccountPermissionValues::getKey($this->attributes[$key]);

			return $this->attributes[$key];
		}
		*/
}
