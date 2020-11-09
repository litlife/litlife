<?php

namespace App;

use App\Enums\CacheTags;
use App\Enums\StatusEnum;
use App\Model as Model;
use App\Traits\CheckedItems;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Complain
 *
 * @property int $id
 * @property string $complainable_type
 * @property int $complainable_id
 * @property int $create_user_id
 * @property string $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $status
 * @property Carbon|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $complainable
 * @property-read \App\User $create_user
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \App\User|null $status_changed_user
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Complain accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain checked()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain onlyChecked()
 * @method static Builder|Complain onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Complain orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain private ()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain query()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereComplainableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereComplainableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereUpdatedAt($value)
 * @method static Builder|Complain withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Complain withoutCheckedScope()
 * @method static Builder|Complain withoutTrashed()
 * @mixin Eloquent
 */
class Complain extends Model
{
	use SoftDeletes;
	use UserCreate;
	use CheckedItems;

	public $dates = ['status_changed_at'];
	protected $attributes =
		[
			'status' => StatusEnum::OnReview
		];
	protected $table = 'complaints';
	protected $fillable = [
		'text'
	];

	static function getCachedOnModerationCount()
	{
		return Cache::tags([CacheTags::ComplainsOnModerationCount])->remember('count', 3600, function () {
			return self::sentOnReview()->count();
		});
	}

	static function flushCachedOnModerationCount()
	{
		Cache::tags([CacheTags::ComplainsOnModerationCount])->pull('count');
	}

	public function complainable()
	{
		return $this->morphTo()->any();
	}

	function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id');
	}

	public function setTextAttribute($text)
	{
		$this->attributes['text'] = $text;
	}

	public function getComplainableName()
	{
		if (empty($this->complainable))
			return null;

		$name = get_class($this->complainable);

		if (preg_match('/^App\\\(.+)/iu', $name, $matches))
			return mb_strtolower($matches[1]);
		else
			return $name;
	}
}
