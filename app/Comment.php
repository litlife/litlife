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
use App\Traits\NestedItems;
use App\Traits\UserAgentTrait;
use App\Traits\UserCreate;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * App\Comment
 *
 * @property int $id
 * @property int $commentable_id
 * @property int $old_commentable_type
 * @property int $create_user_id
 * @property int $old_time
 * @property string $text
 * @property string|null $old_ip_old
 * @property int $vote_up
 * @property int $vote_down
 * @property int $old_is_spam
 * @property mixed|null $old_user_vote_for_spam
 * @property string|null $bb_text
 * @property int|null $edit_user_id
 * @property int $old_edit_time
 * @property int $old_reputation_count
 * @property int $old_hide
 * @property int $old_hide_time
 * @property int $old_hide_user
 * @property string|null $old_complain_user_ids
 * @property int $old_checked
 * @property int $vote
 * @property int $old_action
 * @property string|null $tree
 * @property int $children_count
 * @property int $hide_from_top
 * @property int $old__lft
 * @property int $old__rgt
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $user_edited_at Время когда пользователь отредактировал
 * @property string|null $old_accepted_at
 * @property string|null $old_sent_for_review_at
 * @property string $commentable_type
 * @property int $level
 * @property bool $external_images_downloaded
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property bool $image_size_defined
 * @property string $ip
 * @property int|null $user_agent_id
 * @property string|null $old_rejected_at
 * @property int|null $characters_count
 * @property int|null $origin_commentable_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
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
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $originCommentable
 * @property-write mixed $b_b_text
 * @property-read \App\User|null $status_changed_user
 * @property-read \App\User $user
 * @property-read \App\BookVote|null $userBookVote
 * @property-read \App\UserAgent|null $user_agent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CommentVote[] $votes
 * @method static \Illuminate\Database\Eloquent\Builder|Comment accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment any()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment author($author)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment book()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment checked()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment childs($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment descendants($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment fulltextSearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment notTransferred()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment onlyChecked()
 * @method static \Illuminate\Database\Query\Builder|Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment orDescendants($ids)
 * @method static Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment orderByOriginFirstAndLatest($commentable)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Comment orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment private ()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment roots()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment sequence($sequence)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment showOnHomePage()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment transferred()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment void()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereBbText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCharactersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereEditUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereExternalImagesDownloaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereHideFromTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereImageSizeDefined($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldChecked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldComplainUserIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldHideTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldHideUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldIpOld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldIsSpam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldReputationCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldSentForReviewAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldUserVoteForSpam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOriginCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereTree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereVoteDown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereVoteUp($value)
 * @method static \Illuminate\Database\Query\Builder|Comment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment withoutCheckedScope()
 * @method static \Illuminate\Database\Query\Builder|Comment withoutTrashed()
 * @mixin \Eloquent
 */
class Comment extends Model
{
	use SoftDeletes;
	use UserCreate;
	use CheckedItems;
	use NestedItems;
	use UserAgentTrait;
	use CharactersCountTrait;
	use Compoships;
	use ExternalLinks;
	use BBCodeable;

	protected $fillable = [
		'bb_text'
	];

	protected $attributes = [
		'status' => StatusEnum::Accepted
	];

	protected $perPage = 15;

	const BB_CODE_COLUMN = 'bb_text';
	const HTML_COLUMN = 'text';

	public static function boot()
	{
		parent::boot();

		//static::addGlobalScope(new CheckedScope);
		//static::addGlobalScope(new NotConnectedScope);
	}

	static function getCachedOnModerationCount()
	{
		return Cache::tags([CacheTags::CommentsOnModerationCount])->remember('count', 3600, function () {
			return self::sentOnReview()->count();
		});
	}

	static function flushCachedOnModerationCount()
	{
		Cache::tags([CacheTags::CommentsOnModerationCount])->pull('count');
	}

	static function cachedCountRefresh()
	{
		Cache::forever('comments_count_refresh', true);
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
			$s = "to_tsvector('english', \"text\" )  ";
			$s .= " @@ to_tsquery('english', quote_literal(quote_literal(?)))";

			return $query->whereRaw($s, [implode('+', $Ar)]);
		}
	}

	public function scopeAuthor($query, $author)
	{
		return $query->whereRaw('"commentable_type" = \'book\' AND ' .
			' ("commentable_id" IN (select "id" FROM "books" left join "book_authors" on ("id" = "book_id") where "author_id" = \'' . $author->id . '\') OR' .
			' "commentable_id" IN (select "id" FROM "books" left join "book_translators" on ("id" = "book_id") where "translator_id" = \'' . $author->id . '\') )');
	}

	public function scopeSequence($query, $sequence)
	{
		return $query->whereRaw('"commentable_id" IN (select "id" FROM "books" ' .
			'left join "book_sequences" on ("id" = "book_id") where "sequence_id" = \'' . $sequence->id . '\')');
	}

	function user()
	{
		return $this->belongsTo('App\User', 'create_user_id', 'id');
	}

	public function commentable()
	{
		return $this->morphTo()->any();
	}

	public function complaints()
	{
		return $this->morphMany('App\Complain', 'complainable');
	}

	public function scopeVoid($query)
	{
		return $query;
	}

	public function setBBTextAttribute($value)
	{
		$this->setBBCode($value);
		$this->attributes['external_images_downloaded'] = false;
		$this->refreshCharactersCount();
	}

	public function setTextAttribute($value)
	{
		$this->setHtml($value);
		$this->attributes['external_images_downloaded'] = false;
		$this->refreshCharactersCount();
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

	public function scopeShowOnHomePage($query)
	{
		return $query->where('hide_from_top', false);
	}

	public function updateVotes()
	{
		$this->vote_up = $this->votes()->where('vote', '>', '0')->count();
		//$this->vote_down = $this->votes()->where('vote', '<', '0')->count();
		//$this->vote = $this->vote_up - $this->vote_down;
		$this->vote = $this->vote_up;
		$this->save();
	}

	public function votes()
	{
		return $this->hasMany('App\CommentVote', 'comment_id', 'id');
	}

	public function getShareTitle()
	{
		if ($this->isBookType()) {
			if (!empty($this->originCommentable))
				$book_title = ' "' . $this->originCommentable->title . '" - ' . implode(', ', $this->originCommentable->getAuthorsWithType('writers')->pluck('name_helper')->toArray()) . '';
			else
				$book_title = '';

			return __('comment.comment_from_user_for_book', [
				'user_name' => optional($this->create_user)->userName,
				'book_title' => $book_title
			]);
		} elseif ($this->isCollectionType()) {
			return __('comment.comment_from_user_for_collection', [
				'user_name' => optional($this->create_user)->userName,
				'collection_title' => optional($this->originCommentable)->title
			]);
		}
	}

	public function isBookType()
	{
		return $this->getCommentableModelName() == 'Book';
	}

	public function getCommentableModelName()
	{
		return ltrim(Relation::getMorphedModel($this->commentable_type), "App\\");
	}

	public function isCollectionType()
	{
		return $this->getCommentableModelName() == 'Collection';
	}

	public function getShareDescription()
	{
		return mb_substr(strip_tags($this->text), 0, 200);
	}

	public function isCreateUserAuthorOfBook()
	{
		if ($this->commentable instanceof Book) {
			$manager = $this->commentable->getManagerAssociatedWithUser($this->create_user);

			if (optional($manager)->character == 'author') {
				return true;
			}
		}

		return false;
	}

	public function scopeBookType($query)
	{
		return $query->where('commentable_type', 'book');
	}

	public function userBookVote()
	{
		return $this->hasOne('App\BookVote', ['book_id', 'create_user_id'], ['commentable_id', 'create_user_id']);
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

		if (!empty($this->commentable) and $this->isBookType()) {

			$settings = Variable::where('name', 'settings')
				->first();

			if (!empty($settings)) {
				$words = $settings->value['check_words_in_comments'] ?? [];

				foreach ((array)$words as $word) {
					if (mb_stripos($this->bb_text, $word))
						return true;
				}
			}

			// если в тексте есть email, то отправляем комментарий на проверку
			if (preg_match('/(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))/iD', $this->bb_text))
				return true;
		}

		return false;
	}

	public function getContent()
	{
		return $this->text;
	}

	public function isMustBeHideFromTop()
	{
		if (!empty($this->commentable) and $this->isBookType()) {
			$settings = Variable::disableCache()->where('name', 'settings')
				->first();

			$genre_ids = $settings->value['genres_books_comments_hide_from_home_page'] ?? [];

			$genres = $this->commentable->genres->whereIn('id', $genre_ids);

			if (!empty($genres) and count($genres) > 0)
				return true;
		}

		return false;
	}

	public function getCreateUserBookAuthor()
	{
		if ($this->originCommentable instanceof Book) {
			$authors = $this->originCommentable->getAuthorsManagerAssociatedWithUser($this->create_user);

			if ($authors->isEmpty())
				return null;

			$authors = $authors->sortBy(function ($author, $key) {
				switch ($author->pivot->type) {
					case '0';
						return 0;
						break;
					case '1';
						return 1;
						break;
					case '2';
						return 3;
						break;
					case '3';
						return 5;
						break;
					case '4';
						return 4;
						break;
				}
			});

			if ($authors->isNotEmpty())
				return $authors->first();
		}

		return null;
	}

	public function scopeNotTransferred($query)
	{
		return $query->whereColumn('commentable_id', 'origin_commentable_id');
	}

	public function scopeTransferred($query)
	{
		return $query->whereColumn('commentable_id', '!=', 'origin_commentable_id');
	}

	public function originCommentable()
	{
		return $this->morphTo(null, 'commentable_type', 'origin_commentable_id', 'id')->any();
	}

	public function scopeOrderByOriginFirstAndLatest($query, $commentable)
	{
		return $query->orderByRaw('"origin_commentable_id" = \'' . intval($commentable->id) . '\' desc, "created_at" desc');
	}
}
