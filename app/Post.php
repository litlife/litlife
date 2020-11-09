<?php

namespace App;

use App\Enums\CacheTags;
use App\Enums\StatusEnum;
use App\Model as Model;
use App\Scopes\CheckedScope;
use App\Traits\BBCodeable;
use App\Traits\CharactersCountTrait;
use App\Traits\CheckedItems;
use App\Traits\ExternalLinks;
use App\Traits\LatestOldestWithIDTrait;
use App\Traits\Likeable;
use App\Traits\NestedItems;
use App\Traits\UserAgentTrait;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Post
 *
 * @property int $id
 * @property int $topic_id
 * @property string $bb_text
 * @property string $html_text
 * @property int $create_user_id
 * @property int|null $edit_user_id
 * @property string|null $tree
 * @property int $children_count
 * @property int $like_count
 * @property string|null $ip
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $forum_id
 * @property Carbon|null $user_edited_at Время когда пользователь отредактировал
 * @property bool $private
 * @property int $level
 * @property bool $external_images_downloaded
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property int|null $user_agent_id
 * @property int|null $characters_count
 * @property-read \App\Like|null $authUserLike
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
 * @property-read \App\User $create_user
 * @property-read \App\User|null $edit_user
 * @property-read \App\Forum|null $forum
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read mixed $level_with_limit
 * @property Post|null $parent
 * @property-read mixed $root
 * @property-read mixed $text
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
 * @property-write mixed $b_b_text
 * @property-read \App\User|null $status_changed_user
 * @property-read \App\Topic $topic
 * @property-read \App\UserAgent|null $user_agent
 * @method static \Illuminate\Database\Eloquent\Builder|Post accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Post any()
 * @method static \Illuminate\Database\Eloquent\Builder|Post checked()
 * @method static \Illuminate\Database\Eloquent\Builder|Post checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Post checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Post childs($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Post descendants($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Post fulltextSearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|Post latestWithId($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post oldestWithId($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|Post onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Post onlyChecked()
 * @method static Builder|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Post orDescendants($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Post orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Post orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Post private ()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post roots()
 * @method static \Illuminate\Database\Eloquent\Builder|Post sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Post unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Post unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereBbText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCharactersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereEditUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereExternalImagesDownloaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHtmlText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserEditedAt($value)
 * @method static Builder|Post withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Post withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Post withUserAccessToForums()
 * @method static \Illuminate\Database\Eloquent\Builder|Post withoutCheckedScope()
 * @method static Builder|Post withoutTrashed()
 * @mixin Eloquent
 */
class Post extends Model
{
	use SoftDeletes;
	use UserCreate;
	use CheckedItems;
	use NestedItems;
	use LatestOldestWithIDTrait;
	use UserAgentTrait;
	use Likeable;
	use CharactersCountTrait;
	use ExternalLinks;
	use BBCodeable;

	public $rules = [
		'bb_text' => 'required'
	];

	protected $attributes = [
		'status' => StatusEnum::Accepted
	];

	protected $fillable = [
		'bb_text'
	];

	protected $except = [
		'ip'
	];

	protected $dates = [
		'user_edited_at'
	];

	protected $appends = ['text'];

	protected $perPage = 10;

	const BB_CODE_COLUMN = 'bb_text';
	const HTML_COLUMN = 'html_text';

	public static function boot()
	{
		parent::boot();

		//static::addGlobalScope(new CheckedScope);
		//static::addGlobalScope(new NotConnectedScope);
	}

	static function getCachedOnModerationCount()
	{
		return Cache::tags([CacheTags::PostsOnModerationCount])->remember('count', 3600, function () {
			return self::sentOnReview()->count();
		});
	}

	static function flushCachedOnModerationCount()
	{
		Cache::tags([CacheTags::PostsOnModerationCount])->pull('count');
	}

	static function cachedCountRefresh()
	{
		Cache::forever('posts_count_refresh', true);
	}

	public function scopeAny($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class)->withTrashed();
	}

	public function scopeFulltextSearch($query, $searchText)
	{
		$Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

		$s = '';

		if ($Ar) {
			$s = "to_tsvector('english', \"html_text\" )  ";
			$s .= " @@ to_tsquery('english', quote_literal(quote_literal(?)))";

			return $query->whereRaw($s, [implode('+', $Ar)]);
		}
	}

	public function create_user()
	{
		return $this->belongsTo('App\User', $this->getCreateUserIdColumn(), 'id')
			->any()
			->with("avatar");
	}

	public function topic()
	{
		return $this->belongsTo('App\Topic')->any();
	}

	public function forum()
	{
		return $this->belongsTo('App\Forum')->any();
	}

	public function edit_user()
	{
		return $this->hasOne('App\User', 'id', 'edit_user_id');
	}

	public function complaints()
	{
		return $this->morphMany('App\Complain', 'complainable');
	}

	public function parent()
	{
		return $this->hasOne('App\Post', 'id', 'edit_user_id');
	}

	public function setBBTextAttribute($value)
	{
		$this->setBBCode($value);
		$this->attributes['external_images_downloaded'] = false;
		$this->refreshCharactersCount();
	}

	public function setHtmlTextAttribute($value)
	{
		$this->setHtml($value);
		$this->attributes['external_images_downloaded'] = false;
		$this->refreshCharactersCount();
	}

	public function getTextAttribute()
	{
		$value = $this->attributes['html_text'];

		$value = preg_replace_callback("/((?:<\\/?\\w+)(?:\\s+\\w+(?:\\s*=\\s*(?:\\\".*?\\\"|'.*?'|[^'\\\">\\s]+)?)+\\s*|\\s*)\\/?>)([^<]*)?/", function ($matches) {
			return $matches[1] . str_replace("  ", "&#160; ", $matches[2]);
		}, $value);

		$value = preg_replace_callback("/^([^<>]*)(<?)/i", function ($matches) {
			return str_replace("  ", "&#160; ", $matches[1]) . $matches[2];
		}, $value);
		$value = preg_replace_callback("/(>)([^<>]*)$/i", function ($matches) {
			return $matches[1] . str_replace("  ", "&#160; ", $matches[2]);
		}, $value);

		return $value;
	}

	public function fix()
	{
		$this->topic->top_post_id = $this->id;
		$this->topic->save();
	}

	public function unfix()
	{
		$this->topic->top_post_id = null;
		$this->topic->save();
	}

	public function isFixed()
	{
		if (!empty($this->topic->top_post_id) and $this->id == $this->topic->top_post_id)
			return true;
		else
			return false;
	}

	public function getTopicPage($posts_on_page = null)
	{
		$post = $this->root;

		if (empty($post))
			$post = $this;

		if (empty($post->topic))
			return false;
		else
			$topic = $post->topic;

		$top_post = empty($topic->top_post_id) ? null : $topic->top_post;

		$count = $topic->posts()
			->roots()
			->when($top_post, function ($query) use ($top_post) {
				return $query->where('id', '!=', $top_post->id);
			})
			->when($topic->post_desc, function ($query) use ($post) {
				return $query->where('id', '>=', $post->id);
			}, function ($query) use ($post) {
				return $query->where('id', '<=', $post->id);
			})
			->count();

		$posts_on_page = $posts_on_page ?? $this->getPerPage();

		return intval(ceil($count / $posts_on_page));
	}

	public function scopeWithUserAccessToForums($query)
	{
		return $query->join('topics', 'topics.id', '=', 'posts.topic_id')
			->join('forums', 'forums.id', '=', 'posts.forum_id')
			->leftJoin('users_access_to_forums', function ($join) {
				$join->on('users_access_to_forums.forum_id', '=', 'posts.forum_id')
					->where('users_access_to_forums.user_id', auth()->id());
			})->where(function ($query) {
				$query->where('forums.private', false)
					->orWhere(function ($query) {
						$query->where('forums.private', true)
							->whereNotNull('users_access_to_forums.user_id');
					});
			});
	}

	public function getShareTitle()
	{
		return __('post.post_on_forum_from_user_in_topic', [
			'user_name' => optional($this->create_user)->userName,
			'topic_title' => optional($this->topic)->name
		]);
	}

	public function getShareDescription()
	{
		$s = mb_substr($this->text, 0, 500);
		$s = html_entity_decode(strip_tags($s));
		$s = preg_replace('/([[:space:]]+)/iu', ' ', $s);
		return trim(mb_substr($s, 0, 400));
	}

	public function isEdited()
	{
		return (bool)$this->user_edited_at;
	}

	public function isMustBeSentForReview()
	{
		if (!empty($this->create_user->on_moderate))
			return true;

		if ($this->{$this->getCharactersCountColumn()} > 3) {
			if ($this->getUpperCaseLettersPercent($this->getContent()) > config('litlife.max_number_of_capital_letters'))
				return true;
		}

		if (($this->create_user->comment_count + $this->create_user->forum_message_count) < 10) {
			if ($this->getExternalLinksCount($this->getContent()) > 0)
				return true;
		}

		return false;
	}

	public function getContent()
	{
		return $this->html_text;
	}
}
