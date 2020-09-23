<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Forum
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $old_create_time
 * @property int|null $create_user_id
 * @property int $topic_count
 * @property int $post_count
 * @property int|null $last_topic_id
 * @property int|null $last_post_id
 * @property int|null $forum_group_id
 * @property string|null $obj_type
 * @property int|null $obj_id
 * @property int $old_hide
 * @property int $old_hide_time
 * @property int $old_hide_user
 * @property int $min_message_count
 * @property bool $private
 * @property string|null $private_user_ids
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $user_edited_at Время когда пользователь отредактировал
 * @property bool|null $autofix_first_post_in_created_topics
 * @property bool|null $order_topics_based_on_fix_post_likes
 * @property bool $is_idea_forum
 * @property-read \App\User|null $create_user
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $forumable
 * @property-read \App\ForumGroup|null $group
 * @property-read \App\Post|null $last_post
 * @property-read \App\Topic|null $last_topic
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Topic[] $topics
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UsersAccessToForum[] $user_access
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users_with_access
 * @method static \Illuminate\Database\Eloquent\Builder|Forum any()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum fulltextSearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum newQuery()
 * @method static Builder|Forum onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Forum public ()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum query()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum void()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereAutofixFirstPostInCreatedTopics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereForumGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereIsIdeaForum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereLastPostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereLastTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereMinMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereObjId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereObjType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereOldCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereOldHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereOldHideTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereOldHideUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereOrderTopicsBasedOnFixPostLikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum wherePostCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum wherePrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum wherePrivateUserIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereTopicCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereUserEditedAt($value)
 * @method static Builder|Forum withTrashed()
 * @method static Builder|Forum withoutTrashed()
 * @mixin Eloquent
 */
class Forum extends Model
{
	use SoftDeletes;
	use UserCreate;

	protected $fillable = [
		'name',
		'description',
		'min_message_count',
		'private',
		'private_user_ids',
		'autofix_first_post_in_created_topics',
		'order_topics_based_on_fix_post_likes',
		'is_idea_forum'
	];

	protected $casts = [
		'private' => 'boolean',
		'autofix_first_post_in_created_topics' => 'boolean',
		'order_topics_based_on_fix_post_likes' => 'boolean',
		'is_idea_forum' => 'boolean'
	];

	public function scopeAny($query)
	{
		return $query->withTrashed();
	}

	public function scopeVoid($query)
	{
		return $query;
	}

	public function scopePublic($query)
	{
		return $query->where('private', false);
	}

	public function forumable()
	{
		return $this->morphTo('App\Author', 'obj_type', 'obj_id');
	}

	public function last_post()
	{
		return $this->hasOne('App\Post', 'id', 'last_post_id');
	}

	public function last_topic()
	{
		return $this->hasOne('App\Topic', 'id', 'last_topic_id');
	}

	public function posts()
	{
		return $this->hasMany('App\Post');
	}

	public function group()
	{
		return $this->belongsTo('App\ForumGroup', 'forum_group_id');
	}

	public function users_with_access()
	{
		return $this->belongsToMany('App\User', 'users_access_to_forums', 'forum_id', 'user_id');
	}

	public function user_access()
	{
		return $this->hasMany('App\UsersAccessToForum');
	}

	public function scopeFulltextSearch($query, $searchText)
	{
		$Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

		$s = '';

		if ($Ar) {
			$s = "to_tsvector('english', \"name\" )  ";
			$s .= " @@ to_tsquery('english', quote_literal(quote_literal(?)))";

			return $query->whereRaw($s, [implode('+', $Ar)]);
		}
	}

	public function isPrivate()
	{
		return (bool)$this->private;
	}

	public function isAutofixFirstPostInCreatedTopics()
	{
		return (bool)$this->autofix_first_post_in_created_topics;
	}

	public function isOrderTopicsBasedOnFixPostLikes()
	{
		return (bool)$this->order_topics_based_on_fix_post_likes;
	}

	public function isIdeaForum()
	{
		return (bool)$this->is_idea_forum;
	}

	public function topics()
	{
		return $this->hasMany('App\Topic');
	}

	public function postsCountRefresh()
	{
		$this->post_count = $this->topics()
			->sum('post_count');
	}

	public function topicsCountRefresh()
	{
		$this->topic_count = $this->topics()
			->count();
	}

	public function lastPostRefresh()
	{
		$topic = $this->topics()
			->orderByWithNulls('last_post_created_at', 'desc', 'last')
			->first();

		if (!empty($topic)) {
			if (!empty($topic->last_post)) {
				$this->last_post_id = $topic->last_post->id;
			}

			$this->last_topic_id = $topic->id;
		} else {
			$this->last_post_id = null;
			$this->last_topic_id = null;
		}
	}

	public function hasUserInAccess(User $user): bool
	{
		if (empty($this->user_access))
			return false;

		return (bool)$this->user_access->where('user_id', $user->id)->first();
	}
}
