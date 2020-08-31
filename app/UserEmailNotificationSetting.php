<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserEmailNotificationSetting
 *
 * @property int $user_id
 * @property bool $news Когда появляется новость от администрации
 * @property bool $private_message Когда приходит личное сообщение
 * @property bool $forum_reply Когда приходит ответ на сообщение на форуме
 * @property bool $wall_message Когда появляется новое сообщение на стене
 * @property bool $comment_reply Когда кто-то отвечает на комментарий
 * @property bool $wall_reply Когда кто-то отвечает на мое сообщение на стене
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $db_forum_reply
 * @property bool $db_wall_message
 * @property bool $db_comment_reply
 * @property bool $db_wall_reply
 * @property bool $db_book_finish_parse
 * @property bool $db_like
 * @property bool $db_comment_vote_up
 * @method static Builder|UserEmailNotificationSetting newModelQuery()
 * @method static Builder|UserEmailNotificationSetting newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserEmailNotificationSetting query()
 * @method static Builder|Model void()
 * @method static Builder|UserEmailNotificationSetting whereCommentReply($value)
 * @method static Builder|UserEmailNotificationSetting whereCreatedAt($value)
 * @method static Builder|UserEmailNotificationSetting whereDbBookFinishParse($value)
 * @method static Builder|UserEmailNotificationSetting whereDbCommentReply($value)
 * @method static Builder|UserEmailNotificationSetting whereDbCommentVoteUp($value)
 * @method static Builder|UserEmailNotificationSetting whereDbForumReply($value)
 * @method static Builder|UserEmailNotificationSetting whereDbLike($value)
 * @method static Builder|UserEmailNotificationSetting whereDbWallMessage($value)
 * @method static Builder|UserEmailNotificationSetting whereDbWallReply($value)
 * @method static Builder|UserEmailNotificationSetting whereForumReply($value)
 * @method static Builder|UserEmailNotificationSetting whereNews($value)
 * @method static Builder|UserEmailNotificationSetting wherePrivateMessage($value)
 * @method static Builder|UserEmailNotificationSetting whereUpdatedAt($value)
 * @method static Builder|UserEmailNotificationSetting whereUserId($value)
 * @method static Builder|UserEmailNotificationSetting whereWallMessage($value)
 * @method static Builder|UserEmailNotificationSetting whereWallReply($value)
 * @mixin Eloquent
 */
class UserEmailNotificationSetting extends Model
{

	protected $primaryKey = 'user_id';
	protected $guarded = ['user_id', 'created_at', 'updated_at'];

	protected $attributes =
		[
			'private_message' => true,  // OnMessageComing
			'forum_reply' => true,      // OnForumAnswerComing
			'wall_message' => true,     // OnNewMessageOnWall
			'comment_reply' => true,    // OnCommentAnswerComing
			'wall_reply' => true,        // OnBlogMessageAnswerComing
			'news' => true,             // News

			'db_forum_reply' => true,
			'db_wall_message' => true,
			'db_comment_reply' => true,
			'db_wall_reply' => true,
			'db_book_finish_parse' => true,
			'db_like' => true,
			'db_comment_vote_up' => true
		];

	protected $casts = [
		'private_message' => 'boolean',
		'forum_reply' => 'boolean',
		'wall_message' => 'boolean',
		'comment_reply' => 'boolean',
		'wall_reply' => 'boolean',
		'news' => 'boolean',

		'db_forum_reply' => 'boolean',
		'db_wall_message' => 'boolean',
		'db_comment_reply' => 'boolean',
		'db_wall_reply' => 'boolean',
		'db_book_finish_parse' => 'boolean',
		'db_like' => 'boolean',
		'db_comment_vote_up' => 'boolean'
	];

	protected $fillable = [
		'private_message',
		'forum_reply',
		'comment_reply',
		'wall_message',
		'wall_reply',
		'news',

		'db_forum_reply',
		'db_wall_message',
		'db_comment_reply',
		'db_wall_reply',
		'db_like',
		'db_book_finish_parse',
		'db_comment_vote_up'
	];

	function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}

	public function getFillableAll()
	{
		$array = [];

		foreach ($this->getFillable() as $attribute) {
			$array[$attribute] = $this->attributes[$attribute];
		}

		return $array;
		//return array_diff_key($this->getAttributes(), array_flip($this->getGuarded()));
	}
}
