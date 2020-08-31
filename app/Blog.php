<?php

namespace App;

use App\Enums\CacheTags;
use App\Model as Model;
use App\Traits\BBCodeable;
use App\Traits\CharactersCountTrait;
use App\Traits\CheckedItems;
use App\Traits\ExternalLinks;
use App\Traits\Likeable;
use App\Traits\NestedItems;
use App\Traits\UserAgentTrait;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Stevebauman\Purify\Facades\Purify;

// use IgnorableObservers\IgnorableObservers;

/**
 * App\Blog
 *
 * @property int $id
 * @property int $blog_user_id
 * @property int $create_user_id
 * @property Carbon $add_time
 * @property string|null $bb_text
 * @property string $text
 * @property int $edit_time
 * @property int $hide
 * @property int $hide_time
 * @property int $hide_user
 * @property string|null $tree
 * @property int $children_count
 * @property int $like_count
 * @property int $action
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $user_edited_at Время когда пользователь отредактировал
 * @property int $level
 * @property bool $external_images_downloaded
 * @property bool $image_size_defined
 * @property bool $display_on_home_page
 * @property int|null $user_agent_id
 * @property int|null $status Статус поста
 * @property string|null $status_changed_at Дата изменения статуса
 * @property int|null $status_changed_user_id Пользователь изменивший статус
 * @property int|null $characters_count Количество символов в посте
 * @property-read \App\Like|null $authUserLike
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
 * @property-read \App\User $create_user
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read mixed $level_with_limit
 * @property mixed $parent
 * @property-read mixed $root
 * @property-read mixed $tree_array
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
 * @property-read \App\User $owner
 * @property-write mixed $b_b_text
 * @property-read \App\User|null $status_changed_user
 * @property-read \App\UserAgent|null $user_agent
 * @method static \Illuminate\Database\Eloquent\Builder|Blog accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog any()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog checked()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog childs($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog descendants($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog onlyChecked()
 * @method static Builder|Blog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog orDescendants($ids)
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Blog orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog owned()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog private ()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog roots()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog unchecked()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereBbText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereBlogUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCharactersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDisplayOnHomePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereExternalImagesDownloaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereHideTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereHideUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereImageSizeDefined($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereLikeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUserAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUserEditedAt($value)
 * @method static Builder|Blog withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog withoutCheckedScope()
 * @method static Builder|Blog withoutTrashed()
 * @mixin Eloquent
 */
class Blog extends Model
{
	use SoftDeletes;
	use NestedItems;
	use UserCreate;
	use UserAgentTrait;
	use Likeable;
	use CheckedItems;
	use CharactersCountTrait;
	use ExternalLinks;
	use BBCodeable;

	public $visible = [
		'id',
		'blog_user_id',
		'create_user_id',
		'text',
		'like_count',
		'created_at',
		'deleted_at',
		'characters_count',
		'children_count'
	];

	protected $perPage = 10;

	protected $dates = [
		'add_time',
		'deleted_at'
	];

	protected $fillable = [
		'bb_text',
		'display_on_home_page'
	];

	const BB_CODE_COLUMN = 'bb_text';
	const HTML_COLUMN = 'text';

	static function getCachedOnModerationCount()
	{
		return Cache::tags([CacheTags::BlogPostsOnModerationCount])->remember('count', 3600, function () {
			return self::sentOnReview()->count();
		});
	}

	static function flushCachedOnModerationCount()
	{
		Cache::tags([CacheTags::BlogPostsOnModerationCount])->pull('count');
	}

	public function scopeAny($query)
	{
		return $query->withTrashed();
	}

	function owner()
	{
		return $this->belongsTo('App\User', 'blog_user_id', 'id');
	}

	/*
	 * Проверка это стена такого то пользователя
	 * */

	public function complaints()
	{
		return $this->morphMany('App\Complain', 'complainable');
	}

	public function scopeOwned($query)
	{
		return $query->whereRaw('"blog_user_id" = "create_user_id"');
	}

	public function isUserBlog($user)
	{
		return $this->blog_user_id == $user->id;
	}

	public function isCreateOwner()
	{
		return $this->blog_user_id == $this->create_user_id;
	}

	public function getTextAttribute($value)
	{
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

	public function setBBTextAttribute($value)
	{
		$this->setBBCode($value);
		$this->attributes['external_images_downloaded'] = false;
		$this->refreshCharactersCount();
	}

	public function refreshCharactersCount()
	{
		$this->{$this->getCharactersCountColumn()} = $this->getCharacterCountInText($this->getContent());
	}

	public function getCharacterCountInText($text)
	{
		return transform($text, function ($text) {

			$text = strip_tags($text);

			$text = preg_replace("/[[:space:]]+/iu", "", $text);

			$text = mb_strlen($text);

			return $text;
		});
	}

	public function getContent()
	{
		return $this->text;
	}

	public function setTextAttribute($value)
	{
		$value = trim(replaceAsc194toAsc32($value));
		$value = removeJsAdCode($value);
		$value = preg_replace("/<br(\ *)\/?>(\ *)<br(\ *)\/?>/iu", "\n\n", $value);
		$this->attributes['text'] = @Purify::clean($value);
		$this->attributes['external_images_downloaded'] = false;
		$this->refreshCharactersCount();
	}

	public function isFixed()
	{
		if (empty($this->owner))
			return false;

		if (empty($blogTopRecordId = $this->owner->setting->blog_top_record))
			return false;

		if ($blogTopRecordId == $this->id)
			return true;

		return false;
	}

	public function fix()
	{
		$this->owner->setting->blog_top_record = $this->id;
		$this->owner->setting->save();
	}

	public function unfix()
	{
		$this->owner->setting->blog_top_record = null;
		$this->owner->setting->save();
	}

	public function getShareTitle()
	{
		return __('blog.message_on_the_wall_from_user',
			['user_name' => optional($this->create_user)->userName,
				'wall_user_name' => optional($this->owner)->userName]);
	}

	public function getShareDescription()
	{
		return mb_substr(strip_tags($this->text), 0, 200);
	}

	public function isMustBeSentForReview()
	{
		if (($this->create_user->comment_count + $this->create_user->forum_message_count) < 10) {
			if ($this->getExternalLinksCount($this->getContent()) > 0)
				return true;
		}

		return false;
	}
}
