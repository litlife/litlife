<?php

namespace App;

use App\Authentication\User as UserAuthenticatable;
use App\Enums\CacheTags;
use App\Enums\Gender;
use App\Enums\UserNameShowType;
use App\Enums\UserRelationType;
use App\Notifications\GroupAssignmentNotification;
use App\Notifications\NewReferredUserNotification;
use App\Traits\AdminNoteableTrait;
use App\Traits\Friendship;
use App\Traits\PaginatableTrait;
use App\Traits\Payments;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Spatie\Activitylog\Traits\CausesActivity;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;


/**
 * App\User
 *
 * @property int $id
 * @property int $user_group_id
 * @property int $ec
 * @property string $email
 * @property string|null $nick
 * @property int $old_last_activity
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $old_photo
 * @property string $password
 * @property int $gender
 * @property int $old_reg_date
 * @property int $old_new_message_count
 * @property string|null $old_reg_ip_old
 * @property string|null $old_permission
 * @property string|null $old_read_style
 * @property int $old_mail_notif
 * @property int $old_version
 * @property int $comment_count
 * @property int $user_lib_author_count
 * @property int $user_lib_book_count
 * @property int $user_lib_sequence_count
 * @property int $forum_message_count
 * @property \Illuminate\Support\Carbon|null $born_date
 * @property int $born_date_show
 * @property int $book_rate_count
 * @property int $book_read_count
 * @property int $book_read_later_count
 * @property int $book_read_now_count
 * @property string|null $city
 * @property int $name_show_type
 * @property int $book_read_not_complete_count
 * @property int $old_hide
 * @property int $old_hide_time
 * @property int $old_hide_user
 * @property int $old_book_file_count
 * @property int $old_profile_comment_count
 * @property int $subscriptions_count
 * @property int $subscribers_count
 * @property int $friends_count
 * @property int $blacklists_count
 * @property int $old_hide_email
 * @property int $old_invite_send
 * @property int $book_read_not_read_count
 * @property string|null $text_status
 * @property int|null $avatar_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property string|null $suspended_at
 * @property int $photos_count
 * @property string|null $user_edited_at Время когда пользователь отредактировал
 * @property string|null $url_address
 * @property int $topics_count Количество тем созданных пользователем
 * @property int $confirmed_mailbox_count Количество подтвержденных почтовых ящиков
 * @property int $achievements_count
 * @property string|null $name_helper Вспомогательный столбец для быстрого trgm поиска
 * @property string $reg_ip
 * @property int $admin_notes_count
 * @property int|null $miniature_image_id
 * @property float $balance Баланс пользователя в рублях
 * @property int|null $referred_by_user_id ID пользователя по рекомендации которого зарегистрировался этот пользователь
 * @property bool|null $refresh_counters
 * @property-read \App\UserAccountPermission $account_permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $actions
 * @property-read \App\AdminNote|null $admin_note
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $admin_notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserAuthFail[] $auth_fails
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserAuthLog[] $auth_logs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorStatus[] $author_read_statuses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $authors
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $authors_read_statuses
 * @property-read \App\UserPhoto|null $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $blacklists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Blog[] $blog
 * @property-read \App\Book|null $book
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $bookThatRated
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookStatus[] $book_read_statuses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookmarkFolder[] $bookmark_folders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bookmark[] $bookmarks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSearchSetting[] $booksSearchSettings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books_read_statuses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorRepeat[] $created_author_repeats
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $created_authors
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookFile[] $created_book_files
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $created_books
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Collection[] $created_collections
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Forum[] $created_forums
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Keyword[] $created_keywords
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Sequence[] $created_sequences
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Topic[] $created_topics
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Blog[] $created_wall_posts
 * @property-read \App\UserData $data
 * @property-read \App\UserEmailNotificationSetting $email_notification_setting
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserEmail[] $emails
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSubscriptionsEventNotification[] $eventNotificationSubscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Collection[] $favorite_collections
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $friends
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Genre[] $genre_blacklist
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserGenreBlacklist[] $genres_blacklist
 * @property-read mixed $born_date_format
 * @property-read mixed $group
 * @property-read mixed $name
 * @property-read mixed $user_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserGroup[] $groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Image[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserIncomingPayment[] $incoming_payment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $latest_admin_notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AchievementUser[] $latest_user_achievements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Manager[] $managers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $messages
 * @property-read \App\Image|null $miniature
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserNote[] $notes
 * @property-read \App\UserEmail|null $notice_email
 * @property-read DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \App\UserOnModeration|null $on_moderate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserOutgoingPayment[] $outgoing_payment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Participation[] $participations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PasswordReset[] $password_resets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPaymentTransaction[] $payment_transactions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPhoto[] $photos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $purchased_books
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPurchase[] $purchases
 * @property-read \App\UserReadStyle $readStyle
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserMoneyTransfer[] $receiving
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $refered_users
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $referred_by_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserRelation[] $relationship
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserRelation[] $relationshipReverse
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookReadRememberPage[] $remembered_pages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPurchase[] $sales
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SearchQueriesLog[] $searchQueries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $sent_messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Sequence[] $sequences
 * @property-read \App\UserSetting $setting
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookSimilarVote[] $similar_book_votes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSocialAccount[] $social_accounts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $sold_books
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserTopicSubscription[] $subscribed_topics
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $subscribers
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $subscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSurvey[] $surveys
 * @property-read \App\Bookmark|null $thisPageInBookmarks
 * @property-read \App\UserToken|null $token
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Topic[] $topics
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserMoneyTransfer[] $transfers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AchievementUser[] $user_achievements
 * @property-read \App\UserGroup $user_group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookVote[] $votes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPaymentDetail[] $wallets
 * @method static \Illuminate\Database\Eloquent\Builder|User active()
 * @method static \Illuminate\Database\Eloquent\Builder|User any()
 * @method static \Illuminate\Database\Eloquent\Builder|User female()
 * @method static \Illuminate\Database\Eloquent\Builder|User fulltextSearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|User male()
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User online()
 * @method static Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User orderByPostsCountDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|User orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User rememberCount($minutes = 5, $refresh = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User similaritySearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|User void()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAchievementsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdminNotesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlacklistsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookRateCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadLaterCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadNotCompleteCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadNotReadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadNowCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBornDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBornDateShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmedMailboxCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmpty($column)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereForumMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFriendsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMiniatureImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNameHelper($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNameShowType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickEquals($nick)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldBookFileCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldHideEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldHideTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldHideUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldInviteSend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldMailNotif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldNewMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldPermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldProfileCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldReadStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldRegDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldRegIpOld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOldVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhotosCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReferredByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRefreshCounters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubscribersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubscriptionsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTextStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTextStatusLike($text)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTopicsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUrlAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserLibAuthorCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserLibBookCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserLibSequenceCount($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @mixin Eloquent
 */
class User extends Authenticatable
{
	use SoftDeletes;
	use UserAuthenticatable;
	use CausesActivity;
	use Notifiable;
	use HasRelationships;
	use Friendship;
	use AdminNoteableTrait;
	use Payments;
	use PaginatableTrait;

	//use CacheQueryBuilder;

	public $attributes =
		[
			'user_group_id' => 0,
			'name_show_type' => 0,
			'gender' => Gender::unknown
		];
	protected $guarded = ['id'];
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable = [
		'nick',
		'first_name',
		'last_name',
		'middle_name',
		'gender',
		'born_date',
		'born_date_show',
		'password'
	];
	protected $dates = [
		'reg_date',
		'last_activity',
		'hide_time',
		'created_at',
		'last_activity_at',
		'born_date'
	];
	protected $appends = ['userName'];
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token'
	];

	static function flushNotViewedFriendsNewsCountById($id)
	{
		Cache::tags([CacheTags::FriendsAndSubscriptionsNotViewedNewsCount])->pull($id);
	}

	static function cachedCountRefresh()
	{
		Cache::forever('users_count_refresh', true);
	}

	static function cachedOnlineCountRefresh()
	{
		Cache::forever('users_online_count_refresh', true);
	}

	/**
	 * Route notifications for the mail channel.
	 *
	 * @param Notification $notification
	 * @return string
	 */
	public function routeNotificationForMail($notification)
	{
		return optional($this->notice_email)->email ?? null;
	}

	public function getNameShowTypeAttribute($value)
	{
		return UserNameShowType::getKey($value);
	}

	public function setNameShowTypeAttribute($value)
	{
		$this->attributes['name_show_type'] = UserNameShowType::getValue($value);
	}

	public function scopeAny($query)
	{
		return $query->withTrashed();
	}

	public function notice_email()
	{
		return $this->hasOne('App\UserEmail', 'user_id')->where('notice', true);
	}

	public function token()
	{
		return $this->hasOne('App\UserToken', 'user_id');
	}

	public function avatar()
	{
		return $this->belongsTo('App\UserPhoto', 'avatar_id', 'id');
	}

	public function miniature()
	{
		return $this->hasOne('App\Image', 'id', 'miniature_image_id');
	}

	public function data()
	{
		return $this->hasOne('App\UserData', 'user_id', 'id')
			->withDefault();
	}

	public function readStyle()
	{
		return $this->hasOne('App\UserReadStyle', 'user_id', 'id')
			->withDefault();
	}

	public function blog()
	{
		return $this->hasMany('App\Blog', 'blog_user_id', 'id');
	}

	public function created_wall_posts()
	{
		return $this->hasMany('App\Blog', 'create_user_id', 'id');
	}

	public function comments()
	{
		return $this->hasMany('App\Comment', 'create_user_id', 'id');
	}

	public function posts()
	{
		return $this->hasMany('App\Post', 'create_user_id', 'id');
	}

	public function images()
	{
		return $this->hasMany('App\Image', 'create_user_id', 'id');
	}

	public function setting()
	{
		return $this->hasOne('App\UserSetting', "user_id", "id")->withDefault();
	}

	public function bookmarks()
	{
		return $this->hasMany('App\Bookmark', 'create_user_id', 'id');
	}

	public function notes()
	{
		return $this->hasMany('App\UserNote', 'create_user_id', 'id');
	}

	public function remembered_pages()
	{
		return $this->hasMany('App\BookReadRememberPage', 'user_id', 'id');
	}

	public function thisPageInBookmarks()
	{
		return $this->hasOne('App\Bookmark', 'create_user_id', 'id')
			->where('url', request()->getRequestUri());
	}

	public function bookmark_folders()
	{
		return $this->hasMany('App\BookmarkFolder', 'create_user_id', 'id');
	}

	public function bookThatRated()
	{
		return $this->belongsToMany('App\Book', 'book_votes', 'create_user_id')
			->whereNull('book_votes.deleted_at');
	}

	public function genre_blacklist()
	{
		return $this->belongsToMany('App\Genre', 'user_genre_blacklist')
			->notMain();
	}

	public function genres_blacklist()
	{
		return $this->hasMany('App\UserGenreBlacklist', 'user_id');
	}

	public function account_permissions()
	{
		return $this->hasOne('App\UserAccountPermission', 'user_id', 'id')->withDefault();
	}

	public function email_notification_setting()
	{
		return $this->hasOne('App\UserEmailNotificationSetting', 'user_id', 'id')->withDefault();
	}

	public function created_books()
	{
		return $this->hasMany('App\Book', 'create_user_id');
	}

	public function created_authors()
	{
		return $this->hasMany('App\Author', 'create_user_id');
	}

	public function created_sequences()
	{
		return $this->hasMany('App\Sequence', 'create_user_id');
	}

	public function created_book_files()
	{
		return $this->hasMany('App\BookFile', 'create_user_id');
	}

	public function created_author_repeats()
	{
		return $this->hasMany('App\AuthorRepeat', 'create_user_id');
	}

	public function created_keywords()
	{
		return $this->hasMany('App\Keyword', 'create_user_id');
	}

	public function authors()
	{
		return $this->belongsToMany('App\Author', 'user_authors');
	}

	public function subscriptions_and_friends_comments()
	{
		return $this->hasManyDeep('App\Comment', ['App\UserRelation'],
			[
				'user_id',
				'create_user_id'
			],
			[
				'id',
				'user_id2',

			])->whereIn('user_relations.status', [UserRelationType::Friend, UserRelationType::Subscriber]);
	}

	public function sequences()
	{
		return $this->belongsToMany('App\Sequence', 'user_sequences');
	}

	public function scopeVoid($query)
	{
		return $query;
	}

	public function scopeMale($query)
	{
		return $query->where("gender", 1);
	}

	public function scopeFemale($query)
	{
		return $query->where("gender", 2);
	}

	public function scopeWhereEmpty($query, $column)
	{
		return $query->where($column, '=', '')->orWhereNull($column);
	}

	public function scopeActive($query)
	{
		return $query->whereNull('suspended_at');
	}

	public function getUserNameAttribute()
	{
		switch ($this->name_show_type) {
			case 'FullLastNameFirstName':
				$s = ($this->nick . ' ' . $this->last_name . ' ' . $this->first_name);
				break;
			case 'FullFirstNameLastName':
				$s = ($this->nick . ' ' . $this->first_name . ' ' . $this->last_name);
				break;
			case 'Nick':
				$s = ($this->nick);
				break;
			case 'LastNameFirstName':
				$s = ($this->last_name . ' ' . $this->first_name);
				break;
			case 'FirstNameLastName':
				$s = ($this->first_name . ' ' . $this->last_name);
				break;
			case 'FirstnameNicknameLastname':
				$s = ($this->first_name . ' ' . $this->nick . ' ' . $this->last_name);
				break;
			case 'LastnameNicknameFirstname':
				$s = ($this->last_name . ' ' . $this->nick . ' ' . $this->first_name);
				break;
			case 'NicknameFirstname':
				$s = ($this->nick . ' ' . $this->first_name);
				break;
			case 'FirstnameNickname':
				$s = ($this->first_name . ' ' . $this->nick);
				break;
			default:
				$s = ($this->nick . ' ' . $this->last_name . ' ' . $this->first_name);
				break;
		}

		$s = trim($s);

		if ($s == '')
			$s = $this->nick . ' ' . $this->last_name . ' ' . $this->first_name;

		return $s;
	}

	public function setNickAttribute($value)
	{
		$value = trim($value);

		$this->attributes['nick'] = empty($value) ? '' : $value;

		$this->updateNameHelper();
	}

	public function updateNameHelper()
	{
		$this->attributes['name_helper'] = mb_substr($this->name, 0, 255);
	}

	public function getNickttribute($value)
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
		return trim(trim(trim(trim($this->last_name) . ' ' . $this->first_name) . ' ' . $this->middle_name) . ' ' . $this->nick);
	}

	public function getGenderAttribute($value)
	{
		if ($value == Gender::male)
			return 'male';
		elseif ($value == Gender::female)
			return 'female';
		else
			return 'unknown';
	}

	public function setGenderAttribute($value)
	{
		if ($value == 'male')
			$this->attributes['gender'] = Gender::male;
		elseif ($value == 'female')
			$this->attributes['gender'] = Gender::female;
		else
			$this->attributes['gender'] = Gender::unknown;
	}

	public function scopeFulltextSearch($query, $searchText)
	{
		$Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

		$s = '';

		if ($Ar) {
			$s = "to_tsvector('english', \"name_helper\") ";
			$s .= " @@ to_tsquery(quote_literal(quote_literal(?)) || ':*')";
			return $query->whereRaw($s, implode('+', $Ar));
		}
		return $query;
	}

	public function scopeSimilaritySearch($query, $searchText)
	{
		/*
		 $query->selectRaw("*, (similarity(first_name, '" . $searchText . "')  + similarity(last_name, '" . $searchText . "') + similarity(middle_name, '" . $searchText . "') + similarity(nickname, '" . $searchText . "')) AS rank");

		 $query->whereRaw("(first_name || ' ' || last_name || ' ' || middle_name|| ' ' || nickname) % '" . $searchText . "'");

		 $query->orderBy("rank", 'desc');
		 */

		$query->selectRaw("users.*, similarity(name_helper, ?) AS rank", [$searchText]);

		$query->whereRaw("(name_helper) % ?", [$searchText]);

		$query->orderBy("rank", 'desc');

		return $query;
	}

	public function scopeRememberCount($query, $minutes = 5, $refresh = false)
	{
		if ($refresh) Cache::forget('users_count');

		return Cache::remember('users_count', $minutes, function () use ($query) {
			return $query->count();
		});
	}

	public function getBornDateFormatAttribute()
	{
		if (isset($this->born_date)) {
			switch ($this->born_date_show) {
				case '0':
					return Date::parse($this->born_date)->format('j F Y') . '';

					break;
				case '1':

					return Date::parse($this->born_date)->format('j F');

					break;
				case '2':

					break;
			}
		}
	}

	public function bookVote($book)
	{
		if (!isset($this->bookVote_cache)) {
			$this->bookVote_cache = $this->votes()->where('book_id', $book->id)->first();
		}
		return $this->bookVote_cache;
	}

	public function votes()
	{
		return $this->hasMany('App\BookVote', 'create_user_id');
	}

	/*
		public function scopeFulltextSearch($query, $searchText)
		{
			$Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

			$s = '';

			if ($Ar) {
				$s = "to_tsvector('english', \"last_name\" || ' ' || \"first_name\" || ' ' || \"middle_name\" || ' ' || \"nick\" )  ";
				$s .= " @@ to_tsquery('english', quote_literal(quote_literal(?)))";

				return $query->whereRaw($s, [implode('&', $Ar)]);
			}
			return $query;
		}
	*/

	public function book()
	{
		return $this->hasOne('App\Book', 'id', 'book_id');
	}

	public function sent_messages()
	{
		return $this->hasMany('App\Message', 'sender_id', 'id');
	}

	public function messages()
	{
		return $this->hasMany('App\Message', 'create_user_id', 'id');
	}

	public function authors_read_statuses()
	{
		return $this->belongsToMany('App\Author', 'author_statuses');
	}

	public function books_read_statuses()
	{
		return $this->belongsToMany('App\Book', 'book_statuses');
	}

	public function author_read_statuses()
	{
		return $this->hasMany('App\AuthorStatus', 'user_id');
	}

	public function book_read_statuses()
	{
		return $this->hasMany('App\BookStatus', 'user_id');
	}

	public function on_moderate()
	{
		return $this->hasOne('App\UserOnModeration', 'user_id', 'id');
	}

	public function auth_fails()
	{
		return $this->hasMany('App\UserAuthFail', 'user_id');
	}

	public function auth_logs()
	{
		return $this->hasMany('App\UserAuthLog', 'user_id');
	}

	public function eventNotificationSubscriptions()
	{
		return $this->hasMany('App\UserSubscriptionsEventNotification', 'notifiable_user_id');
	}

	public function unsuspend()
	{
		$this->suspended_at = null;
	}

	public function suspend()
	{
		$this->suspended_at = Carbon::now('UTC');
	}

	public function isActive()
	{
		if ($this->trashed() or $this->isSuspended())
			return false;
		else
			return true;
	}

	public function isSuspended()
	{
		return (boolean)$this->suspended_at;
	}

	public function update_activity()
	{
		$dt = $this->last_activity_at;

		if ((!$dt) or ($dt->addMinutes(config('litlife.user_last_activity'))->timestamp < Carbon::now('UTC')->timestamp)) {
			$this->last_activity_at = Carbon::now('UTC');
			$this->save();
		}
	}

	public function isOnline()
	{
		$dt = $this->last_activity_at;

		if (!$dt)
			return false;

		if ($dt->addMinutes(config('litlife.user_last_activity'))->timestamp >= Carbon::now('UTC')->timestamp)
			return true;

		return false;
	}

	public function scopeOnline($query)
	{
		$time = now()->subMinutes(config('litlife.user_last_activity'))
			->subSeconds(5);

		return $query->where("last_activity_at", '>', $time);
	}

	public function ads_enable()
	{
		if ($this->getPermission('NotShowAd'))
			// у пользователя включено не показывать объявления
			// тогда пользователь не должен видеть их
			return false;
		else
			return true;
	}

	public function getPermission($value): bool
	{
		$value = Str::snake($value);

		foreach ($this->groups as $group) {
			if (optional($group)->$value)
				return true;
		}

		return false;
	}

	public function user_achievements()
	{
		return $this->hasMany('App\AchievementUser');
	}

	public function password_resets()
	{
		return $this->hasMany('App\PasswordReset');
	}

	public function latest_user_achievements()
	{
		return $this->hasMany('App\AchievementUser')
			->latest();
	}

	public function social_accounts()
	{
		return $this->hasMany('App\UserSocialAccount');
	}

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = md0($value);
	}

	public function friendsNews()
	{
		$query = $this->relationship()->whereIn('user_relations.status', [UserRelationType::Subscriber, UserRelationType::Friend])
			->select(['user_relations.user_id2']);

		return Blog::whereRaw('"create_user_id" IN (' . $query->toSql() . ')', $query->getBindings())
			->whereRaw('"blog_user_id" = "create_user_id"');
	}

	public function scopeWherePassword($query, $value)
	{
		return $query->where('password', md0($value));
	}

	public function getNotViewedFriendsNewsCount()
	{
		return Cache::tags([CacheTags::FriendsAndSubscriptionsNotViewedNewsCount])
			->remember($this->id, 3600, function () {

				$count = Blog::whereHas('owner.relationshipReverse',
					function ($query) {
						$query->whereIn('status', [UserRelationType::Subscriber, UserRelationType::Friend])
							->where('user_id', $this->id)
							->select('user_id2');
					})
					->where('create_user_id', '!=', $this->id)
					->where('created_at', '>', $this->data->last_news_view_at ?? $this->created_at)
					->count();

				return $count;

				/*
				return $this->friendsNews()
					// берем последнюю дату просмотра, либо дату создания аккаунта
					->where('created_at', '>', $this->data->last_news_view_at ?? $this->created_at)
					->count();
				*/
			});
	}

	public function flushNotViewedFriendsNewsCount()
	{
		Cache::tags([CacheTags::FriendsAndSubscriptionsNotViewedNewsCount])->pull($this->id);
	}

	public function getNewMessagesCount()
	{
		return Cache::tags([CacheTags::NewPrivateMessagesCount])->remember($this->id, 3600, function () {
			return $this->participations()
				->sum('new_messages_count');
		});
	}

	public function participations()
	{
		return $this->hasMany('App\Participation', 'user_id');
	}

	public function flushCacheNewMessages()
	{
		Cache::tags([CacheTags::NewPrivateMessagesCount])->pull($this->id);
	}

	public function itsMe()
	{
		if (auth()->check() and $this->id == auth()->id())
			return true;
		else
			return false;
	}

	public function isHaveConfirmedMailbox()
	{
		if (empty($this->confirmed_mailbox_count))
			return false;
		else
			return true;
	}

	public function setBornDateAttribute($value)
	{
		if (is_object($value)) {
			$this->attributes['born_date'] = $value->format('Y-m-d');
		} elseif (!empty($value)) {
			$this->attributes['born_date'] = Carbon::parse($value)
				->format('Y-m-d');
		}
	}

	public function similar_book_votes()
	{
		return $this->hasMany('App\BookSimilarVote', 'create_user_id');
	}

	public function created_forums()
	{
		return $this->hasMany('App\Forum', 'create_user_id');
	}

	public function likes()
	{
		return $this->hasMany('App\Like', 'create_user_id');
	}

	/*
    * Возвращает количество новых созданных диалогов с пользователями за послдений час
    * @return bool
    *
    */
	function latest_new_particaipations_for_hour_count(): int
	{
		return $this->participations()
			->whereHas('latest_message',
				function ($query) {
					$query->where('create_user_id', $this->id);
				})
			->where('created_at', '>', now()->subHour())
			->count();
	}

	public function getNewFavoriteAuthorsBooksCount(): int
	{
		return Cache::tags([CacheTags::NewFavoriteAuthorsBooksCount])->remember($this->id, 86400, function () {

			$count = $this->getFavoriteAuthorBooksBuilder()
				->where('books.create_user_id', '!=', $this->id)
				->where('books.status_changed_at', '>', $this->data->favorite_authors_books_latest_viewed_at)
				->count();

			return $count;
		});
	}

	public function getFavoriteAuthorBooksBuilder(): \Illuminate\Database\Eloquent\Builder
	{
		return Book::whereHas('authors', function (\Illuminate\Database\Eloquent\Builder $query) {
			//$query->whereIn('author_id', $this->authors()->select('authors.id')->getQuery());
			$query->whereHas('users_added_to_favorites', function (\Illuminate\Database\Eloquent\Builder $query) {
				$query->where('user_id', $this->id);
			});
		})->acceptedOrBelongsToUser($this);
	}

	/*
		public function favorite_authors_books()
		{
			return $this->hasManyDeep(Book::class, ['user_authors', Author::class, 'book_authors']);
		}
		*/

	public function flushCachedNewFavoriteAuthorsBooksCount()
	{
		Cache::tags([CacheTags::NewFavoriteAuthorsBooksCount])->pull($this->id);
	}

	public function getUnreadNotificationsCount(): int
	{
		return Cache::tags([CacheTags::UnreadNotifications])->remember($this->id, 86400, function () {

			$count = $this->unreadNotifications()->count();

			return $count;
		});
	}

	public function flushCachedUnreadNotificationsCount()
	{
		Cache::tags([CacheTags::UnreadNotifications])->pull($this->id);
	}

	public function purchasedBookCountRefresh()
	{
		$this->data->books_purchased_count = $this->purchases()->notCanceled()->whereHasMorph('purchasable', 'App\Book')->count();
		$this->push();
	}

	public function shopEnable()
	{
		$this->group->shop_enable = true;
		$this->push();
	}

	public function isOnModeration(): bool
	{
		if ($this->on_moderate)
			return true;
		else
			return false;
	}

	public function getFavoriteBooksWithUpdatesCount(): int
	{
		return Cache::tags([CacheTags::FavoriteBooksWithUpdatesCount])->remember($this->id, 86400, function () {

			$count = $this->favoriteBooksWithUpdates()
				->count();

			/*
						$count = $this->books()
							->join('book_read_remember_pages', function ($join) {
								$join->on('books.id', '=', 'book_read_remember_pages.book_id')
									->where('book_read_remember_pages.user_id', $this->id);
							})
							->where('books.characters_count', '>', \Illuminate\Support\Facades\DB::raw('book_read_remember_pages.characters_count'))
							->count();
			*/
			return $count;
		});
	}

	public function favoriteBooksWithUpdates()
	{
		return $this->books()
			->whereHas('remembered_pages', function ($query) {
				$query->where('books.characters_count', '>', DB::raw('book_read_remember_pages.characters_count'))
					->where('book_read_remember_pages.user_id', $this->id);
			});
	}

	public function books()
	{
		return $this->belongsToMany('App\Book', 'user_books');
	}

	public function flushCachedFavoriteBooksWithUpdatesCount()
	{
		Cache::tags([CacheTags::FavoriteBooksWithUpdatesCount])->pull($this->id);
	}

	public function referred_by_user()
	{
		return $this->belongsToMany('App\User', 'referred_users', 'referred_user_id', 'referred_by_user_id')
			->withPivot('comission_buy_book')
			->withPivot('comission_sell_book')
			->withTimestamps();
	}

	public function setReferredByUserId($id = null)
	{
		if (!empty($id)) {
			if ($id instanceof User)
				$reffered_by_user = $id;
			else
				$reffered_by_user = User::find(intval($id));

			if (!empty($reffered_by_user)) {
				ReferredUser::firstOrCreate(
					[
						'referred_by_user_id' => $reffered_by_user->id,
						'referred_user_id' => $this->id,
					]
				);

				$reffered_by_user->refer_users_refresh();
				$reffered_by_user->notify(new NewReferredUserNotification($this));
			}
		}
	}

	public function refer_users_refresh()
	{
		$this->data->refer_users_count = $this->refered_users()->count();
		$this->data->save();
	}

	public function refered_users()
	{
		return $this->belongsToMany('App\User', 'referred_users', 'referred_by_user_id', 'referred_user_id')
			->withPivot('comission_buy_book')
			->withPivot('comission_sell_book')
			->withTimestamps();
	}

	public function user_group()
	{
		return $this->belongsTo('App\UserGroup', 'user_group_id', 'id');
	}

	public function getGroupAttribute()
	{
		return $this->groups->first();
	}

	public function getGroupStatus($remove = null)
	{
		$array = [];

		if (!empty($remove)) {
			$remove = mb_strtolower($remove);
		}

		if ($this->groups->isNotEmpty()) {
			foreach ($this->groups->where('show', true) as $group) {
				if (!empty($group->name)) {
					if (mb_strtolower($group->name) != $remove) {
						$array[] = trim($group->name);
					}
				}
			}
		}

		foreach (explode(',', $this->text_status) as $status) {
			if (!empty($status)) {
				if (mb_strtolower($status) != $remove) {
					$array[] = trim($status);
				}
			}
		}

		return $array;
	}

	public function subscribed_topics()
	{
		return $this->hasMany('App\UserTopicSubscription', 'user_id', 'id');
	}

	public function refreshCreatedTopicsCount()
	{
		$this->topics_count = $this->topics()->count();
	}

	public function topics()
	{
		return $this->hasMany('App\Topic', 'create_user_id', 'id');
	}

	public function created_topics()
	{
		return $this->hasMany('App\Topic', 'create_user_id', 'id');
	}

	public function refreshPhotosCount()
	{
		$this->photos_count = $this->photos()->count();
	}

	public function photos()
	{
		return $this->hasMany('App\UserPhoto', 'user_id', 'id');
	}

	public function searchQueries()
	{
		return $this->hasMany('App\SearchQueriesLog', 'user_id', 'id');
	}

	public function refreshConfirmedMailboxCount()
	{
		$this->confirmed_mailbox_count = $this->emails()->confirmed()->count();
	}

	public function emails()
	{
		return $this->hasMany('App\UserEmail', 'user_id')->latest();
	}

	public function appendTextStatus($text)
	{
		$text = trim($text);

		$array = explode(',', $this->text_status);

		$array = array_map(function ($value) {
			return trim($value);
		}, $array);

		$array[] = $text;

		$array = array_filter($array);
		$array = array_unique($array);

		$this->text_status = implode(', ', $array);
	}

	public function setTextStatusAttribute($text)
	{
		$array = explode(',', $text);

		$array = array_map(function ($value) {
			return trim($value);
		}, $array);

		$array = array_unique($array);

		$this->attributes['text_status'] = implode(', ', $array);
	}

	public function hasTextStatus($text)
	{
		$text = trim($text);

		$array = explode(',', $this->text_status);

		$array = array_map(function ($value) {
			return trim($value);
		}, $array);

		$array = array_unique($array);

		foreach ($array as $key => $value) {
			if (mb_strtolower($text) == mb_strtolower($value))
				return true;
		}

		return false;
	}

	public function detachUserGroup(UserGroup $group)
	{
		$this->groups()->detach([$group->id]);
	}

	public function groups()
	{
		return $this->belongsToMany('App\UserGroup', 'user_group_pivot', 'user_id', 'user_group_id')
			->withTimestamps();
	}

	public function scopeWhereTextStatusLike($query, $text)
	{
		return $query->where('text_status', 'ilike', '%' . $text . '%');
	}

	public function hasUserGroup(UserGroup $group)
	{
		if ($this->groups->where('id', $group->id)->first())
			return true;
		else
			return false;
	}

	public function attachAuthorGroup()
	{
		$this->attachUserGroupByNameIfExists('Автор');
	}

	public function attachUserGroupByNameIfExists($name)
	{
		$group = UserGroup::whereName($name)->first();

		if ($group) {
			$this->attachUserGroup($group);
		}
	}

	public function attachUserGroup(UserGroup $group)
	{
		$this->groups()->syncWithoutDetaching([$group->id]);
		$this->removeTextStatus($group->name);

		$this->notify(new GroupAssignmentNotification(collect([$group])));
	}

	public function removeTextStatus($text)
	{
		$text = trim($text);

		$array = explode(',', $this->text_status);

		$array = array_map(function ($value) {
			return trim($value);
		}, $array);

		$array = array_filter($array, function ($value) use ($text) {
			return mb_strtolower($value) != mb_strtolower($text);
		});

		$array = array_unique($array);

		$this->text_status = implode(', ', $array);
	}

	public function detachAuthorGroup()
	{
		$this->detachUserGroupByNameIfExists('Автор');
	}

	public function detachUserGroupByNameIfExists($name)
	{
		$groups = $this->groups()->whereName($name)->get();

		if ($groups->count() > 0)
			$this->groups()->detach($groups->pluck('id')->toArray());
	}

	public function isAuthorGroupMustAttached()
	{
		$managers = $this->managers()
			->with('manageable')
			->accepted()
			->get();

		$manager = $managers->first(function ($item, $key) {
			if (empty($item->user))
				return false;

			if (empty($item->manageable))
				return false;

			if ($item->trashed())
				return false;

			if ($item->manageable->isMerged())
				return false;

			if (!empty($item->manageable->redirect_to_author_id))
				return false;

			return true;
		});

		if (empty($manager))
			return false;
		else
			return true;
	}

	public function managers()
	{
		return $this->hasMany('App\Manager');
	}

	public function refreshFavoriteCollectionsCount()
	{
		$this->data->favorite_collections_count = $this->favorite_collections()->count();
	}

	public function favorite_collections()
	{
		return $this->belongsToMany('App\Collection', 'user_favorite_collections');
	}

	public function refreshCreatedCollectionsCount()
	{
		$this->data->created_collections_count = $this->created_collections()->count();
	}

	public function created_collections()
	{
		return $this->hasMany('App\Collection', 'create_user_id');
	}

	public function scopeWhereNickEquals($query, $nick)
	{
		return $query->where('nick', 'ilike', preg_quote($nick));
	}

	public function scopeOrderByWithNulls($query, $column, $sort = 'asc', $nulls = 'first')
	{
		$sort = (mb_strtolower($sort) == 'asc') ? 'asc' : 'desc';
		$nulls = (mb_strtolower($nulls) == 'first') ? 'first' : 'last';

		return $query->orderByRaw('"' . $column . '" ' . $sort . ' nulls ' . $nulls . '');
	}

	/*
		public function getPrivateChaptersCount()
		{
			return Cache::tags([CacheTags::UserPrivateChaptersCount])->remember($this->id, 86400, function () {

				$managers = $this->managers()
					->accepted()
					->authors()
					->with('manageable')
					->get();

				return $count;
			});
		}

		public function flushPrivateChaptersCount()
		{
			Cache::tags([CacheTags::UserPrivateChaptersCount])->pull($this->id);
		}
		*/

	public function booksSearchSettings()
	{
		return $this->hasMany('App\UserSearchSetting', 'user_id');
	}

	public function surveys()
	{
		return $this->hasMany('App\UserSurvey', 'create_user_id', 'id');
	}

	public function scopeOrderByPostsCountDesc($query)
	{
		return $query->orderBy('forum_message_count', 'desc');
	}

	public function hasNoticeEmail(): bool
	{
		return (bool)optional($this->notice_email)->email;
	}

	public function getAge()
	{
		if ($this->born_date instanceof Carbon)
			return $this->born_date->diffInYears();
		else
			return null;
	}

	public function getJsonForYandexMetrika()
	{
		$array = [];

		if ($this->getAge())
			$array['age'] = (int)$this->getAge();

		if ($this->id) {
			$array['UserID'] = (int)$this->id;
			$array['gender'] = $this->gender;
		}

		$object = (object)$array;

		return json_encode($object, JSON_NUMERIC_CHECK);
	}

	public function createdSupportRequests()
	{
		return $this->hasMany('App\SupportRequest', 'create_user_id');
	}

	public function createdSupportMessages()
	{
		return $this->hasMany('App\SupportRequestMessage', 'create_user_id');
	}
}
