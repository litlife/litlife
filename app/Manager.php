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
 * App\Manager
 *
 * @property int $id
 * @property int $create_user_id
 * @property int $user_id
 * @property string $character
 * @property int $manageable_id
 * @property int $add_time
 * @property int $hide
 * @property int $hide_time
 * @property int $hide_user
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $accepted_at
 * @property int|null $check_user_id
 * @property string $manageable_type
 * @property string|null $rejected_at
 * @property int|null $status
 * @property Carbon|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property string|null $sent_for_review_at
 * @property bool $can_sale Может продавать книги или нет
 * @property int|null $profit_percent Процент от прибыли, который получает автор
 * @property bool $disable_editing_for_co_author Запрет редактирования для соавторов
 * @property-read \App\User|null $check_user
 * @property-read \App\User $create_user
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $manageable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorSaleRequest[] $saleRequests
 * @property-read \App\User|null $status_changed_user
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Manager accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager authors()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager checked()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager editors()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager onlyChecked()
 * @method static Builder|Manager onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Manager orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager private ()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager query()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager sentOnReviewAndManageableNotPrivateAndNotOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager unchecked()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCanSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCharacter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCheckUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereDisableEditingForCoAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereHideTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereHideUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereManageableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereManageableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereProfitPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereSentForReviewAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereUserId($value)
 * @method static Builder|Manager withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Manager withoutCheckedScope()
 * @method static Builder|Manager withoutTrashed()
 * @mixin Eloquent
 */
class Manager extends Model
{
	use SoftDeletes;
	use UserCreate;
	use CheckedItems;

	protected $attributes =
		[
			'status' => StatusEnum::OnReview
		];

	protected $fillable = [
		'user_id'
	];

	protected $dates = [
		'status_changed_at'
	];

	public static function boot()
	{
		static::Creating(function ($model) {
			$model->autoAssociateAuthUser();
		});

		parent::boot();
	}

	static function getCachedOnModerationCount()
	{
		return Cache::tags([CacheTags::ManagersOnModerationCount])->remember('count', 3600, function () {
			return self::sentOnReviewAndManageableNotPrivateAndNotOnReview()->count();
		});
	}

	public function scopeSentOnReviewAndManageableNotPrivateAndNotOnReview($query)
	{
		return $query->sentOnReview()
			->whereHasMorph('manageable', ['App\Author'], function (\Illuminate\Database\Eloquent\Builder $query) {
				$query->whereStatusNot('Private')
					->whereStatusNot('OnReview');
			});
	}

	static function flushCachedOnModerationCount()
	{
		Cache::tags([CacheTags::ManagersOnModerationCount])->pull('count');
	}

	/**
	 * Get all of the owning commentable models.
	 */
	public function manageable()
	{
		return $this->morphTo();
	}

	public function saleRequests()
	{
		return $this->hasMany('App\AuthorSaleRequest', 'manager_id');
	}

	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id');
	}

	public function check_user()
	{
		return $this->hasOne('App\User');
	}

	public function getProfitPercentAttribute($value)
	{
		if (empty($value))
			return 100 - config('litlife.comission');

		return $value;
	}

	public function isAuthorCharacter()
	{
		return $this->character == 'author';
	}

	public function isEditorCharacter()
	{
		return $this->character == 'editor';
	}

	public function scopeAuthors($query)
	{
		return $query->where('character', 'author');
	}

	public function scopeEditors($query)
	{
		return $query->where('character', 'editor');
	}
}
