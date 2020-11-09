<?php

namespace App;

use App\Enums\UserAccountPermissionValues;
use App\Traits\CheckedItems;
use App\Traits\Commentable;
use App\Traits\FavoritableTrait;
use App\Traits\Likeable;
use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * App\Collection
 *
 * @property int $id
 * @property string $title Название
 * @property string|null $description Описание
 * @property int $who_can_add Кто может добавлять книги
 * @property int $who_can_comment Кто может комментировать
 * @property string|null $lang Язык
 * @property string|null $url Ссылка на подборку на внешнем сайте
 * @property string|null $url_title Название ссылки
 * @property int|null $cover_id collection.cover_id
 * @property int $create_user_id ID пользователя, который создал
 * @property int $books_count Количество книг
 * @property int|null $comments_count Количество комментариев
 * @property int|null $added_to_favorites_users_count Количество раз добавлена в избранное
 * @property int|null $views_count Количество просмотров
 * @property int|null $like_count Количество лайков
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $latest_updates_at Дата последних обновлений в подборке
 * @property int|null $status Кто видит подборку
 * @property string|null $status_changed_at Дата изменения поля кто видит подборку
 * @property int|null $status_changed_user_id Пользователь изменивший поле кто видит подборку
 * @property int|null $users_count Количество участников
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $addedToFavoritesUsers
 * @property-read \App\Like|null $authUserLike
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CollectedBook[] $collected
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CollectionUser[] $collectionUser
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
 * @property-read \App\User $create_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSubscriptionsEventNotification[] $eventNotificationSubscriptions
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $latest_books
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
 * @property-write mixed $who_can_see
 * @property-read \App\User|null $status_changed_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $usersAddedToFavorites
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserFavoriteCollection[] $usersAddedToFavoritesPivot
 * @method static Builder|Collection accepted()
 * @method static Builder|Collection acceptedAndSentForReview()
 * @method static Builder|Collection acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static Builder|Collection acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static Builder|Collection acceptedOrBelongsToAuthUser()
 * @method static Builder|Collection acceptedOrBelongsToUser($user)
 * @method static Builder|Collection any()
 * @method static Builder|Collection checked()
 * @method static Builder|Collection checkedAndOnCheck()
 * @method static Builder|Collection checkedOrBelongsToUser($user)
 * @method static Builder|Collection fulltextSearch($searchText)
 * @method static Builder|Collection newModelQuery()
 * @method static Builder|Collection newQuery()
 * @method static Builder|Collection onCheck()
 * @method static Builder|Collection onlyChecked()
 * @method static \Illuminate\Database\Query\Builder|Collection onlyTrashed()
 * @method static Builder|Collection orderByBooksCount()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static Builder|Collection orderByLikesCount()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Collection orderStatusChangedAsc()
 * @method static Builder|Collection orderStatusChangedDesc()
 * @method static Builder|Collection private ()
 * @method static Builder|Collection query()
 * @method static Builder|Collection seeEveryone()
 * @method static Builder|Collection sentOnReview()
 * @method static Builder|Collection unaccepted()
 * @method static Builder|Collection unchecked()
 * @method static Builder|Collection userSees($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|Collection whereAddedToFavoritesUsersCount($value)
 * @method static Builder|Collection whereBooksCount($value)
 * @method static Builder|Collection whereCommentsCount($value)
 * @method static Builder|Collection whereCoverId($value)
 * @method static Builder|Collection whereCreateUserId($value)
 * @method static Builder|Collection whereCreatedAt($value)
 * @method static Builder|Collection whereCreator(\App\User $user)
 * @method static Builder|Collection whereDeletedAt($value)
 * @method static Builder|Collection whereDescription($value)
 * @method static Builder|Collection whereId($value)
 * @method static Builder|Collection whereLang($value)
 * @method static Builder|Collection whereLatestUpdatesAt($value)
 * @method static Builder|Collection whereLikeCount($value)
 * @method static Builder|Collection whereStatus($value)
 * @method static Builder|Collection whereStatusChangedAt($value)
 * @method static Builder|Collection whereStatusChangedUserId($value)
 * @method static Builder|Collection whereStatusIn($statuses)
 * @method static Builder|Collection whereStatusNot($status)
 * @method static Builder|Collection whereTitle($value)
 * @method static Builder|Collection whereUpdatedAt($value)
 * @method static Builder|Collection whereUrl($value)
 * @method static Builder|Collection whereUrlTitle($value)
 * @method static Builder|Collection whereUserCanAddBooks(\App\User $user)
 * @method static Builder|Collection whereUsersCount($value)
 * @method static Builder|Collection whereViewsCount($value)
 * @method static Builder|Collection whereWhoCanAdd($value)
 * @method static Builder|Collection whereWhoCanComment($value)
 * @method static \Illuminate\Database\Query\Builder|Collection withTrashed()
 * @method static Builder|Collection withUnchecked()
 * @method static Builder|Collection withoutCheckedScope()
 * @method static \Illuminate\Database\Query\Builder|Collection withoutTrashed()
 * @mixin \Eloquent
 */
class Collection extends Model
{
	use UserCreate;
	use SoftDeletes;
	use Likeable;
	use Commentable;
	use CheckedItems;
	use HasRelationships;
	use FavoritableTrait;

	public $fillable = [
		'title',
		'description',
		'status',
		'who_can_add',
		'who_can_comment',
		'url',
		'url_title'
	];

	protected $visible = [
		'id',
		'title',
		'description',
		'status',
		'who_can_add',
		'who_can_comment',
		'url',
		'url_title',
		'created_at',
		'updated_at',
		'deleted_at',
		'like_count'
	];

	const ADDED_TO_FAVORITES_USERS_COUNT_COLUMN_NAME = 'added_to_favorites_users_count';
	const FAVORITABLE_PIVOT_TABLE = 'user_favorite_collections';

	/*
		protected $casts = [
			'books_count' => 'integer',
			'comments_count' => 'integer',
			'added_to_favorites_users_count' => 'integer',
			'views_count' => 'integer'
		];
		*/

	public function scopeAcceptedOrBelongsToUser($query, $user)
	{
		if (isset($user)) {
			return $query->accepted()
				->orWhere($this->getTable() . '.create_user_id', $user->id)
				->orWhereHas('collectionUser', function (Builder $query) use ($user) {
					$query->select('id')
						->where('user_id', $user->id);
				});
		} else {
			return $query->accepted();
		}
	}

	public function scopeWhereUserCanAddBooks($query, User $user)
	{
		return $query->where('who_can_add', UserAccountPermissionValues::everyone)
			->orWhere($this->getTable() . '.create_user_id', $user->id)
			->orWhereHas('collectionUser', function (Builder $query) use ($user) {
				$query->select('id')
					->where('user_id', $user->id)
					->where('can_add_books', true);
			});
	}

	public function scopeFulltextSearch($query, $searchText)
	{
		$searchText = replaceSimilarSymbols($searchText);

		$Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

		$s = '';

		if ($Ar) {
			$s = "to_tsvector('english', \"title\") ";
			$s .= " @@ to_tsquery(quote_literal(quote_literal(?)) || ':*')";

			return $query->whereRaw($s, implode('+', $Ar));
		}

		return $query;
	}

	/*
		public function books()
		{
			return $this->belongsToMany('App\Book', 'collected_books')
				->withPivot('number', 'comment', 'create_user_id')
				->withTimestamps();
		}
	*/

	public function collectionUser()
	{
		return $this->hasMany('App\CollectionUser');
	}

	public function scopeAny($query)
	{
		return $query->withTrashed();
	}

	public function setWhoCanSeeAttribute($value)
	{
		$this->attributes['who_can_see'] = UserAccountPermissionValues::getValue($value);
	}

	public function setWhoCanAddAttribute($value)
	{
		$this->attributes['who_can_add'] = UserAccountPermissionValues::getValue($value);
	}

	public function setWhoCanCommentAttribute($value)
	{
		$this->attributes['who_can_comment'] = UserAccountPermissionValues::getValue($value);
	}

	public function refreshBooksCount()
	{
		$this->books_count = $this->books()->count();
	}

	public function collected()
	{
		return $this->hasMany('App\CollectedBook');
	}

	public function books()
	{
		return $this->hasManyDeep(Book::class, [CollectedBook::class],
			['collection_id', 'id'], ['id', 'book_id'])
			->withIntermediate('App\CollectedBook', ['create_user_id', 'number', 'comment']);
	}

	/*
		public function getWhoCanAddAttribute($value)
		{
			return \App\Enums\UserAccountPermissionValues::getKey($value);
		}
		*/

	public function getBooksCountAttribute($value)
	{
		return intval($value);
	}

	public function getCommentsCountAttribute($value)
	{
		return intval($value);
	}

	public function getAddedToFavoritesUsersCountAttribute($value)
	{
		return intval($value);
	}

	public function getViewsCountAttribute($value)
	{
		return intval($value);
	}

	public function getUsersCountAttribute($value)
	{
		return intval($value);
	}

	public function viewsIncrement()
	{
		$this->views_count++;
		$this->save();
	}

	public function usersAddedToFavoritesPivot()
	{
		return $this->hasMany('App\UserFavoriteCollection');
	}

	public function refreshUsersAddedToFavoritesCount()
	{
		$this->added_to_favorites_users_count = $this->usersAddedToFavorites()->count();
	}

	public function usersAddedToFavorites()
	{
		return $this->belongsToMany('App\User',
			'user_favorite_collections',
			'collection_id',
			'user_id');
	}

	public function authUserAddedToFavorites()
	{
		return $this->usersAddedToFavorites()
			->where('user_favorite_collections.user_id', auth()->id());
	}

	public function scopeSeeEveryone($query)
	{
		return $query->accepted();
	}

	public function scopeUserSees($query, $user)
	{
		return $query->acceptedOrBelongsToUser($user);
	}

	public function getShareTitle()
	{
		$title = __('collection.collection') . ' "' . $this->title . '"';

		if ($this->books_count)
			$title .= ' - ' . $this->books_count . ' ' . mb_strtolower(trans_choice('collection.books', $this->books_count));

		return $title;
	}

	public function getShareDescription()
	{
		return mb_substr(strip_tags($this->description), 0, 200);
	}

	public function getShareImage()
	{
		return null;
	}

	public function getShareUrl()
	{
		return route('collections.show', $this);
	}

	public function getShareTooltip()
	{
		return __('collection.share_a_collection');
	}

	public function eventNotificationSubscriptions()
	{
		return $this->morphMany('App\UserSubscriptionsEventNotification', 'eventable');
	}

	public function latest_books()
	{
		return $this->belongsToMany('App\Book', 'collected_books')
			->withPivot('created_at')
			->orderBy('pivot_created_at', 'desc');
	}

	public function complaints()
	{
		return $this->morphMany('App\Complain', 'complainable');
	}

	public function refreshUsersCount()
	{
		$this->users_count = $this->users()->count() + 1;
	}

	public function users()
	{
		return $this->belongsToMany('App\User', 'collection_users')
			->withPivot('can_edit',
				'can_add_books',
				'can_remove_books',
				'can_edit_books_description',
				'can_comment')
			->withTimestamps()
			->wherePivot('deleted_at', null);
	}

	public function scopeOrderByLikesCount($query)
	{
		return $query->orderByWithNulls('like_count', 'desc', 'last')
			->orderBy('id', 'desc');
	}

	public function scopeOrderByBooksCount($query)
	{
		return $query->orderByWithNulls('books_count', 'desc', 'last')
			->orderBy('id', 'desc');
	}
}
