<?php

namespace App;

use App\Model as Model;
use App\Traits\BBCodeable;
use App\Traits\LatestOldestWithIDTrait;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\Message
 *
 * @property int $id
 * @property bool $is_read
 * @property bool|null $old_recepient_del
 * @property bool|null $old_sender_del
 * @property int|null $recepient_id
 * @property int $create_user_id
 * @property string $text
 * @property int $old_create_time
 * @property bool $old_is_spam
 * @property string|null $bb_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property bool $external_images_downloaded
 * @property int $new Если 1, то сообщение новое (не прочитано), если 0, то прочитано
 * @property bool $old_image_size_defined
 * @property int|null $conversation_id
 * @property string|null $deleted_at_for_created_user
 * @property Carbon|null $user_updated_at
 * @property-read \App\Conversation|null $conversation
 * @property-read \App\User $create_user
 * @property-read \App\User|null $recepient
 * @property-read \App\User|null $sender
 * @property-write mixed $b_b_text
 * @property-read \App\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MessageDelete[] $user_deletetions
 * @method static \Illuminate\Database\Eloquent\Builder|Message joinUserDeletions($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Message latestWithId($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|Message messageNobodyRemoved()
 * @method static \Illuminate\Database\Eloquent\Builder|Message messageNotReaded()
 * @method static \Illuminate\Database\Eloquent\Builder|Message messageReaded()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message notDeletedForUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Message oldestWithId($column = 'created_at')
 * @method static Builder|Message onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message recepientRemove()
 * @method static \Illuminate\Database\Eloquent\Builder|Message reciviedByUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Message sendedByUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Message senderRemove()
 * @method static \Illuminate\Database\Eloquent\Builder|Message void()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereBbText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedAtForCreatedUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereExternalImagesDownloaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereOldCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereOldImageSizeDefined($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereOldIsSpam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereOldRecepientDel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereOldSenderDel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRecepientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUserUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message withDeletedForUser($user)
 * @method static Builder|Message withTrashed()
 * @method static Builder|Message withoutTrashed()
 * @mixin Eloquent
 */
class Message extends Model
{
	use SoftDeletes;
	use UserCreate;
	use LatestOldestWithIDTrait;
	use BBCodeable;

	public $recepient_id = null;

	protected $primaryKey = 'id';

	protected $fillable = [
		'bb_text'
	];

	protected $dates = [
		'user_updated_at'
	];

	const BB_CODE_COLUMN = 'bb_text';
	const HTML_COLUMN = 'text';

	public function scopeVoid($query)
	{
		return $query;
	}

	public function create_user()
	{
		return $this->belongsTo('App\User', 'create_user_id', 'id')
			->any();
	}

	public function conversation()
	{
		return $this->belongsTo('App\Conversation', 'conversation_id', 'id');
	}

	function user_deletetions()
	{
		return $this->hasMany('App\MessageDelete', 'message_id', 'id');
	}

	public function setBBTextAttribute($value)
	{
		$this->setBBCode($value);
		$this->attributes['external_images_downloaded'] = false;
	}

	public function setTextAttribute($value)
	{
		$this->setHtml($value);
		$this->attributes['external_images_downloaded'] = false;
	}

	public function setRecepientIdAttribute($value)
	{
		$this->recepient_id = $value;
	}

	public function isViewed(): bool
	{
		if ($this->id <= $this->recepients_participations()->max('latest_seen_message_id'))
			return true;
		else
			return false;
	}

	public function isViewedByUser(User $user): bool
	{
		if ($user->is($this->create_user))
			return true;

		$participation = $this->getRecepientsParticipations()
			->where('user_id', $user->id)
			->first();

		if (empty($participation))
			return false;

		if ($this->id <= $participation->latest_seen_message_id)
			return true;
		else
			return false;
	}

	public function recepients_participations()
	{
		return $this->conversation
			->participations
			->where('user_id', '!=', $this->create_user_id);
	}

	public function getRecepientsParticipations()
	{
		return $this->recepients_participations();
	}

	public function getFirstRecepientParticipation()
	{
		return $this->getRecepientsParticipations()
			->first();
	}

	public function getSenderParticipation()
	{
		return $this->conversation
			->participations
			->where('user_id', $this->create_user_id)
			->first();
	}

	public function isUpdatedByUser(): bool
	{
		return (bool)$this->user_updated_at;
	}

	public function isDeletedForSender(): bool
	{
		if ($this->deleted_at_for_created_user)
			return true;
		else
			return false;
	}

	public function isDeletedForUser($user): bool
	{
		$user_deletetion = $this->user_deletetions
			->where('user_id', $user->id)
			->first();

		if (!empty($user_deletetion))
			return true;
		else
			return false;
	}

	public function scopeJoinUserDeletions($query, $user)
	{
		if (is_object($user))
			$user = $user->getKey();

		return $query->leftJoin('message_deletions', function ($join) use ($user) {
			$join->on('messages.id', '=', 'message_deletions.message_id')
				->where('message_deletions.user_id', '=', $user);
		})->selectRaw('messages.*, message_deletions.deleted_at as message_deletions_deleted_at');
	}

	public function scopeNotDeletedForUser($query, $user)
	{
		if ($user instanceof User)
			$user = $user->getKey();

		// этот вариант быстрее работает, чем второй
		return $query->joinUserDeletions($user)
			->whereNull('message_deletions.deleted_at');
		/*
				return $query->whereDoesntHave('user_deletetions', function (Builder $query) use ($user) {
					$query->where('user_id', $user);
				});*/
	}

	public function scopeWithDeletedForUser($query, $user)
	{
		if (is_object($user))
			$user = $user->getKey();

		return $query->joinUserDeletions($user);
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

	public function deleteForUser(User $user)
	{
		DB::transaction(function () use ($user) {

			$this->user_deletetions()
				->firstOrCreate(
					['user_id' => $user->id],
					['deleted_at' => now()]
				);

			if (!$this->isViewed()) {

				$this->delete();

				foreach ($this->conversation->participations as $participation) {

					$participation->updateLatestMessage();

					if (!$this->isUserCreator($participation->user)) {
						$participation->new_messages_count--;
						$participation->user->flushCacheNewMessages();
					}

					if ($user->is($participation->user)) {
						if ($this->id > $participation->latest_seen_message_id)
							$participation->latest_seen_message_id = $this->id;
					}

					$participation->save();
				}

			} else {

				$participation = $this->conversation
					->participations
					->where('user_id', $user->id)
					->first();

				$participation->updateLatestMessage();
				$participation->save();
			}
		});
	}

	public function restoreForUser(User $user)
	{
		DB::transaction(function () use ($user) {

			$this->user_deletetions()
				->where('user_id', $user->id)
				->delete();

			if ($this->trashed())
				$this->restore();

			if (!$this->isViewed()) {
				foreach ($this->conversation->participations as $participation) {

					$participation->updateLatestMessage();

					if (!$this->isUserCreator($participation->user)) {
						$participation->updateNewMessagesCount();
						$participation->user->flushCacheNewMessages();
					}

					if (!empty($participation->latest_message_id)) {

						if ($user->is($participation->user))
							$participation->noNewMessages();
					}

					$participation->save();
				}

			} else {

				foreach ($this->conversation->participations as $participation) {

					$participation->updateLatestMessage();

					if ($participation->user->is($user))
						$participation->noNewMessages();

					$participation->save();
				}
			}
		});
	}

	public function trashed(): bool
	{
		return !is_null($this->{$this->getDeletedAtColumn()}) or !empty($this->message_deletions_deleted_at);
	}

	public function getPreviewText($length = 100)
	{
		$text = str_replace("\r\n", '', $this->text);
		$text = str_replace("\n", '', $text);
		$text = preg_replace('/\<blockquote\ class\=\"bb\ bb\_quote\"\>([\\s\\S]*?)\<\/blockquote\>/iu', ' ', $text);

		$text = preg_replace('/\<img(.*)\>/iu', '(' . __('message.image') . ')', $text);
		$text = preg_replace('/\<iframe\ ([\\s\\S]*?)>([\\s\\S]*?)\<\/iframe\>/iu', '(' . __('message.video') . ')', $text);

		return trim(html_entity_decode(mb_substr(strip_tags($text), 0, $length)));
	}
}
