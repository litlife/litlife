<?php

namespace App;

use App\Enums\StatusEnum;
use App\Model as Model;
use App\Scopes\CheckedScope;
use App\Scopes\NotMergedScope;
use App\Traits\CheckedItems;
use App\Traits\FavoritableTrait;
use App\Traits\Likeable;
use App\Traits\UserCreate;
use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Sequence
 *
 * @property int $id
 * @property string $name
 * @property int|null $create_user_id
 * @property int|null $merged_to
 * @property int $book_count
 * @property int $user_lib_count
 * @property int $like_count
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $user_edited_at Время когда пользователь отредактировал
 * @property int|null $status
 * @property Carbon|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property int|null $merge_user_id
 * @property Carbon|null $merged_at
 * @property int $added_to_favorites_count Количество пользователей добавивших в избранное
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $activities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $addedToFavoritesUsers
 * @property-read \App\Like|null $authUserLike
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
 * @property-read \App\User|null $create_user
 * @property mixed $books_count
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read mixed $pivot_number
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSequence[] $library_users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
 * @property-read \App\User|null $merge_user
 * @property-read Sequence|null $merged_sequence
 * @property-read \App\User|null $status_changed_user
 * @method static Builder|Sequence accepted()
 * @method static Builder|Sequence acceptedAndSentForReview()
 * @method static Builder|Sequence acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static Builder|Sequence acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static Builder|Sequence acceptedOrBelongsToAuthUser()
 * @method static Builder|Sequence acceptedOrBelongsToUser($user)
 * @method static Builder|Sequence any()
 * @method static Builder|Sequence checked()
 * @method static Builder|Sequence checkedAndOnCheck()
 * @method static Builder|Sequence checkedOrBelongsToUser($user)
 * @method static Builder|Sequence fulltextSearch($searchText)
 * @method static Builder|Sequence newModelQuery()
 * @method static Builder|Sequence newQuery()
 * @method static Builder|Sequence notMerged()
 * @method static Builder|Sequence onCheck()
 * @method static Builder|Sequence onlyChecked()
 * @method static \Illuminate\Database\Query\Builder|Sequence onlyTrashed()
 * @method static Builder|Sequence orderByBooksCountAsc()
 * @method static Builder|Sequence orderByBooksCountDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Sequence orderStatusChangedAsc()
 * @method static Builder|Sequence orderStatusChangedDesc()
 * @method static Builder|Sequence private ()
 * @method static Builder|Sequence query()
 * @method static Builder|Sequence rememberCount($minutes = 5, $refresh = false)
 * @method static Builder|Sequence sentOnReview()
 * @method static Builder|Sequence similaritySearch($searchText)
 * @method static Builder|Sequence unaccepted()
 * @method static Builder|Sequence unchecked()
 * @method static Builder|Sequence void()
 * @method static Builder|Sequence whereAddedToFavoritesCount($value)
 * @method static Builder|Sequence whereBookCount($value)
 * @method static Builder|Sequence whereCreateUserId($value)
 * @method static Builder|Sequence whereCreatedAt($value)
 * @method static Builder|Sequence whereCreator(\App\User $user)
 * @method static Builder|Sequence whereDeletedAt($value)
 * @method static Builder|Sequence whereDescription($value)
 * @method static Builder|Sequence whereId($value)
 * @method static Builder|Sequence whereLikeCount($value)
 * @method static Builder|Sequence whereMergeUserId($value)
 * @method static Builder|Sequence whereMergedAt($value)
 * @method static Builder|Sequence whereMergedTo($value)
 * @method static Builder|Sequence whereName($value)
 * @method static Builder|Sequence whereStatus($value)
 * @method static Builder|Sequence whereStatusChangedAt($value)
 * @method static Builder|Sequence whereStatusChangedUserId($value)
 * @method static Builder|Sequence whereStatusIn($statuses)
 * @method static Builder|Sequence whereStatusNot($status)
 * @method static Builder|Sequence whereUpdatedAt($value)
 * @method static Builder|Sequence whereUserEditedAt($value)
 * @method static Builder|Sequence whereUserLibCount($value)
 * @method static \Illuminate\Database\Query\Builder|Sequence withTrashed()
 * @method static Builder|Sequence withUnchecked()
 * @method static Builder|Sequence withoutCheckedScope()
 * @method static \Illuminate\Database\Query\Builder|Sequence withoutTrashed()
 * @method static Builder|Sequence wordSimilaritySearch($searchText)
 * @mixin Eloquent
 */
class Sequence extends Model
{
	use SoftDeletes;
	use CheckedItems;
	use UserCreate;
	use LogsActivity;
	use Likeable;
	use FavoritableTrait;

	protected static $recordEvents = [];
	protected $attributes =
		[
			'status' => StatusEnum::Private
		];
	protected $fillable = [
		'name',
		'description',
		'merged_to'
	];

	protected $dates = [
		'merged_at',
		'status_changed_at',
		'user_edited_at'
	];

	protected $perPage = 20;

	const FAVORITABLE_PIVOT_TABLE = 'user_sequences';

	public static function boot()
	{
		parent::boot();

		//static::addGlobalScope(new CheckedScope);
		//static::addGlobalScope(new NotMergedScope);
	}

	static function cachedCountRefresh()
	{
		Cache::forever('sequences_count_refresh', true);
	}

	public function scopeAny($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class)->withoutGlobalScope(NotMergedScope::class)->withTrashed();
	}

	public function scopeFulltextSearch($query, $searchText)
	{
		$Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

		$s = '';

		if ($Ar) {
			/*
			$query->whereRaw("to_tsvector('english', \"name\") @@ to_tsquery('english', quote_literal(quote_literal(?)))", [implode('&', $Ar)])
			*/
			$s = "to_tsvector('english', \"name\") ";
			$s .= " @@ to_tsquery(quote_literal(quote_literal(?)) || ':*')";
			return $query->whereRaw($s, implode('+', $Ar));
		}
		return $query;
	}

	public function scopeVoid($query)
	{
		return $query;
	}

	public function library_users()
	{
		return $this->hasMany('App\UserSequence');
	}

	public function create_user()
	{
		return $this->belongsTo('App\User', $this->getCreateUserIdColumn(), 'id')
			->with("avatar");
	}

	public function scopeRememberCount($query, $minutes = 5, $refresh = false)
	{
		if ($refresh) Cache::forget('sequences_count');

		return Cache::remember('sequences_count', $minutes, function () use ($query) {
			return $query->count();
		});
	}

	public function scopeWordSimilaritySearch($query, $searchText)
	{
		$query->selectRaw("sequences.*, word_similarity(name, ?) AS rank", [$searchText]);

		$query->whereRaw("(name) %> ?", [$searchText]);

		$query->orderBy("rank", 'desc');

		return $query;
	}

	public function scopeSimilaritySearch($query, $searchText)
	{
		$query->selectRaw("sequences.*, similarity(name, ?) AS rank", [$searchText]);

		$query->whereRaw("(name) % ?", [$searchText]);

		$query->orderBy("rank", 'desc');

		return $query;
	}

	public function scopeNotMerged($query)
	{
		return $query->whereNull('merged_at');
	}

	/*
	 * Серия объединена или нет
	 * @return boolean
	 *
	 * */

	public function isMerged()
	{
		return (boolean)$this->merged_at;
	}

	public function merged_sequence()
	{
		return $this->hasOne('App\Sequence', 'id', 'merged_to');
	}

	public function merge_user()
	{
		return $this->hasOne('App\User', 'id', 'merge_user_id');
	}

	public function getPivotNumberAttribute($value)
	{
		return sprintf("%04s", $this->pivot->number);
	}

	public function setDescriptionAttribute($value)
	{
		$this->attributes['description'] = $value;
	}

	public function refreshBooksCount()
	{
		$this->book_count = $this->books()->count();
	}

	public function books()
	{
		return $this->belongsToMany('App\Book', 'book_sequences', 'sequence_id', 'book_id')
			->withPivot('number')
			->orderBy('number', 'asc');
	}

	public function setBooksCountAttribute($value)
	{
		$this->attributes['book_count'] = intval($value);
	}

	public function getBooksCountAttribute()
	{
		return intval($this->book_count);
	}

	public function scopeOrderByBooksCountAsc($query)
	{
		return $query->orderBy('book_count', 'desc')
			->orderBy('sequences.id', 'asc');
	}

	public function scopeOrderByBooksCountDesc($query)
	{
		return $query->orderBy('book_count', 'desc')
			->orderBy('sequences.id', 'asc');
	}

	public function updateBooksCount()
	{
		if ($this->isPrivate()) {
			$this->book_count = $this->books()
				->acceptedOrBelongsToUser($this->create_user)
				->count();
		} else {
			$this->book_count = $this->books()
				->accepted()
				->count();
		}
	}
}
