<?php

namespace App;

use App\Enums\AuthorEnum;
use App\Enums\StatusEnum;
use App\IndexConfigurators\AuthorIndexConfigurator;
use App\Scopes\CheckedScope;
use App\Traits\AdminNoteableTrait;
use App\Traits\CheckedItems;
use App\Traits\FavoritableTrait;
use App\Traits\Likeable;
use App\Traits\UserCreate;
use Auth;
use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Author
 *
 * @property int $id
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property int $books_count
 * @property int $old_rating
 * @property string|null $lang
 * @property int $old_time
 * @property string $nickname
 * @property string|null $home_page
 * @property string|null $email
 * @property int $old_action
 * @property string|null $description
 * @property int $old_translate_books_count
 * @property int|null $create_user_id
 * @property int $old_hide
 * @property int|null $redirect_to_author_id
 * @property int $comments_count
 * @property string|null $wikipedia_url
 * @property int $old_gender
 * @property string|null $born_date
 * @property string|null $born_place
 * @property string|null $dead_date
 * @property string|null $dead_place
 * @property string|null $years_creation
 * @property int|null $edit_user_id
 * @property int|null $old_edit_time
 * @property int|null $old_hide_time
 * @property int|null $old_delete_user_id
 * @property string|null $old_hide_reason
 * @property int $old_user_show
 * @property string|null $orig_last_name
 * @property string|null $orig_first_name
 * @property string|null $orig_middle_name
 * @property float|null $old_vote_average
 * @property int $votes_count
 * @property int|null $forum_id
 * @property int $user_lib_count
 * @property int $view_day
 * @property int $view_week
 * @property int $view_month
 * @property int $view_year
 * @property int $view_all
 * @property float|null $vote_average
 * @property int $like_count
 * @property int|null $group_id
 * @property int|null $group_add_user
 * @property int|null $group_add_time
 * @property int $rating
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $photo_id
 * @property Carbon|null $view_updated_at
 * @property string|null $merged_at
 * @property Carbon|null $user_edited_at Время когда пользователь отредактировал
 * @property string|null $old_accepted_at
 * @property string|null $old_sent_for_review_at
 * @property int|null $old_check_user_id
 * @property string $gender
 * @property int|null $status
 * @property Carbon|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property string|null $name_helper Вспомогательный столбец для быстрого trgm поиска
 * @property int|null $biography_id
 * @property string|null $old_rejected_at
 * @property bool $rating_changed Если рейтинг у книг изменился, то значение будет true
 * @property int $admin_notes_count
 * @property int $added_to_favorites_count Количество пользователей добавивших в избранное
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $activities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $addedToFavoritesUsers
 * @property-read \App\AdminNote|null $admin_note
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $admin_notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $any_books
 * @property-read \App\Like|null $authUserLike
 * @property-read \App\AuthorPhoto|null $avatar
 * @property-read \App\AuthorAverageRatingForPeriod $averageRatingForPeriod
 * @property-read \App\AuthorBiography $biography
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $compiled_books
 * @property-read \App\User|null $create_user
 * @property-read \App\User|null $edit_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $edited_books
 * @property-read \App\Forum|null $forum
 * @property-read mixed $full_name
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property mixed $name
 * @property-read mixed $original_full_name
 * @property-read \App\AuthorGroup|null $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $illustrated_books
 * @property-read \App\Language|null $language
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $latest_admin_notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserAuthor[] $library_users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Manager[] $managers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ModeratorRequest[] $moderator_requests
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorModerator[] $moderators
 * @property-read \App\AuthorPhoto|null $photo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorPhoto[] $photos
 * @property-read Author|null $redirect_to_author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorRepeat[] $repeats
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorSaleRequest[] $sales_request
 * @property-read \App\User|null $status_changed_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $translated_books
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $user2
 * @property-read \App\AuthorStatus|null $user_status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users_added_to_favorites
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorStatus[] $users_read_statuses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $written_books
 * @method static Builder|Author accepted()
 * @method static Builder|Author acceptedAndSentForReview()
 * @method static Builder|Author acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static Builder|Author acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static Builder|Author acceptedOrBelongsToAuthUser()
 * @method static Builder|Author acceptedOrBelongsToUser($user)
 * @method static Builder|Author any()
 * @method static Builder|Author checked()
 * @method static Builder|Author checkedAndOnCheck()
 * @method static Builder|Author checkedOrBelongsToUser($user)
 * @method static Builder|Author fulltextSearch($searchText)
 * @method static Builder|Author newModelQuery()
 * @method static Builder|Author newQuery()
 * @method static Builder|Author notMerged()
 * @method static Builder|Author onCheck()
 * @method static Builder|Author onlyChecked()
 * @method static \Illuminate\Database\Query\Builder|Author onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Author orderByRating()
 * @method static Builder|Author orderByRatingDayDesc()
 * @method static Builder|Author orderByRatingDesc()
 * @method static Builder|Author orderByRatingMonthDesc()
 * @method static Builder|Author orderByRatingQuarterDesc()
 * @method static Builder|Author orderByRatingWeekDesc()
 * @method static Builder|Author orderByRatingYearDesc()
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Author orderStatusChangedAsc()
 * @method static Builder|Author orderStatusChangedDesc()
 * @method static Builder|Author private ()
 * @method static Builder|Author query()
 * @method static Builder|Author rememberCount($minutes = 5, $refresh = false)
 * @method static Builder|Author searchByNameParts($last_name = null, $first_name = null, $middle_name = null, $nickname = null)
 * @method static Builder|Author sentOnReview()
 * @method static Builder|Author similaritySearch($searchText)
 * @method static Builder|Author unaccepted()
 * @method static Builder|Author unchecked()
 * @method static Builder|Author void()
 * @method static Builder|Author whereAddedToFavoritesCount($value)
 * @method static Builder|Author whereAdminNotesCount($value)
 * @method static Builder|Author whereBiographyId($value)
 * @method static Builder|Author whereBooksCount($value)
 * @method static Builder|Author whereBornDate($value)
 * @method static Builder|Author whereBornPlace($value)
 * @method static Builder|Author whereCommentsCount($value)
 * @method static Builder|Author whereCreateUserId($value)
 * @method static Builder|Author whereCreatedAt($value)
 * @method static Builder|Author whereCreator(\App\User $user)
 * @method static Builder|Author whereDeadDate($value)
 * @method static Builder|Author whereDeadPlace($value)
 * @method static Builder|Author whereDeletedAt($value)
 * @method static Builder|Author whereDescription($value)
 * @method static Builder|Author whereEditUserId($value)
 * @method static Builder|Author whereEmail($value)
 * @method static Builder|Author whereFirstName($value)
 * @method static Builder|Author whereForumId($value)
 * @method static Builder|Author whereGender($value)
 * @method static Builder|Author whereGroupAddTime($value)
 * @method static Builder|Author whereGroupAddUser($value)
 * @method static Builder|Author whereGroupId($value)
 * @method static Builder|Author whereHomePage($value)
 * @method static Builder|Author whereId($value)
 * @method static Builder|Author whereLang($value)
 * @method static Builder|Author whereLastName($value)
 * @method static Builder|Author whereLikeCount($value)
 * @method static Builder|Author whereMergedAt($value)
 * @method static Builder|Author whereMiddleName($value)
 * @method static Builder|Author whereNameHelper($value)
 * @method static Builder|Author whereNickname($value)
 * @method static Builder|Author whereOldAcceptedAt($value)
 * @method static Builder|Author whereOldAction($value)
 * @method static Builder|Author whereOldCheckUserId($value)
 * @method static Builder|Author whereOldDeleteUserId($value)
 * @method static Builder|Author whereOldEditTime($value)
 * @method static Builder|Author whereOldGender($value)
 * @method static Builder|Author whereOldHide($value)
 * @method static Builder|Author whereOldHideReason($value)
 * @method static Builder|Author whereOldHideTime($value)
 * @method static Builder|Author whereOldRating($value)
 * @method static Builder|Author whereOldRejectedAt($value)
 * @method static Builder|Author whereOldSentForReviewAt($value)
 * @method static Builder|Author whereOldTime($value)
 * @method static Builder|Author whereOldTranslateBooksCount($value)
 * @method static Builder|Author whereOldUserShow($value)
 * @method static Builder|Author whereOldVoteAverage($value)
 * @method static Builder|Author whereOrigFirstName($value)
 * @method static Builder|Author whereOrigLastName($value)
 * @method static Builder|Author whereOrigMiddleName($value)
 * @method static Builder|Author wherePhotoId($value)
 * @method static Builder|Author whereRating($value)
 * @method static Builder|Author whereRatingChanged($value)
 * @method static Builder|Author whereRedirectToAuthorId($value)
 * @method static Builder|Author whereStatus($value)
 * @method static Builder|Author whereStatusChangedAt($value)
 * @method static Builder|Author whereStatusChangedUserId($value)
 * @method static Builder|Author whereStatusIn($statuses)
 * @method static Builder|Author whereStatusNot($status)
 * @method static Builder|Author whereUpdatedAt($value)
 * @method static Builder|Author whereUserEditedAt($value)
 * @method static Builder|Author whereUserLibCount($value)
 * @method static Builder|Author whereViewAll($value)
 * @method static Builder|Author whereViewDay($value)
 * @method static Builder|Author whereViewMonth($value)
 * @method static Builder|Author whereViewUpdatedAt($value)
 * @method static Builder|Author whereViewWeek($value)
 * @method static Builder|Author whereViewYear($value)
 * @method static Builder|Author whereVoteAverage($value)
 * @method static Builder|Author whereVotesCount($value)
 * @method static Builder|Author whereWikipediaUrl($value)
 * @method static Builder|Author whereYearsCreation($value)
 * @method static \Illuminate\Database\Query\Builder|Author withTrashed()
 * @method static Builder|Author withUnchecked()
 * @method static Builder|Author withoutCheckedScope()
 * @method static \Illuminate\Database\Query\Builder|Author withoutTrashed()
 * @method static Builder|Author wordSimilaritySearch($searchText)
 * @mixin Eloquent
 */
class Author extends Model
{
	use SoftDeletes;
	use CheckedItems;
	use UserCreate;
	use LogsActivity;
	use AdminNoteableTrait;
	use Likeable;
	use FavoritableTrait;

	protected static $recordEvents = [];

	protected $attributes =
		[
			'status' => StatusEnum::Private
		];

	protected $indexConfigurator = AuthorIndexConfigurator::class;

	// Here you can specify a mapping for a model fields.
	protected $searchRules = [
		//
	];

	protected $mapping = [
		'properties' => [
			'first_name' => [
				'type' => 'text'
			],
			'last_name' => [
				'type' => 'text'
			],
			'middle_name' => [
				'type' => 'text'
			],
			'nickname' => [
				'type' => 'text'
			],
			'name_helper' => array(
				'type' => 'text',
				'analyzer' => 'autocomplete',
				'search_analyzer' => 'autocomplete_search',
			),
			/*
			'name' => [
				'properties' => [
					'first_name' => [
						'type' => 'text',
						'fields' => [
							'ngrams' => [
								'type' => 'text',
								'analyzer' => 'autocomplete'
							]
						]
					],
					'last_name' => [
						'type' => 'text'
					],
					'middle_name' => [
						'type' => 'text'
					],
					'nickname' => [
						'type' => 'text'
					],
				]
			]
			*/
		]
	];
	protected $fillable = [
		'first_name',
		'last_name',
		'middle_name',
		'nickname',
		'lang',
		'home_page',
		'email',
		'wikipedia_url',
		'gender',
		'born_date',
		'born_place',
		'dead_date',
		'dead_place',
		'years_creation',
		'orig_last_name',
		'orig_first_name',
		'orig_middle_name'
	];

	protected $appends = ['fullName'];

	protected $dates = [
		'status_changed_at',
		'connected_at',
		'view_updated_at',
		'user_edited_at'
	];

	protected $casts = [
		'nickname' => 'string',
		'first_name' => 'string',
		'last_name' => 'string',
		'middle_name' => 'string'
	];

	protected $visible = [
		'id',
		'first_name',
		'last_name',
		'middle_name',
		'nickname',
		'books_count',
		'created_at',
		'deleted_at',
		'fullName',
		'name_helper',
		'create_user_id',
		'status',
		'status_changed_at',
		'like_count'
	];

	protected $perPage = 20;

	const FAVORITABLE_PIVOT_TABLE = 'user_authors';

	public static function boot()
	{
		parent::boot();

		//static::addGlobalScope(new CheckedScope);
	}

	static function cachedCountRefresh()
	{
		Cache::forever('authors_count_refresh', true);
	}

	public function searchableAs()
	{
		return 'author_index';
	}

	/**
	 * Get the indexable data array for the model.
	 *
	 * @return array
	 */
	public function toSearchableArray()
	{
		$array = $this->toArray();

		return [
			'id' => $array['id'],
			'first_name' => $array['first_name'],
			'last_name' => $array['last_name'],
			'middle_name' => $array['middle_name'],
			'nickname' => $array['nickname'],
			'name_helper' => $array['name_helper'],
			'create_user_id' => $array['create_user_id'],
			'status' => $array['status']
		];
	}

	public function scopeAny($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class)
			->withTrashed();
	}

	public function scopeNotMerged($query)
	{
		return $query->whereNull('merged_at');
	}

	public function scopeRememberCount($query, $minutes = 5, $refresh = false)
	{
		if ($refresh) Cache::forget('authors_count');

		return Cache::remember('authors_count', $minutes, function () use ($query) {
			return $query->count();
		});
	}

	public function scopeVoid($query)
	{
		return $query;
	}

	public function averageRatingForPeriod()
	{
		return $this->hasOne('App\AuthorAverageRatingForPeriod', 'author_id', 'id')->withDefault();
	}

	public function repeats()
	{
		return $this->belongsToMany('App\AuthorRepeat', 'author_repeat_pivots');
	}

	public function users_added_to_favorites()
	{
		return $this->belongsToMany('App\User', 'user_authors');
	}

	public function getAnyBooksByType($type)
	{
		return $this->any_books->filter(function ($item, $key) use ($type) {
			return $item->pivot->type == $type;
		});
	}

	public function books()
	{
		return $this->belongsToMany('App\Book', 'book_authors')
			->withPivot('type')
			->withPivotValue('type', AuthorEnum::Writer)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function written_books()
	{
		return $this->belongsToMany('App\Book', 'book_authors')
			->withPivot('type')
			->withPivotValue('type', AuthorEnum::Writer)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function translated_books()
	{
		return $this->belongsToMany('App\Book', 'book_authors')
			->withPivot('type')
			->withPivotValue('type', AuthorEnum::Translator)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function edited_books()
	{
		return $this->belongsToMany('App\Book', 'book_authors')
			->withPivot('type')
			->withPivotValue('type', AuthorEnum::Editor)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function compiled_books()
	{
		return $this->belongsToMany('App\Book', 'book_authors')
			->withPivot('type')
			->withPivotValue('type', AuthorEnum::Compiler)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function illustrated_books()
	{
		return $this->belongsToMany('App\Book', 'book_authors')
			->withPivot('type')
			->withPivotValue('type', AuthorEnum::Illustrator)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function photos()
	{
		return $this->hasMany('App\AuthorPhoto');
	}

	public function photo()
	{
		return $this->hasOne('App\AuthorPhoto', 'id', 'photo_id');
	}

	public function avatar()
	{
		return $this->belongsTo('App\AuthorPhoto', 'photo_id', 'id');
	}

	public function group()
	{
		return $this->hasOne('App\AuthorGroup', 'id', 'group_id');
	}

	public function managers()
	{
		return $this->morphMany('App\Manager', 'manageable');
	}

	public function moderators()
	{
		return $this->hasMany('App\AuthorModerator', 'author_id', 'id');
	}

	public function moderator_requests()
	{
		return $this->hasMany('App\ModeratorRequest', 'author_id', 'id');
	}

	public function forum()
	{
		return $this->hasOne('App\Forum', 'id', 'forum_id');
	}

	public function archived_books()
	{

	}

	public function scopeWordSimilaritySearch($query, $searchText)
	{
		$query->selectRaw("authors.*, word_similarity(name_helper, ?) AS rank", [$searchText]);

		$query->whereRaw("(name_helper) %> ?", [$searchText]);

		$query->orderBy("rank", 'desc');

		return $query;
	}

	public function scopeSimilaritySearch($query, $searchText)
	{
		$query->selectRaw("authors.*, similarity(name_helper, ?) AS rank", [$searchText]);

		$query->whereRaw("(name_helper) % ?", [$searchText]);

		$query->orderBy("rank", 'desc');

		return $query;
	}

	public function scopeFulltextSearch($query, $searchText)
	{
		$searchText = replaceSimilarSymbols($searchText);
		$Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

		$s = '';

		if ($Ar) {
			$s = "to_tsvector('english', \"name_helper\") ";
			$s .= " @@ to_tsquery(quote_literal(quote_literal(?)) || ':*')";
			return $query->whereRaw($s, implode('+', $Ar));
		}
		return $query;
	}

	public function scopeSearchByNameParts($query, $last_name = null, $first_name = null, $middle_name = null, $nickname = null)
	{
		$last_name = trim($last_name);
		$first_name = trim($first_name);
		$middle_name = trim($middle_name);
		$nickname = trim($nickname);

		$query->where(function ($query) use ($last_name, $first_name, $middle_name, $nickname) {
			$query->when($last_name, function ($query, $last_name) {
				return $query->where('last_name', 'ILIKE', $last_name);
			})->when($first_name, function ($query, $first_name) {
				return $query->where('first_name', 'ILIKE', $first_name);
			})->when($middle_name, function ($query, $middle_name) {
				return $query->where('middle_name', 'ILIKE', $middle_name);
			})->when($nickname, function ($query, $nickname) {
				return $query->where('nickname', 'ILIKE', $nickname);
			});
		});

		return $query;
	}

	public function getFullNameAttribute()
	{
		return trim(rtrim(rtrim($this->attributes['last_name'] . ' ' .
			$this->attributes['first_name'] . ' ' .
			$this->attributes['middle_name'] . ' ' .
			$this->attributes['nickname'])));
	}

	public function getOriginalFullNameAttribute()
	{
		$model = &$this;

		return trim(rtrim(rtrim($model->orig_last_name . ' ' . $model->orig_first_name) . ' ' . $model->orig_middle_name));
	}

	public function setNameAttribute($name)
	{
		$name = trim($name);

		$arr = preg_split("/[\s,]+/", $name);

		if (count($arr) < 1) return '';

		if (count($arr) == 1) {
			$this->attributes['nickname'] = $arr[0];
		} elseif (count($arr) == 2) {
			$this->attributes['last_name'] = @$arr[0];
			$this->attributes['first_name'] = @$arr[1];
		} elseif (count($arr) == 3) {
			$this->attributes['last_name'] = @$arr[0];
			$this->attributes['first_name'] = @$arr[1];
			$this->attributes['middle_name'] = @$arr[2];
		} else {
			$this->attributes['last_name'] = @$arr[0];
			$this->attributes['first_name'] = @$arr[1];
			$this->attributes['middle_name'] = @$arr[2];
			$this->attributes['nickname'] = @$arr[3];
		}

		$this->updateNameHelper();
	}

	public function updateNameHelper()
	{
		$searchText = replaceSimilarSymbols($this->name);
		$this->attributes['name_helper'] = mb_substr($searchText, 0, 255);
	}

	public function setNicknameAttribute($value)
	{
		$value = trim($value);

		$this->attributes['nickname'] = empty($value) ? '' : $value;

		$this->updateNameHelper();
	}

	public function getNicknameAttribute($value)
	{
		return empty($value) ? '' : $value;
	}

	public function setMiddleNameAttribute($value)
	{
		$value = trim($value);

		$this->attributes['middle_name'] = empty($value) ? '' : $value;

		$this->updateNameHelper();
	}

	public function getMiddleNameAttribute($value)
	{
		return empty($value) ? '' : $value;
	}

	public function setLastNameAttribute($value)
	{
		$value = trim($value);

		$this->attributes['last_name'] = empty($value) ? '' : $value;

		$this->updateNameHelper();
	}

	public function getLastNameAttribute($value)
	{
		return empty($value) ? '' : $value;
	}

	public function setFirstNameAttribute($value)
	{
		$value = trim($value);

		$this->attributes['first_name'] = empty($value) ? '' : $value;

		$this->updateNameHelper();
	}

	public function getFirstNameAttribute($value)
	{
		return empty($value) ? '' : $value;
	}

	public function getNameAttribute()
	{
		return trim(trim(trim(trim($this->last_name) . ' ' . $this->first_name) . ' ' . $this->middle_name) . ' ' . $this->nickname);
	}

	public function language()
	{
		return $this->hasOne('App\Language', 'code', 'lang');
	}

	public function edit_user()
	{
		return $this->hasOne('App\User', 'id', 'edit_user_id');
	}

	public function biography()
	{
		return $this->belongsTo('App\AuthorBiography', 'id', 'author_id');
	}

	public function user2()
	{
		return $this->belongsToMany('App\User', 'user_authors')->where("users.id", Auth::id());
	}

	public function library_users()
	{
		return $this->hasMany('App\UserAuthor');
	}

	public function user_status()
	{
		return $this->hasOne('App\AuthorStatus')
			->where('status', '!=', 'null')
			->where("user_id", Auth::id());
	}

	public function users_read_statuses()
	{
		return $this->hasMany('App\AuthorStatus')
			->where('status', '!=', 'null');
	}

	public function redirect_to_author()
	{
		return $this->belongsTo('App\Author', 'redirect_to_author_id');
	}

	/*
		public function getGenderAttribute($value)
		{
			if ($value == '1')
				return 'male';
			elseif ($value == '2')
				return 'female';
			else
				return 'unknown';
		}

		public function setGenderAttribute($value)
		{
			if ($value == 'male')
				$this->gender = 1;
			elseif ($value == 'female')
				$this->gender = 2;
			else
				$this->gender = 0;
		}
		*/

	public function attach_to_group($group)
	{
		$this->group_id = $group->id;

		if (Auth::check())
			$this->group_add_user = Auth::id();

		$this->save();
	}

	public function detach_from_group()
	{
		$group = $this->group;

		$this->group_id = null;

		if (Auth::check())
			$this->group_add_user = Auth::id();

		$this->save();

		$group->count = Author::where('group_id', $group->id)->count();

		$group->save();
	}

	/**
	 * Автор присоединен
	 *
	 * @return boolean
	 */

	public function isMerged()
	{
		return (bool)$this->merged_at;
	}

	public function isRatingChanged()
	{
		return (bool)$this->rating_changed;
	}

	public function ratingChanged()
	{
		$this->rating_changed = true;
	}

	public function flushUsersAddedToFavoritesNewBooksCount()
	{
		foreach ($this->users_added_to_favorites as $user)
			$user->flushCachedNewFavoriteAuthorsBooksCount();
	}

	public function getShareTitle()
	{
		$s = trans_choice('author.authors', 1) . ': ' . $this->name;

		if ($this->books_count > 0)
			$s .= ' - ' . $this->books_count . ' ' . trans_choice('book.books_count', $this->books_count);

		return $s;
	}

	public function getShareDescription()
	{
		$description = __('common.read') . ' ' . $this->books_count . ' ' . mb_strtolower(trans_choice('book.books_count', $this->books_count)) . ' 📚 ';

		if (!empty($this->biography))
			$description .= trim(mb_substr(trim(strip_tags(optional($this->biography)->text)), 0, 500));

		return $description;
	}

	public function getShareImage()
	{
		if (!empty($this->photo))
			return $this->photo->fullUrlMaxSize(500, 500);
		else
			return '';
	}

	public function sales_request()
	{
		return $this->hasMany('App\AuthorSaleRequest', 'author_id');
	}

	public function seller()
	{
		return optional($this->managers->where('can_sale', true)->first())->user;
	}

	public function isOnline()
	{
		if ($this->managers->isNotEmpty()) {
			foreach ($this->managers as $manager) {
				if ($manager->isAccepted() and $manager->isAuthorCharacter()) {
					if (!empty($manager->user)) {
						if ($manager->user->isOnline())
							return true;
					}
				}
			}
		}

		return false;
	}

	public function updateLang($refresh = false)
	{
		if (!is_null($this->lang)) {
			if (!$refresh)
				return $this->lang;
		}

		$books = $this->any_books()
			->select('id', 'ti_lb')
			->wherePivotIn('type', [AuthorEnum::Writer, AuthorEnum::Translator])
			->when($this->isAccepted() || $this->isSentForReview(), function ($query) {
				$query->acceptedAndSentForReview();
			})
			->get();

		$lang = key($books
			->groupBy('ti_lb')
			->map(function ($items) {
				return $items->count();
			})->sortByDesc(function ($item, $value) {
				return $item;
			})->take(1)->toArray());

		if (!empty($lang))
			$this->lang = $lang;
		else {
			if (is_null($this->lang))
				$this->lang = '';
		}

		return $this->lang;
	}

	public function any_books()
	{
		return $this->belongsToMany('App\Book', 'book_authors')
			->withPivot('type')
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function scopeOrderByRatingDesc($query)
	{
		return $query->orderBy('rating', 'desc');
	}

	public function scopeOrderByRatingDayDesc($query)
	{
		return $query->addSelect('author_average_rating_for_periods.day_rating')
			->join('author_average_rating_for_periods', 'authors.id', '=', 'author_average_rating_for_periods.author_id')
			->orderByWithNulls('author_average_rating_for_periods.day_rating', 'desc', 'last')
			->orderByWithNulls('author_average_rating_for_periods.all_rating', 'desc', 'last');
	}

	public function scopeOrderByRatingWeekDesc($query)
	{
		return $query->addSelect('author_average_rating_for_periods.week_rating')
			->join('author_average_rating_for_periods', 'authors.id', '=', 'author_average_rating_for_periods.author_id')
			->orderByWithNulls('author_average_rating_for_periods.week_rating', 'desc', 'last')
			->orderByWithNulls('author_average_rating_for_periods.all_rating', 'desc', 'last');
	}

	public function scopeOrderByRatingMonthDesc($query)
	{
		return $query->addSelect('author_average_rating_for_periods.month_rating')
			->join('author_average_rating_for_periods', 'authors.id', '=', 'author_average_rating_for_periods.author_id')
			->orderByWithNulls('author_average_rating_for_periods.month_rating', 'desc', 'last')
			->orderByWithNulls('author_average_rating_for_periods.all_rating', 'desc', 'last');
	}

	public function scopeOrderByRatingQuarterDesc($query)
	{
		return $query->addSelect('author_average_rating_for_periods.quarter_rating')
			->join('author_average_rating_for_periods', 'authors.id', '=', 'author_average_rating_for_periods.author_id')
			->orderByWithNulls('author_average_rating_for_periods.quarter_rating', 'desc', 'last')
			->orderByWithNulls('author_average_rating_for_periods.all_rating', 'desc', 'last');
	}

	public function scopeOrderByRatingYearDesc($query)
	{
		return $query->addSelect('author_average_rating_for_periods.year_rating')
			->join('author_average_rating_for_periods', 'authors.id', '=', 'author_average_rating_for_periods.author_id')
			->orderByWithNulls('author_average_rating_for_periods.year_rating', 'desc', 'last')
			->orderByWithNulls('author_average_rating_for_periods.all_rating', 'desc', 'last');
	}

	public function convertAllBooksInTheOldFormatToTheNewOne()
	{
		$books = $this->any_books()
			->where('online_read_new_format', false)
			->get();

		foreach ($books as $book) {
			$source = $book->source;

			if (!empty($source)) {
				$source->sentParsePages();
			}
		}
	}

	public function scopeOrderByRating($query)
	{
		return $query->orderBy("rating", 'desc');
	}
}
